<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Payroll;
use App\Services\PayrollCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PayrollController extends Controller
{
    public function __construct(private PayrollCalculatorService $calculator) {}

    // ----------------------------------------------------------------
    // Listing
    // ----------------------------------------------------------------

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Payroll::class);

        $query = Payroll::select('id', 'employee_id', 'year', 'month', 'base_salary', 'bonus', 'total_salary', 'status', 'pay_date')
            ->with('employee:id,name,employee_code')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderBy('employee_id');

        // Period drill-down filter (from Periods view)
        $filterYear  = $request->integer('year') ?: null;
        $filterMonth = $request->integer('month') ?: null;

        // Additional filters
        $filterStatus     = $request->input('status');
        $filterEmployeeId = $request->input('employee_id');

        if ($filterYear)  $query->where('year', $filterYear);
        if ($filterMonth) $query->where('month', $filterMonth);

        if ($filterStatus && in_array($filterStatus, ['draft', 'approved', 'paid'])) {
            $query->where('status', $filterStatus);
        }
        if ($filterEmployeeId) {
            $query->where('employee_id', $filterEmployeeId);
        }

        $payrolls = $query->get();

        $periodLabel = ($filterYear && $filterMonth)
            ? \Carbon\Carbon::create($filterYear, $filterMonth)->translatedFormat('F Y')
            : null;

        $allEmployees = Employee::select('id', 'name', 'nik')->orderBy('name')->get();

        return view('dashboard.payrolls.index', compact(
            'payrolls', 'periodLabel',
            'filterYear', 'filterMonth', 'filterStatus', 'filterEmployeeId',
            'allEmployees'
        ));
    }

    /**
     * Payroll Periods — grouped summary view (year + month).
     * Each row = one period with aggregate stats.
     */
    public function periods(Request $request)
    {
        Gate::authorize('viewAny', Payroll::class);

        $filterYear   = $request->input('year');
        $filterMonth  = $request->input('month');
        $filterStatus = $request->input('period_status');

        $query = Payroll::selectRaw('
                year,
                month,
                COUNT(*) as total_employees,
                SUM(base_salary) as total_base_salary,
                SUM(bonus) as total_bonus,
                SUM(total_salary) as total_salary,
                SUM(CASE WHEN status = "draft"    THEN 1 ELSE 0 END) as draft_count,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_count,
                SUM(CASE WHEN status = "paid"     THEN 1 ELSE 0 END) as paid_count,
                MIN(pay_date) as pay_date
            ')
            ->groupBy('year', 'month')
            ->orderByDesc('year')
            ->orderByDesc('month');

        if ($filterYear)  $query->where('year', $filterYear);
        if ($filterMonth) $query->where('month', $filterMonth);

        $periods = $query->get();

        // Filter period status in PHP (after aggregation)
        if ($filterStatus === 'paid') {
            $periods = $periods->filter(fn($p) => $p->paid_count === $p->total_employees);
        } elseif ($filterStatus === 'approved') {
            $periods = $periods->filter(fn($p) => $p->draft_count === 0 && $p->paid_count < $p->total_employees);
        } elseif ($filterStatus === 'draft') {
            $periods = $periods->filter(fn($p) => $p->draft_count > 0);
        }

        $availableYears = Payroll::selectRaw('DISTINCT year')->orderByDesc('year')->pluck('year');

        return view('dashboard.payrolls.periods', compact('periods', 'filterYear', 'filterMonth', 'filterStatus', 'availableYears'));
    }

    public function trash()
    {
        Gate::authorize('viewAny', Payroll::class);

        $payrolls = Payroll::onlyTrashed()
            ->select('id', 'employee_id', 'year', 'month', 'base_salary', 'bonus', 'total_salary', 'status', 'pay_date')
            ->with('employee:id,name,employee_code')
            ->latest()
            ->get();

        return view('dashboard.payrolls.index', compact('payrolls'))->with('isTrash', true);
    }

    // ----------------------------------------------------------------
    // Manual single payroll (create / edit)
    // ----------------------------------------------------------------

    public function create()
    {
        Gate::authorize('create', Payroll::class);

        $employees = Employee::select('id', 'name', 'employee_code', 'position_id', 'employee_type')
            ->with('position:id,base_salary_fulltime,base_salary_internship')
            ->where('employee_status', 'active')
            ->orderBy('name')
            ->get();

        return view('dashboard.payrolls.create', compact('employees'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Payroll::class);

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'year'        => 'required|integer|min:2000|max:2100',
            'month'       => 'required|integer|min:1|max:12',
            'pay_date'    => 'required|date',
            'base_salary' => 'required|numeric|min:0',
            'bonus'       => 'required|numeric|min:0',
            'total_salary'=> 'required|numeric|min:0',
            'notes'       => 'nullable|string',
            'status'      => 'required|in:draft,approved,paid',
        ]);

        Payroll::create($validated);

        return redirect()->route('payrolls.index')
            ->with('success', 'Payroll record created successfully.');
    }

    public function show(string $id)
    {
        $payroll = Payroll::withTrashed()->with('employee')->findOrFail($id);
        Gate::authorize('view', $payroll);

        return view('dashboard.payrolls.show', compact('payroll'));
    }

    public function edit(string $id)
    {
        $payroll = Payroll::findOrFail($id);
        Gate::authorize('update', $payroll);

        $employees = Employee::select('id', 'name', 'employee_code')
            ->where('employee_status', 'active')
            ->orderBy('name')
            ->get();

        return view('dashboard.payrolls.edit', compact('payroll', 'employees'));
    }

    public function update(Request $request, string $id)
    {
        $payroll = Payroll::findOrFail($id);
        Gate::authorize('update', $payroll);

        $validated = $request->validate([
            'pay_date'    => 'required|date',
            'base_salary' => 'required|numeric|min:0',
            'bonus'       => 'required|numeric|min:0',
            'total_salary'=> 'required|numeric|min:0',
            'notes'       => 'nullable|string',
            'status'      => 'required|in:draft,approved,paid',
        ]);

        $payroll->update($validated);

        return redirect()->route('payrolls.index')
            ->with('success', 'Payroll updated successfully.');
    }

    // ----------------------------------------------------------------
    // Bulk generate via PayrollCalculatorService
    // ----------------------------------------------------------------

    /**
     * Show the "Run Payroll" form.
     */
    public function generateForm()
    {
        Gate::authorize('create', Payroll::class);

        return view('dashboard.payrolls.generate');
    }

    /**
     * Execute bulk payroll generation for a given period.
     */
    public function generateBulk(Request $request)
    {
        Gate::authorize('create', Payroll::class);

        $validated = $request->validate([
            'year'     => 'required|integer|min:2000|max:2100',
            'month'    => 'required|integer|min:1|max:12',
            'pay_date' => 'required|date',
        ]);

        $result = $this->calculator->generateBulk(
            (int) $validated['year'],
            (int) $validated['month'],
            $validated['pay_date']
        );

        $message = "Payroll run complete. Created: {$result['created']} records.";
        if ($result['skipped'] > 0) {
            $message .= " Skipped (already exists): {$result['skipped']}.";
        }

        return redirect()->route('payrolls.periods')->with('success', $message);
    }

    /**
     * Preview what generateBulk will produce (no DB writes).
     */
    public function generatePreview(Request $request)
    {
        Gate::authorize('create', Payroll::class);

        $request->validate([
            'year'  => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $year  = (int) $request->year;
        $month = (int) $request->month;

        $employees = Employee::with('position')
            ->where('employee_status', 'active')
            ->get();

        $preview = $employees->map(function (Employee $employee) use ($year, $month) {
            $components  = $this->calculator->calculate($employee, $year, $month);
            $alreadyDone = Payroll::where('employee_id', $employee->id)
                ->where('year', $year)
                ->where('month', $month)
                ->exists();

            return array_merge($components, [
                'employee'     => $employee,
                'already_done' => $alreadyDone,
            ]);
        });

        return response()->json($preview);
    }

    // ----------------------------------------------------------------
    // Approve / Mark Paid
    // ----------------------------------------------------------------

    public function approve(string $id)
    {
        $payroll = Payroll::findOrFail($id);
        Gate::authorize('update', $payroll);

        $payroll->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Payroll approved.');
    }

    public function markPaid(string $id)
    {
        $payroll = Payroll::findOrFail($id);
        Gate::authorize('update', $payroll);

        $payroll->update(['status' => 'paid']);

        return redirect()->back()->with('success', 'Payroll marked as paid.');
    }

    // ----------------------------------------------------------------
    // Soft delete / restore / force delete
    // ----------------------------------------------------------------

    public function destroy(string $id)
    {
        $payroll = Payroll::findOrFail($id);
        Gate::authorize('delete', $payroll);
        $payroll->delete();

        return redirect()->route('payrolls.index')
            ->with('success', 'Payroll moved to trash.');
    }

    public function restore(string $id)
    {
        $payroll = Payroll::onlyTrashed()->findOrFail($id);
        Gate::authorize('restore', $payroll);
        $payroll->restore();

        return redirect()->route('payrolls.trash')
            ->with('success', 'Payroll restored successfully.');
    }

    public function forceDelete(string $id)
    {
        $payroll = Payroll::onlyTrashed()->findOrFail($id);
        Gate::authorize('forceDelete', $payroll);
        $payroll->forceDelete();

        return redirect()->route('payrolls.trash')
            ->with('success', 'Payroll permanently deleted.');
    }

    // ----------------------------------------------------------------
    // Export
    // ----------------------------------------------------------------

    public function export(string $id)
    {
        $payroll = Payroll::withTrashed()->with('employee')->findOrFail($id);
        Gate::authorize('view', $payroll);

        $fileName = 'payroll_' . $payroll->id . '_' . date('Y-m-d') . '.csv';
        $headers  = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($payroll) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Field', 'Value']);
            fputcsv($file, ['Payroll ID', $payroll->id]);
            fputcsv($file, ['Employee Code', $payroll->employee?->employee_code ?? '-']);
            fputcsv($file, ['Employee Name', $payroll->employee?->name ?? '-']);
            fputcsv($file, ['Period', $payroll->monthName()]);
            fputcsv($file, ['Pay Date', $payroll->pay_date?->format('Y-m-d')]);
            fputcsv($file, ['Base Salary', $payroll->base_salary]);
            fputcsv($file, ['Bonus', $payroll->bonus]);
            fputcsv($file, ['Total Salary', $payroll->total_salary]);
            fputcsv($file, ['Status', $payroll->status]);
            fputcsv($file, ['Notes', $payroll->notes ?? '-']);
            fputcsv($file, ['Created At', $payroll->created_at->format('Y-m-d H:i:s')]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
