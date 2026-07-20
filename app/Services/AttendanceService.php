<?php

namespace App\Services;

use App\Models\AttendanceRecord;

class AttendanceService
{
    const WORK_START_TIME = '08:00:00';
    const WORK_END_TIME = '17:00:00';

    public function calculateAttendanceData(AttendanceRecord $record): array
    {
        $workHours = 0;
        $lateMinutes = 0;
        $overtimeMinutes = 0;
        $status = 'need_review';

        // Calculate work hours
        if ($record->check_in && $record->check_out) {
            $checkIn = strtotime($record->check_in);
            $checkOut = strtotime($record->check_out);
            $diffSeconds = $checkOut - $checkIn;
            $workHours = round($diffSeconds / 3600, 2);
        }

        // Calculate late minutes
        if ($record->check_in) {
            $checkIn = strtotime($record->check_in);
            $workStart = strtotime(self::WORK_START_TIME);
            if ($checkIn > $workStart) {
                $lateMinutes = (int)round(($checkIn - $workStart) / 60);
            }
        }

        // Calculate overtime minutes
        if ($record->check_out) {
            $checkOut = strtotime($record->check_out);
            $workEnd = strtotime(self::WORK_END_TIME);
            if ($checkOut > $workEnd) {
                $overtimeMinutes = (int)round(($checkOut - $workEnd) / 60);
            }
        }

        // Determine status
        if (!$record->check_in && !$record->check_out) {
            $status = 'absent';
        } elseif (!$record->check_in || !$record->check_out) {
            $status = 'need_review';
        } elseif ($lateMinutes > 0) {
            $status = 'late';
        } else {
            $status = 'present';
        }

        return [
            'work_hours' => $workHours,
            'late_minutes' => $lateMinutes,
            'overtime_minutes' => $overtimeMinutes,
            'attendance_status' => $status,
        ];
    }
}
