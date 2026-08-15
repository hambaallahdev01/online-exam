<?php

namespace App\Services;

use App\Models\ExamResult;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class ExamDraftStore
{
    private static bool $cacheFailureLogged = false;

    /**
     * @return array<string, mixed>
     */
    public function answersFor(ExamResult $result): array
    {
        if ($this->usesDatabase()) {
            return $result->answers_json ?? [];
        }

        try {
            $draft = Cache::store($this->store())->get($this->cacheKey($result));

            return is_array($draft) && is_array($draft['answers'] ?? null)
                ? $draft['answers']
                : ($result->answers_json ?? []);
        } catch (Throwable $exception) {
            $this->logCacheFailure($exception);

            return $result->answers_json ?? [];
        }
    }

    /**
     * Save quickly to the configured cache and periodically checkpoint to SQL.
     * Cache failures always fall back to an immediate database update.
     *
     * @param  array<string, mixed>  $answers
     */
    public function save(ExamResult $result, array $answers, int $timeRemaining): void
    {
        if ($this->usesDatabase()) {
            $this->persistIfChanged($result, $answers, $timeRemaining);

            return;
        }

        try {
            $cache = Cache::store($this->store());
            $key = $this->cacheKey($result);
            $existing = $cache->get($key);
            $lastPersistedAt = is_array($existing) ? (int) ($existing['last_persisted_at'] ?? 0) : 0;
            $now = now()->getTimestamp();
            $draft = [
                'answers' => $answers,
                'time_remaining_seconds' => $timeRemaining,
                'saved_at' => $now,
                'last_persisted_at' => $lastPersistedAt,
            ];

            $cache->put($key, $draft, $this->ttlSeconds($timeRemaining));

            $checkpointSeconds = max(1, (int) config('exam.drafts.database_checkpoint_seconds', 30));
            $firstChangedDraft = $lastPersistedAt === 0 && ($result->answers_json ?? []) !== $answers;
            $checkpointDue = $lastPersistedAt > 0 && ($now - $lastPersistedAt) >= $checkpointSeconds;

            if ($firstChangedDraft || $checkpointDue) {
                $this->persistIfChanged($result, $answers, $timeRemaining);
                $draft['last_persisted_at'] = $now;
                $cache->put($key, $draft, $this->ttlSeconds($timeRemaining));
            }
        } catch (Throwable $exception) {
            $this->logCacheFailure($exception);
            $this->persistIfChanged($result, $answers, $timeRemaining);
        }
    }

    public function forget(ExamResult $result): void
    {
        if ($this->usesDatabase()) {
            return;
        }

        try {
            Cache::store($this->store())->forget($this->cacheKey($result));
        } catch (Throwable $exception) {
            $this->logCacheFailure($exception);
        }
    }

    /**
     * @param  array<string, mixed>  $answers
     */
    private function persist(ExamResult $result, array $answers, int $timeRemaining): void
    {
        $result->update([
            'answers_json' => $answers,
            'time_remaining_seconds' => $timeRemaining,
        ]);
    }

    /**
     * Avoid a SQL write for a periodic browser checkpoint with no new answers.
     *
     * @param  array<string, mixed>  $answers
     */
    private function persistIfChanged(ExamResult $result, array $answers, int $timeRemaining): void
    {
        if (($result->answers_json ?? []) === $answers) {
            return;
        }

        $this->persist($result, $answers, $timeRemaining);
    }

    private function usesDatabase(): bool
    {
        return $this->store() === 'database';
    }

    private function store(): string
    {
        return (string) config('exam.drafts.store', 'database');
    }

    private function cacheKey(ExamResult $result): string
    {
        return "exam:draft:v1:result:{$result->id}";
    }

    private function ttlSeconds(int $timeRemaining): int
    {
        $configuredTtl = max(60, (int) config('exam.drafts.ttl_seconds', 86400));

        return max(60, min($configuredTtl, max(0, $timeRemaining) + 3600));
    }

    private function logCacheFailure(Throwable $exception): void
    {
        if (self::$cacheFailureLogged) {
            return;
        }

        self::$cacheFailureLogged = true;
        Log::warning('Exam draft cache unavailable; saving directly to the database.', [
            'store' => $this->store(),
            'exception' => $exception::class,
            'message' => $exception->getMessage(),
        ]);
    }
}
