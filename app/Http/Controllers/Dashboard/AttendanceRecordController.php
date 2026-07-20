<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Services\AttendanceService;
use Illuminate\Http\Request;

class AttendanceRecordController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function index(Request $request)
    {
        $month = $request->month ?? date('m');
        $year = $request->year ?? date('Y');
        $employeeId = $request->employee_id ?? null;

        $query = Employee::with(['attendanceRecords' => function($q) use ($month, $year) {
            $q->whereYear('attendance_date', $year)->whereMonth('attendance_date', $month);
        }]);

        if ($employeeId) {
            $query->where('id', $employeeId);
        }

        $employees = $query->get();

        // Get days in selected month
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $days = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $days[] = $i;
        }

        $allEmployees = Employee::all();

        return view('dashboard.attendance-records.index', compact('employees', 'allEmployees', 'month', 'year', 'days'));
    }

    public function show(AttendanceRecord $attendanceRecord)
    {
        $attendanceRecord->load('employee', 'attendanceAdjustments');
        return view('dashboard.attendance-records.show', compact('attendanceRecord'));
    }

    public function edit(AttendanceRecord $attendanceRecord)
    {
        return view('dashboard.attendance-records.edit', compact('attendanceRecord'));
    }

    public function update(Request $request, AttendanceRecord $attendanceRecord)
    {
        $validated = $request->validate([
            'check_in' => 'nullable|date_format:H:i:s',
            'check_out' => 'nullable|date_format:H:i:s',
            'notes' => 'nullable|string',
            'attendance_status' => 'nullable|in:present,late,leave,sick,absent,holiday,need_review',
        ]);

        $attendanceRecord->update($validated);

        // Recalculate attendance data if check in/out changed
        if ($request->has('check_in') || $request->has('check_out')) {
            $calculatedData = $this->attendanceService->calculateAttendanceData($attendanceRecord);
            $attendanceRecord->update($calculatedData);
        }

        return redirect()->route('attendance-records.show', $attendanceRecord)->with('success', 'Attendance record updated');
    }
}
