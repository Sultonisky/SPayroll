<?php

namespace Tests\Unit;

use App\Models\AttendanceRecord;
use App\Services\AttendanceService;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for AttendanceService.
 * Pure unit test — no DB needed, uses plain model instances.
 */
class AttendanceServiceTest extends TestCase
{
    private AttendanceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AttendanceService;
    }

    private function makeRecord(?string $checkIn, ?string $checkOut): AttendanceRecord
    {
        $record = new AttendanceRecord;
        $record->check_in = $checkIn;
        $record->check_out = $checkOut;

        return $record;
    }

    // -----------------------------------------------------------------------
    // Status detection
    // -----------------------------------------------------------------------

    public function test_absent_when_both_times_null(): void
    {
        $result = $this->service->calculateAttendanceData($this->makeRecord(null, null));

        $this->assertSame('absent', $result['attendance_status']);
        $this->assertEquals(0, $result['work_hours']);
        $this->assertSame(0, $result['late_minutes']);
        $this->assertSame(0, $result['overtime_minutes']);
    }

    public function test_need_review_when_only_check_in(): void
    {
        $result = $this->service->calculateAttendanceData($this->makeRecord('08:00:00', null));

        $this->assertSame('need_review', $result['attendance_status']);
    }

    public function test_need_review_when_only_check_out(): void
    {
        $result = $this->service->calculateAttendanceData($this->makeRecord(null, '17:00:00'));

        $this->assertSame('need_review', $result['attendance_status']);
    }

    public function test_present_on_time(): void
    {
        $result = $this->service->calculateAttendanceData($this->makeRecord('08:00:00', '17:00:00'));

        $this->assertSame('present', $result['attendance_status']);
        $this->assertSame(0, $result['late_minutes']);
    }

    public function test_late_when_arriving_after_start(): void
    {
        $result = $this->service->calculateAttendanceData($this->makeRecord('09:30:00', '17:00:00'));

        $this->assertSame('late', $result['attendance_status']);
        $this->assertSame(90, $result['late_minutes']);
    }

    // -----------------------------------------------------------------------
    // Work hours calculation
    // -----------------------------------------------------------------------

    public function test_work_hours_calculated_correctly(): void
    {
        $result = $this->service->calculateAttendanceData($this->makeRecord('08:00:00', '17:00:00'));

        $this->assertSame(9.0, $result['work_hours']);
    }

    public function test_work_hours_zero_when_times_missing(): void
    {
        $result = $this->service->calculateAttendanceData($this->makeRecord(null, null));

        $this->assertEquals(0, $result['work_hours']);
    }

    public function test_work_hours_rounded_to_two_decimals(): void
    {
        // 8:07 → 16:52 = 8h 45m = 8.75h
        $result = $this->service->calculateAttendanceData($this->makeRecord('08:07:00', '16:52:00'));

        $this->assertSame(round((strtotime('16:52:00') - strtotime('08:07:00')) / 3600, 2), $result['work_hours']);
    }

    // -----------------------------------------------------------------------
    // Late minutes calculation
    // -----------------------------------------------------------------------

    public function test_no_late_minutes_for_on_time_arrival(): void
    {
        $result = $this->service->calculateAttendanceData($this->makeRecord('08:00:00', '17:00:00'));

        $this->assertSame(0, $result['late_minutes']);
    }

    public function test_no_late_minutes_for_early_arrival(): void
    {
        $result = $this->service->calculateAttendanceData($this->makeRecord('07:45:00', '17:00:00'));

        $this->assertSame(0, $result['late_minutes']);
    }

    public function test_late_minutes_calculated_for_late_arrival(): void
    {
        // 10:00 is 120 minutes after 08:00
        $result = $this->service->calculateAttendanceData($this->makeRecord('10:00:00', '17:00:00'));

        $this->assertSame(120, $result['late_minutes']);
    }

    public function test_late_minutes_zero_when_no_check_in(): void
    {
        $result = $this->service->calculateAttendanceData($this->makeRecord(null, '17:00:00'));

        $this->assertSame(0, $result['late_minutes']);
    }

    // -----------------------------------------------------------------------
    // Overtime minutes calculation
    // -----------------------------------------------------------------------

    public function test_no_overtime_leaving_on_time(): void
    {
        $result = $this->service->calculateAttendanceData($this->makeRecord('08:00:00', '17:00:00'));

        $this->assertSame(0, $result['overtime_minutes']);
    }

    public function test_overtime_calculated_correctly(): void
    {
        // 18:00 is 60 minutes after 17:00
        $result = $this->service->calculateAttendanceData($this->makeRecord('08:00:00', '18:00:00'));

        $this->assertSame(60, $result['overtime_minutes']);
    }

    public function test_overtime_zero_when_leaving_early(): void
    {
        $result = $this->service->calculateAttendanceData($this->makeRecord('08:00:00', '16:00:00'));

        $this->assertSame(0, $result['overtime_minutes']);
    }

    public function test_overtime_zero_when_no_check_out(): void
    {
        $result = $this->service->calculateAttendanceData($this->makeRecord('08:00:00', null));

        $this->assertSame(0, $result['overtime_minutes']);
    }

    // -----------------------------------------------------------------------
    // Return shape
    // -----------------------------------------------------------------------

    public function test_returns_all_required_keys(): void
    {
        $result = $this->service->calculateAttendanceData($this->makeRecord('08:00:00', '17:00:00'));

        $this->assertArrayHasKey('work_hours', $result);
        $this->assertArrayHasKey('late_minutes', $result);
        $this->assertArrayHasKey('overtime_minutes', $result);
        $this->assertArrayHasKey('attendance_status', $result);
    }
}
