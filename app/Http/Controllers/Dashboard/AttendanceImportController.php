<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AttendanceImport;
use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceImportController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function index()
    {
        $imports = AttendanceImport::with('importedBy')->orderBy('created_at', 'desc')->get();
        return view('dashboard.attendance-imports.index', compact('imports'));
    }

    public function create()
    {
        return view('dashboard.attendance-imports.create');
    }

    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('file');
        $rows = [];
        $valid = [];
        $invalid = [];

        try {
            $collection = Excel::toArray([], $file);
            $data = $collection[0];

            // Skip header
            $header = array_shift($data);

            foreach ($data as $index => $row) {
                $rowNumber = $index + 2;
                $employeeNik = $row[0] ?? null;
                $attendanceDate = $row[1] ?? null;
                $checkIn = $row[2] ?? null;
                $checkOut = $row[3] ?? null;

                $rowData = [
                    'row_number' => $rowNumber,
                    'employee_nik' => $employeeNik,
                    'attendance_date' => $attendanceDate,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                ];

                // Validate
                $errors = [];

                if (!$employeeNik) {
                    $errors[] = 'Employee NIK is required';
                } else {
                    $employee = Employee::where('nik', $employeeNik)->first();
                    if (!$employee) {
                        $errors[] = 'Employee not found';
                    }
                }

                if (!$attendanceDate) {
                    $errors[] = 'Attendance date is required';
                } else {
                    try {
                        $date = \Carbon\Carbon::parse($attendanceDate)->format('Y-m-d');
                        $rowData['attendance_date'] = $date;
                    } catch (\Exception $e) {
                        $errors[] = 'Invalid date format';
                    }
                }

                if (empty($errors)) {
                    $valid[] = $rowData;
                } else {
                    $invalid[] = [
                        'row_number' => $rowNumber,
                        'errors' => $errors,
                    ];
                }

                $rows[] = $rowData;
            }

            // Store in session for later import
            session()->put('attendance_import_data', [
                'file_name' => $file->getClientOriginalName(),
                'rows' => $rows,
                'valid' => $valid,
                'invalid' => $invalid,
            ]);

            return view('dashboard.attendance-imports.preview', compact('rows', 'valid', 'invalid'));
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Error reading file: ' . $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        $importData = session()->get('attendance_import_data');
        if (!$importData) {
            return redirect()->route('attendance-imports.create')->with('error', 'No import data found');
        }

        $user = Auth::user();

        $attendanceImport = AttendanceImport::create([
            'file_name' => $importData['file_name'],
            'imported_by' => $user->id,
            'total_rows' => count($importData['rows']),
            'success_rows' => 0,
            'failed_rows' => 0,
            'status' => 'pending',
        ]);

        $successCount = 0;
        $failedCount = 0;

        foreach ($importData['valid'] as $row) {
            $employee = Employee::where('nik', $row['employee_nik'])->first();
            if (!$employee) {
                $failedCount++;
                continue;
            }

            try {
                $record = AttendanceRecord::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'attendance_date' => $row['attendance_date'],
                    ],
                    [
                        'attendance_import_id' => $attendanceImport->id,
                        'check_in' => $row['check_in'] ? date('H:i:s', strtotime($row['check_in'])) : null,
                        'check_out' => $row['check_out'] ? date('H:i:s', strtotime($row['check_out'])) : null,
                    ]
                );

                // Calculate attendance data
                $calculatedData = $this->attendanceService->calculateAttendanceData($record);
                $record->update($calculatedData);
                $successCount++;
            } catch (\Exception $e) {
                $failedCount++;
            }
        }

        $attendanceImport->update([
            'success_rows' => $successCount,
            'failed_rows' => $failedCount,
            'status' => 'completed',
            'imported_at' => now(),
        ]);

        session()->forget('attendance_import_data');

        return redirect()->route('attendance-imports.index')->with('success', 'Attendance imported successfully');
    }

    public function show(AttendanceImport $attendanceImport)
    {
        $attendanceImport->load('attendanceRecords.employee');
        return view('dashboard.attendance-imports.show', compact('attendanceImport'));
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=attendance_template.csv',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['employee_nik', 'attendance_date', 'check_in', 'check_out']);
            fputcsv($file, ['EMP001', '2026-07-01', '08:00:00', '17:00:00']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
