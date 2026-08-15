<?php

namespace App\Services;

use App\Models\School;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use DateTimeZone;
use InvalidArgumentException;
use Throwable;

class TenantDateTime
{
    public const INPUT_FORMAT = 'Y-m-d\\TH:i';

    /**
     * Convert a tenant-local form value into an immutable UTC instant.
     */
    public function toUtc(string $localDateTime, School|string|null $tenant): CarbonImmutable
    {
        $timezone = $this->timezoneFor($tenant);

        try {
            $dateTime = CarbonImmutable::createFromFormat(
                '!'.self::INPUT_FORMAT,
                $localDateTime,
                $timezone,
            );
        } catch (Throwable $exception) {
            throw new InvalidArgumentException('The local date and time is invalid.', previous: $exception);
        }

        // The round-trip check also rejects local wall times skipped by a DST change.
        if (! $dateTime || $dateTime->format(self::INPUT_FORMAT) !== $localDateTime) {
            throw new InvalidArgumentException('The local date and time does not exist in the selected timezone.');
        }

        $matchingUtcTimestamps = $this->matchingUtcTimestamps($localDateTime, $timezone);
        if (count($matchingUtcTimestamps) > 1) {
            throw new InvalidArgumentException('The local date and time is ambiguous in the selected timezone.');
        }

        return $matchingUtcTimestamps
            ? CarbonImmutable::createFromTimestampUTC($matchingUtcTimestamps[0])->utc()
            : $dateTime->utc();
    }

    /**
     * Represent a UTC instant in a tenant's timezone without mutating the input.
     */
    public function forTenant(CarbonInterface $dateTime, School|string|null $tenant): CarbonImmutable
    {
        return CarbonImmutable::instance($dateTime)
            ->utc()
            ->setTimezone($this->timezoneFor($tenant));
    }

    public function toInputValue(CarbonInterface $dateTime, School|string|null $tenant): string
    {
        return $this->forTenant($dateTime, $tenant)->format(self::INPUT_FORMAT);
    }

    public function format(
        CarbonInterface $dateTime,
        School|string|null $tenant,
        string $format = 'd M Y, H:i',
    ): string {
        return $this->forTenant($dateTime, $tenant)->format($format);
    }

    public function timezoneFor(School|string|null $tenant): string
    {
        $timezone = $tenant instanceof School ? $tenant->timezone : $tenant;
        $timezone = $timezone ?: config('tenancy.default_timezone', 'Asia/Jakarta');

        if (! in_array($timezone, DateTimeZone::listIdentifiers(DateTimeZone::ALL), true)) {
            return 'UTC';
        }

        return $timezone;
    }

    /**
     * Return IANA timezone choices ordered by their current UTC offset.
     *
     * @return array<string, string>
     */
    public function timezoneOptions(): array
    {
        $now = CarbonImmutable::now('UTC');
        $options = [];

        foreach (DateTimeZone::listIdentifiers(DateTimeZone::ALL) as $identifier) {
            $zone = new DateTimeZone($identifier);
            $offset = $zone->getOffset($now);
            $absoluteOffset = abs($offset);
            $sign = $offset < 0 ? '-' : '+';
            $hours = intdiv($absoluteOffset, 3600);
            $minutes = intdiv($absoluteOffset % 3600, 60);

            $options[] = [
                'identifier' => $identifier,
                'offset' => $offset,
                'label' => sprintf('(UTC%s%02d:%02d) %s', $sign, $hours, $minutes, $identifier),
            ];
        }

        usort(
            $options,
            fn (array $left, array $right): int => [$left['offset'], $left['identifier']]
                <=> [$right['offset'], $right['identifier']],
        );

        return array_column($options, 'label', 'identifier');
    }

    /**
     * Find UTC instants that render as the supplied local wall time. A repeated
     * DST hour has two matches and cannot be disambiguated by datetime-local.
     *
     * @return list<int>
     */
    private function matchingUtcTimestamps(string $localDateTime, string $timezone): array
    {
        $naiveLocal = CarbonImmutable::createFromFormat(
            '!'.self::INPUT_FORMAT,
            $localDateTime,
            'UTC',
        );

        if (! $naiveLocal) {
            return [];
        }

        $zone = new DateTimeZone($timezone);
        $transitions = $zone->getTransitions(
            $naiveLocal->getTimestamp() - 172800,
            $naiveLocal->getTimestamp() + 172800,
        );

        if ($transitions === false) {
            return [];
        }

        $matches = [];
        foreach (array_unique(array_column($transitions, 'offset')) as $offset) {
            $candidateTimestamp = $naiveLocal->getTimestamp() - $offset;
            $candidate = CarbonImmutable::createFromTimestampUTC($candidateTimestamp)->setTimezone($timezone);

            if ($candidate->format(self::INPUT_FORMAT) === $localDateTime) {
                $matches[$candidateTimestamp] = true;
            }
        }

        return array_map('intval', array_keys($matches));
    }
}
