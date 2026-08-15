<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\QuestionGroup;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class ExamQuestionPayload
{
    private static bool $cacheFailureLogged = false;

    /**
     * Return the student-safe question payload, with a stable order per result.
     *
     * @return array<int, array<string, mixed>>
     */
    public function forResult(Exam $exam, ExamResult $result): array
    {
        $group = $exam->questionGroup()->firstOrFail();
        $questions = $this->forGroup($group);

        if ($exam->randomize_questions) {
            $questions = $this->stableShuffle(
                $questions,
                "exam:{$exam->id}:result:{$result->id}:questions",
                fn (array $question): string => (string) $question['id'],
            );
        }

        if ($exam->randomize_options) {
            foreach ($questions as &$question) {
                if (
                    in_array($question['type'], ['single_choice', 'multiple_choice'], true)
                    && is_array($question['options'])
                ) {
                    $question['options'] = $this->stableShuffle(
                        $question['options'],
                        "exam:{$exam->id}:result:{$result->id}:question:{$question['id']}:options",
                        fn (mixed $option, int $index): string => is_array($option)
                            ? (string) ($option['id'] ?? $index)
                            : (string) $index,
                    );
                }
            }
            unset($question);
        }

        return $questions;
    }

    /**
     * @return array<string, true>
     */
    public function allowedQuestionIds(Exam $exam): array
    {
        $group = $exam->questionGroup()->firstOrFail();

        return collect($this->forGroup($group))
            ->mapWithKeys(fn (array $question): array => [(string) $question['id'] => true])
            ->all();
    }

    public function forgetQuestionGroup(int $questionGroupId): void
    {
        if (! config('exam.payload_cache.enabled')) {
            return;
        }

        try {
            Cache::store($this->cacheStore())->forget($this->cacheKey($questionGroupId));
        } catch (Throwable $exception) {
            $this->logCacheFailure($exception);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function forGroup(QuestionGroup $group): array
    {
        if (! config('exam.payload_cache.enabled')) {
            return $this->loadGroup($group);
        }

        try {
            return Cache::store($this->cacheStore())->remember(
                $this->cacheKey($group->id),
                max(1, (int) config('exam.payload_cache.ttl_seconds', 3600)),
                fn (): array => $this->loadGroup($group),
            );
        } catch (Throwable $exception) {
            $this->logCacheFailure($exception);

            return $this->loadGroup($group);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function loadGroup(QuestionGroup $group): array
    {
        return $group->questions()
            ->orderBy('id')
            ->get(['id', 'question_type', 'content', 'options_json', 'weight'])
            ->map(fn ($question): array => [
                'id' => $question->id,
                'type' => $question->question_type,
                'content' => HtmlSanitizerService::sanitize($question->content),
                'options' => $this->sanitizeOptions($question->options_json, $question->question_type),
                'weight' => $question->weight,
            ])
            ->all();
    }

    private function sanitizeOptions(mixed $options, string $questionType): mixed
    {
        if (! is_array($options)) {
            return $options;
        }

        if (in_array($questionType, ['single_choice', 'multiple_choice'], true)) {
            return array_map(function ($option) {
                if (! is_array($option)) {
                    return HtmlSanitizerService::sanitize((string) $option);
                }

                $option['id'] = mb_substr((string) ($option['id'] ?? ''), 0, 20);
                $option['text'] = HtmlSanitizerService::sanitize((string) ($option['text'] ?? ''));

                return $option;
            }, $options);
        }

        return $this->plainTextOptions($options);
    }

    private function plainTextOptions(array $options): array
    {
        foreach ($options as $key => $value) {
            if (is_array($value)) {
                $options[$key] = $this->plainTextOptions($value);
            } elseif (is_string($value)) {
                $options[$key] = mb_substr(trim(strip_tags($value)), 0, 1000);
            }
        }

        return $options;
    }

    /**
     * @template T
     *
     * @param  array<int, T>  $items
     * @param  callable(T, int): string  $identity
     * @return array<int, T>
     */
    private function stableShuffle(array $items, string $seed, callable $identity): array
    {
        $decorated = [];

        foreach (array_values($items) as $index => $item) {
            $identityValue = $identity($item, $index);
            $decorated[] = [
                'sort' => hash('sha256', $seed.':'.$identityValue),
                'index' => $index,
                'item' => $item,
            ];
        }

        usort($decorated, fn (array $left, array $right): int => [$left['sort'], $left['index']] <=> [$right['sort'], $right['index']]);

        return array_column($decorated, 'item');
    }

    private function cacheStore(): string
    {
        return (string) config('exam.payload_cache.store', config('cache.default', 'file'));
    }

    private function cacheKey(int $questionGroupId): string
    {
        return "exam:question-payload:v1:group:{$questionGroupId}";
    }

    private function logCacheFailure(Throwable $exception): void
    {
        if (self::$cacheFailureLogged) {
            return;
        }

        self::$cacheFailureLogged = true;
        Log::warning('Exam question cache unavailable; using the database fallback.', [
            'exception' => $exception::class,
            'message' => $exception->getMessage(),
        ]);
    }
}
