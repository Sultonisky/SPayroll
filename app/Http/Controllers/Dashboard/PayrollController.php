<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', Payroll::class);
        $payrolls = Payroll::select('id', 'employee_id', 'year', 'month', 'total_salary', 'status', 'created_at')
            ->with(['employee'])
            ->latest()
            ->get();
            
        return view('dashboard.payrolls.index', compact('payrolls'));
    }

    /**
     * Display a listing of deleted resources.
     */
    public function trash()
    {
        Gate::authorize('viewAny', Payroll::class);
        $payrolls = Payroll::onlyTrashed()
            ->select('id', 'employee_id', 'year', 'month', 'total_salary', 'status', 'created_at')
            ->with(['employee'])
            ->latest()
            ->get();
            
        return view('dashboard.payrolls.index', compact('payrolls'))->with('isTrash', true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', Payroll::class);
        $employees = Employee::all();
        return view('dashboard.payrolls.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Payroll::class);
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'pay_date' => 'required|date',
            'base_salary' => 'required|numeric|min:0',
            'allowances' => 'required|numeric|min:0',
            'bonus' => 'required|numeric|min:0',
            'overtime_pay' => 'required|numeric|min:0',
            'deductions' => 'required|numeric|min:0',
            'total_salary' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,paid',
        ]);

        Payroll::create($validated);

        return redirect()->route('payrolls.index')->with('success', 'Success create new payroll data.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payroll = Payroll::withTrashed()->with(['employee'])->findOrFail($id);
        Gate::authorize('view', $payroll);
        return view('dashboard.payrolls.show', compact('payroll'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $payroll = Payroll::findOrFail($id);
        Gate::authorize('update', $payroll);
        $employees = Employee::all();
        return view('dashboard.payrolls.edit', compact('payroll', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $payroll = Payroll::findOrFail($id);
        Gate::authorize('update', $payroll);
        
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'pay_date' => 'required|date',
            'base_salary' => 'required|numeric|min:0',
            'allowances' => 'required|numeric|min:0',
            'bonus' => 'required|numeric|min:0',
            'overtime_pay' => 'required|numeric|min:0',
            'deductions' => 'required|numeric|min:0',
            'total_salary' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,paid',
        ]);

        $payroll->update($validated);

        return redirect()->route('payrolls.index')->with('success', 'Success update payroll data.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $payroll = Payroll::findOrFail($id);
        Gate::authorize('delete', $payroll);
        $payroll->delete();

        return redirect()->route('payrolls.index')->with('success', 'Success move payroll data to trash.');
    }

    /**
     * Restore the specified deleted resource.
     */
    public function restore(string $id)
    {
        $payroll = Payroll::onlyTrashed()->findOrFail($id);
        Gate::authorize('restore', $payroll);
        $payroll->restore();

        return redirect()->route('payrolls.trash')->with('success', 'Success restore payroll data.');
    }

    /**
     * Permanently delete the specified resource.
     */
    public function forceDelete(string $id)
    {
        $payroll = Payroll::onlyTrashed()->findOrFail($id);
        Gate::authorize('forceDelete', $payroll);
        $payroll->forceDelete();

        return redirect()->route('payrolls.trash')->with('success', 'Success permanently delete payroll data.');
    }

    /**
     * Export single payroll to CSV.
     */
    public function export(string $id)
    {
        // ->with(['employee', 'attendance']) — 'attendance' relation disabled (attendance feature not yet needed)
        $payroll = Payroll::withTrashed()->with(['employee'])->findOrFail($id);
        Gate::authorize('view', $payroll);

        $fileName = 'payroll_' . $payroll->id . '_' . date('Y-m-d') . '.csv';
        $headers  = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($payroll) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['Field', 'Value']);
            
            // Payroll details
            fputcsv($file, ['ID', $payroll->id]);
            fputcsv($file, ['Employee Name', $payroll->employee ? $payroll->employee->name : '-']);
            fputcsv($file, ['Year', $payroll->year]);
            fputcsv($file, ['Month', $payroll->month]);
            fputcsv($file, ['Pay Date', $payroll->pay_date->format('Y-m-d')]);
            fputcsv($file, ['Base Salary', $payroll->base_salary]);
            fputcsv($file, ['Allowances', $payroll->allowances]);
            fputcsv($file, ['Bonus', $payroll->bonus]);
            fputcsv($file, ['Overtime Pay', $payroll->overtime_pay]);
            fputcsv($file, ['Deductions', $payroll->deductions]);
            fputcsv($file, ['Total Salary', $payroll->total_salary]);
            fputcsv($file, ['Status', $payroll->status]);
            fputcsv($file, ['Notes', $payroll->notes]);
            fputcsv($file, ['Created At', $payroll->created_at->format('Y-m-d H:i:s')]);
            fputcsv($file, ['Updated At', $payroll->updated_at->format('Y-m-d H:i:s')]);
            if ($payroll->deleted_at) {
                fputcsv($file, ['Deleted At', $payroll->deleted_at->format('Y-m-d H:i:s')]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
