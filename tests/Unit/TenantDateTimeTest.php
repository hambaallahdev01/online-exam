<?php

namespace Tests\Unit;

use App\Services\TenantDateTime;
use Carbon\CarbonImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TenantDateTimeTest extends TestCase
{
    #[DataProvider('timezoneConversions')]
    public function test_it_converts_tenant_local_time_to_utc(
        string $timezone,
        string $localDateTime,
        string $expectedUtc,
    ): void {
        $actual = app(TenantDateTime::class)->toUtc($localDateTime, $timezone);

        $this->assertSame($expectedUtc, $actual->format('Y-m-d H:i:s T'));
    }

    public static function timezoneConversions(): array
    {
        return [
            'WIB' => ['Asia/Jakarta', '2026-08-16T09:00', '2026-08-16 02:00:00 UTC'],
            'WITA' => ['Asia/Makassar', '2026-08-16T09:00', '2026-08-16 01:00:00 UTC'],
            'WIT' => ['Asia/Jayapura', '2026-08-16T09:00', '2026-08-16 00:00:00 UTC'],
        ];
    }

    public function test_it_formats_a_utc_instant_in_the_tenant_timezone(): void
    {
        $utc = CarbonImmutable::parse('2026-08-16 02:00:00', 'UTC');

        $actual = app(TenantDateTime::class)->toInputValue($utc, 'Asia/Jakarta');

        $this->assertSame('2026-08-16T09:00', $actual);
    }

    public function test_it_rejects_a_local_time_skipped_by_dst(): void
    {
        $this->expectException(InvalidArgumentException::class);

        app(TenantDateTime::class)->toUtc('2026-03-08T02:30', 'America/New_York');
    }

    public function test_it_rejects_a_local_time_repeated_by_dst(): void
    {
        $this->expectException(InvalidArgumentException::class);

        app(TenantDateTime::class)->toUtc('2026-11-01T01:30', 'America/New_York');
    }
}
