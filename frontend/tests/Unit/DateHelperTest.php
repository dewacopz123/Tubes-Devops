<?php

namespace Tests\Unit;

use Tests\TestCase;

class DateHelperTest extends TestCase
{
    public function test_format_date_time_handles_valid_date(): void
    {
        $result = formatDateTime('2026-05-15 14:30:00');
        $this->assertStringContainsString('2026', $result);
        $this->assertStringContainsString('14:30 WIB', $result);
    }

    public function test_format_date_time_handles_empty_or_null(): void
    {
        $this->assertEquals('-', formatDateTime(null));
        $this->assertEquals('-', formatDateTime(''));
    }

    public function test_format_date_handles_valid_date(): void
    {
        $result = formatDate('2026-11-20');
        $this->assertStringContainsString('2026', $result);
        $this->assertStringContainsString('20', $result);
    }

    public function test_format_date_handles_empty(): void
    {
        $this->assertEquals('-', formatDate(null));
    }

    public function test_format_time_handles_valid_time(): void
    {
        $result = formatTime('2026-01-01 08:45:00');
        $this->assertEquals('08:45 WIB', $result);
    }

    public function test_format_time_handles_null(): void
    {
        $this->assertEquals('-', formatTime(null));
    }

    public function test_format_date_short(): void
    {
        $result = formatDateShort('2026-05-15');
        $this->assertStringContainsString('2026', $result);
        $this->assertEquals('-', formatDateShort(null));
    }

    public function test_format_date_time_short(): void
    {
        $result = formatDateTimeShort('2026-05-15 10:15:00');
        $this->assertStringContainsString('2026', $result);
        $this->assertStringContainsString('10:15', $result);
        $this->assertEquals('-', formatDateTimeShort(null));
    }

    public function test_format_relative(): void
    {
        $result = formatRelative('2020-01-01');
        $this->assertIsString($result);
        $this->assertEquals('-', formatRelative(null));
    }
}
