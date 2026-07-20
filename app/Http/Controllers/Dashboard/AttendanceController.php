<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Imports\AttendanceImport;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', Attendance::class);
        $attendances = Attendance::select('id', 'employee_id', 'year', 'month', 'present', 'created_at')
            ->with(['employee'])
            ->orderBy('employee_id')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $attendances = $attendances->groupBy('employee_id');

        return view('dashboard.attendances.index', compact('attendances'));
    }

    /**
     * Display a listing of deleted resources.
     */
    public function trash()
    {
        Gate::authorize('viewAny', Attendance::class);
        $attendances = Attendance::onlyTrashed()
            ->select('id', 'employee_id', 'year', 'month', 'present', 'created_at')
            ->with(['employee'])
            ->orderBy('employee_id')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $attendances = $attendances->groupBy('employee_id');

        return view('dashboard.attendances.index', compact('attendances'))->with('isTrash', true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', Attendance::class);
        $employees = Employee::all();
        return view('dashboard.attendances.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Attendance::class);
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'work_days' => 'required|integer|min:0',
            'present' => 'required|integer|min:0',
            'sick' => 'required|integer|min:0',
            'leave' => 'required|integer|min:0',
            'alpha' => 'required|integer|min:0',
            'overtime_hours' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        Attendance::create($validated);

        return redirect()->route('attendances.index')->with('success', 'Success create new attendance data.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $attendance = Attendance::withTrashed()->with(['employee'])->findOrFail($id);
        Gate::authorize('view', $attendance);

        $periods = Attendance::withTrashed()
            ->where('employee_id', $attendance->employee_id)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('dashboard.attendances.show', compact('attendance', 'periods'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $attendance = Attendance::findOrFail($id);
        Gate::authorize('update', $attendance);
        $employees = Employee::all();
        return view('dashboard.attendances.edit', compact('attendance', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $attendance = Attendance::findOrFail($id);
        Gate::authorize('update', $attendance);
        
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'work_days' => 'required|integer|min:0',
            'present' => 'required|integer|min:0',
            'sick' => 'required|integer|min:0',
            'leave' => 'required|integer|min:0',
            'alpha' => 'required|integer|min:0',
            'overtime_hours' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $attendance->update($validated);

        return redirect()->route('attendances.index')->with('success', 'Success update attendance data.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $attendance = Attendance::findOrFail($id);
        Gate::authorize('delete', $attendance);
        $attendance->delete();

        return redirect()->route('attendances.index')->with('success', 'Success move attendance data to trash.');
    }

    /**
     * Restore the specified deleted resource.
     */
    public function restore(string $id)
    {
        $attendance = Attendance::onlyTrashed()->findOrFail($id);
        Gate::authorize('restore', $attendance);
        $attendance->restore();

        return redirect()->route('attendances.trash')->with('success', 'Success restore attendance data.');
    }

    /**
     * Permanently delete the specified resource.
     */
    public function forceDelete(string $id)
    {
        $attendance = Attendance::onlyTrashed()->findOrFail($id);
        Gate::authorize('forceDelete', $attendance);
        $attendance->forceDelete();

        return redirect()->route('attendances.trash')->with('success', 'Success permanently delete attendance data.');
    }


    /**
     * Export single attendance to CSV.
     */
    public function export(string $id)
    {
        $attendance = Attendance::withTrashed()->with(['employee'])->findOrFail($id);
        Gate::authorize('view', $attendance);

        $fileName = 'attendance_' . $attendance->id . '_' . date('Y-m-d') . '.csv';
        $headers  = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($attendance) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['Field', 'Value']);
            
            // Attendance details
            fputcsv($file, ['ID', $attendance->id]);
            fputcsv($file, ['Employee', $attendance->employee ? $attendance->employee->name : '-']);
            fputcsv($file, ['Year', $attendance->year]);
            fputcsv($file, ['Month', $attendance->month]);
            fputcsv($file, ['Work Days', $attendance->work_days]);
            fputcsv($file, ['Present', $attendance->present]);
            fputcsv($file, ['Sick', $attendance->sick]);
            fputcsv($file, ['Leave', $attendance->leave]);
            fputcsv($file, ['Alpha', $attendance->alpha]);
            fputcsv($file, ['Overtime Hours', $attendance->overtime_hours]);
            fputcsv($file, ['Notes', $attendance->notes]);
            fputcsv($file, ['Created At', $attendance->created_at->format('Y-m-d H:i:s')]);
            fputcsv($file, ['Updated At', $attendance->updated_at->format('Y-m-d H:i:s')]);
            if ($attendance->deleted_at) {
                fputcsv($file, ['Deleted At', $attendance->deleted_at->format('Y-m-d H:i:s')]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        Gate::authorize('create', Attendance::class);
        
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $import = new AttendanceImport();
        Excel::import($import, $request->file('file'));

        if (!empty($import->failures)) {
            $messages = [];
            foreach ($import->failures as $failure) {
                $messages[] = "{$failure['row']}: {$failure['error']}";
            }
            return back()->with('warning', 'Beberapa data gagal diimport: ' . implode(' | ', $messages));
        }

        return back()->with('success', 'Attendance data imported successfully!');
    }

    public function downloadSampleTemplate()
    {
        Gate::authorize('create', Attendance::class);

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=attendance_template.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['employee_id', 'employee_name', 'year', 'month', 'work_days', 'present', 'sick', 'leave', 'alpha', 'overtime_hours', 'notes']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
