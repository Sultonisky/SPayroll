<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AttendanceAdjustment;
use App\Models\AttendanceRecord;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceAdjustmentController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function index()
    {
        $adjustments = AttendanceAdjustment::with('attendanceRecord.employee')->orderBy('created_at', 'desc')->get();
        return view('dashboard.attendance-adjustments.index', compact('adjustments'));
    }

    public function create(AttendanceRecord $attendanceRecord)
    {
        return view('dashboard.attendance-adjustments.create', compact('attendanceRecord'));
    }

    public function store(Request $request, AttendanceRecord $attendanceRecord)
    {
        $validated = $request->validate([
            'new_check_in' => 'nullable|date_format:H:i:s',
            'new_check_out' => 'nullable|date_format:H:i:s',
            'reason' => 'required|string',
        ]);

        AttendanceAdjustment::create([
            'attendance_record_id' => $attendanceRecord->id,
            'old_check_in' => $attendanceRecord->check_in,
            'new_check_in' => $validated['new_check_in'] ?? $attendanceRecord->check_in,
            'old_check_out' => $attendanceRecord->check_out,
            'new_check_out' => $validated['new_check_out'] ?? $attendanceRecord->check_out,
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return redirect()->route('attendance-records.show', $attendanceRecord)->with('success', 'Adjustment request submitted');
    }

    public function show(AttendanceAdjustment $attendanceAdjustment)
    {
        $attendanceAdjustment->load('attendanceRecord.employee');
        return view('dashboard.attendance-adjustments.show', compact('attendanceAdjustment'));
    }

    public function approve(AttendanceAdjustment $attendanceAdjustment)
    {
        $user = Auth::user();

        $attendanceAdjustment->update([
            'status' => 'approved',
            'approved_by' => $user->id,
        ]);

        // Update the attendance record
        $attendanceRecord = $attendanceAdjustment->attendanceRecord;
        $attendanceRecord->update([
            'check_in' => $attendanceAdjustment->new_check_in,
            'check_out' => $attendanceAdjustment->new_check_out,
        ]);

        // Recalculate attendance data
        $calculatedData = $this->attendanceService->calculateAttendanceData($attendanceRecord);
        $attendanceRecord->update($calculatedData);

        return back()->with('success', 'Adjustment approved');
    }

    public function reject(AttendanceAdjustment $attendanceAdjustment)
    {
        $user = Auth::user();

        $attendanceAdjustment->update([
            'status' => 'rejected',
            'approved_by' => $user->id,
        ]);

        return back()->with('success', 'Adjustment rejected');
    }
}
