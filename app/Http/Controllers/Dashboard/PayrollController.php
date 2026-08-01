<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Bonus;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\User;
use App\Notifications\DashboardNotification;
use App\Services\PayrollCalculatorService;
use Carbon\Carbon;
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
            ->whereIn('status', ['paid'])
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderBy('employee_id');

        // Period drill-down filter (from Periods view)
        $filterYear = $request->integer('year') ?: null;
        $filterMonth = $request->integer('month') ?: null;

        // Additional filters
        $filterStatus = $request->input('status');
        $filterEmployeeId = $request->input('employee_id');

        if ($filterYear) {
            $query->where('year', $filterYear);
        }
        if ($filterMonth) {
            $query->where('month', $filterMonth);
        }

        if ($filterStatus && in_array($filterStatus, ['paid'])) {
            $query->where('status', $filterStatus);
        }
        if ($filterEmployeeId) {
            $query->where('employee_id', $filterEmployeeId);
        }

        $payrolls = $query->get();

        $periodLabel = ($filterYear && $filterMonth)
            ? Carbon::create($filterYear, $filterMonth)->translatedFormat('F Y')
            : null;

        $allEmployees = Employee::select('id', 'name', 'nik', 'employee_code')->orderBy('employee_code')->get();

        return view('dashboard.payrolls.index', compact(
            'payrolls', 'periodLabel',
            'filterYear', 'filterMonth', 'filterStatus', 'filterEmployeeId',
            'allEmployees'
        ));
    }

    /**
     * Payroll Approved — shows all approved payroll records ready to be paid.
     */
    public function approved(Request $request)
    {
        Gate::authorize('viewAny', Payroll::class);

        $filterYear = $request->input('year');
        $filterMonth = $request->input('month');
        $filterEmployeeId = $request->input('employee_id');

        $query = Payroll::select('id', 'employee_id', 'year', 'month', 'base_salary', 'bonus', 'total_salary', 'status', 'pay_date')
            ->with('employee:id,name,employee_code')
            ->where('status', 'approved')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderBy('employee_id');

        if ($filterYear) {
            $query->where('year', $filterYear);
        }
        if ($filterMonth) {
            $query->where('month', $filterMonth);
        }
        if ($filterEmployeeId) {
            $query->where('employee_id', $filterEmployeeId);
        }

        $payrolls = $query->get();
        $allEmployees = Employee::select('id', 'name', 'nik', 'employee_code')->orderBy('name')->get();

        return view('dashboard.payrolls.approved', compact(
            'payrolls', 'allEmployees',
            'filterYear', 'filterMonth', 'filterEmployeeId'
        ));
    }

    public function drafts(Request $request)
    {
        Gate::authorize('viewAny', Payroll::class);

        $filterYear = $request->input('year');
        $filterMonth = $request->input('month');
        $filterEmployeeId = $request->input('employee_id');

        $query = Payroll::select('id', 'employee_id', 'year', 'month', 'base_salary', 'bonus', 'total_salary', 'status', 'pay_date')
            ->with('employee:id,name,employee_code')
            ->where('status', 'draft')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderBy('employee_id');

        if ($filterYear) {
            $query->where('year', $filterYear);
        }
        if ($filterMonth) {
            $query->where('month', $filterMonth);
        }
        if ($filterEmployeeId) {
            $query->where('employee_id', $filterEmployeeId);
        }

        $payrolls = $query->get();
        $allEmployees = Employee::select('id', 'name', 'nik', 'employee_code')->orderBy('employee_code')->get();

        return view('dashboard.payrolls.drafts', compact(
            'payrolls', 'allEmployees',
            'filterYear', 'filterMonth', 'filterEmployeeId'
        ));
    }

    public function periods(Request $request)
    {
        Gate::authorize('viewAny', Payroll::class);

        $filterYear = $request->input('year');
        $filterMonth = $request->input('month');
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

        if ($filterYear) {
            $query->where('year', $filterYear);
        }
        if ($filterMonth) {
            $query->where('month', $filterMonth);
        }

        $periods = $query->get();

        // Filter period status in PHP (after aggregation)
        if ($filterStatus === 'paid') {
            $periods = $periods->filter(fn ($p) => $p->paid_count === $p->total_employees);
        } elseif ($filterStatus === 'approved') {
            $periods = $periods->filter(fn ($p) => $p->draft_count === 0 && $p->paid_count < $p->total_employees);
        } elseif ($filterStatus === 'draft') {
            $periods = $periods->filter(fn ($p) => $p->draft_count > 0);
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
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'pay_date' => 'required|date',
            'base_salary' => 'required|numeric|min:0',
            'bonus' => 'required|numeric|min:0',
            'total_salary' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:draft,approved,paid',
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
            'pay_date' => 'required|date',
            'base_salary' => 'required|numeric|min:0',
            'bonus' => 'required|numeric|min:0',
            'total_salary' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:draft,approved,paid',
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
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
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

        return redirect()->route('payrolls.drafts', [
            'year' => $validated['year'],
            'month' => $validated['month'],
        ])->with('success', $message);
    }

    /**
     * Preview what generateBulk will produce (no DB writes).
     */
    public function generatePreview(Request $request)
    {
        Gate::authorize('create', Payroll::class);

        $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $year = (int) $request->year;
        $month = (int) $request->month;

        $employees = Employee::with('position')
            ->where('employee_status', 'active')
            ->orderBy('name')
            ->get();

        $preview = $employees->map(function (Employee $employee) use ($year, $month) {
            $components = $this->calculator->calculate($employee, $year, $month);
            $alreadyDone = Payroll::where('employee_id', $employee->id)
                ->where('year', $year)
                ->where('month', $month)
                ->exists();

            return array_merge($components, [
                'employee' => $employee,
                'already_done' => $alreadyDone,
            ]);
        });

        return response()->json($preview);
    }

    /**
     * Bulk approve all selected draft payrolls.
     */
    public function approveAll(Request $request)
    {
        Gate::authorize('create', Payroll::class);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:payrolls,id',
        ]);

        $payrolls = Payroll::whereIn('id', $request->ids)
            ->where('status', 'draft')
            ->get();

        foreach ($payrolls as $payroll) {
            $payroll->update(['status' => 'approved']);
        }

        $count = $payrolls->count();

        // Send a single bulk notification instead of per-record noise
        $managers = User::whereIn('role', ['admin', 'HR', 'finance'])
            ->where('id', '!=', auth()->id())
            ->get();

        foreach ($managers as $user) {
            $user->notify(new DashboardNotification(
                'Bulk Payroll Approved',
                "{$count} draft payroll record(s) have been approved in bulk.",
                route('payrolls.approved'),
                'success'
            ));
        }

        return redirect()->route('payrolls.approved')
            ->with('success', 'All selected draft payrolls have been approved.');
    }

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

    /**
     * Export approved payrolls as CSV for bank transfer.
     * Supports same filters as approved() — year, month, employee_id.
     */
    public function exportApproved(Request $request)
    {
        Gate::authorize('viewAny', Payroll::class);

        $query = Payroll::where('status', 'approved')
            ->with(['employee:id,name,employee_code,bank_name,bank_account_number,nik'])
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderBy('employee_id');

        if ($request->input('year')) {
            $query->where('year', $request->input('year'));
        }
        if ($request->input('month')) {
            $query->where('month', $request->input('month'));
        }
        if ($request->input('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        $payrolls = $query->get();

        $periodLabel = ($request->input('year') && $request->input('month'))
            ? '_'.$request->input('year').'_'.str_pad($request->input('month'), 2, '0', STR_PAD_LEFT)
            : '';

        $fileName = 'payroll_transfer'.$periodLabel.'_'.date('Ymd').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($payrolls) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'No',
                'Employee Code',
                'Employee Name',
                'NIK',
                'Bank',
                'Account Number',
                'Base Salary',
                'Bonus',
                'Total Salary',
                'Period',
                'Pay Date',
                'Notes',
            ]);

            foreach ($payrolls as $i => $payroll) {
                fputcsv($file, [
                    $i + 1,
                    $payroll->employee?->employee_code ?? '-',
                    $payroll->employee?->name ?? '-',
                    $payroll->employee?->nik ?? '-',
                    $payroll->employee?->bank_name ?? '-',
                    $payroll->employee?->bank_account_number ?? '-',
                    $payroll->base_salary,
                    $payroll->bonus,
                    $payroll->total_salary,
                    $payroll->monthName(),
                    $payroll->pay_date?->format('Y-m-d') ?? '-',
                    $payroll->notes ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function markPaidAll(Request $request)
    {
        Gate::authorize('create', Payroll::class);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:payrolls,id',
        ]);

        $payrolls = Payroll::whereIn('id', $request->ids)
            ->where('status', 'approved')
            ->get();

        foreach ($payrolls as $payroll) {
            $payroll->update(['status' => 'paid']);
        }

        $count = $payrolls->count();

        // Send a single bulk notification
        $managers = User::whereIn('role', ['admin', 'HR', 'finance'])
            ->where('id', '!=', auth()->id())
            ->get();

        foreach ($managers as $user) {
            $user->notify(new DashboardNotification(
                'Bulk Payroll Marked as Paid',
                "{$count} payroll record(s) have been marked as paid in bulk.",
                route('payrolls.index'),
                'success'
            ));
        }

        return redirect()->route('payrolls.index')
            ->with('success', 'All selected payrolls have been marked as paid.');
    }

    // ----------------------------------------------------------------
    // Payslip
    // ----------------------------------------------------------------

    /**
     * List all paid payrolls as payslips (with employee filter).
     * Staff can only see their own payslip.
     */
    public function payslipIndex(Request $request)
    {
        $user = auth()->user();
        $isStaff = $user->role === 'staff';
        $canViewAll = in_array($user->role, ['admin', 'HR', 'finance']);

        $query = Payroll::select('id', 'employee_id', 'year', 'month', 'base_salary', 'bonus', 'total_salary', 'status', 'pay_date', 'notes')
            ->with('employee:id,name,employee_code,nik,department_id,position_id,employee_type,bank_name,bank_account_number')
            ->where('status', 'paid')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderBy('employee_id');

        // Staff: only see their own payslip
        if ($isStaff) {
            $user->loadMissing('employee');
            abort_unless($user->employee, 403, 'Akun Anda belum terhubung ke data karyawan.');
            $query->where('employee_id', $user->employee->id);
        }

        $filterYear = $request->integer('year') ?: null;
        $filterMonth = $request->integer('month') ?: null;
        $filterEmployeeId = $request->input('employee_id');

        if ($filterYear) {
            $query->where('year', $filterYear);
        }
        if ($filterMonth) {
            $query->where('month', $filterMonth);
        }

        if ($canViewAll && $filterEmployeeId) {
            $query->where('employee_id', $filterEmployeeId);
        }

        $payrolls = $query->get();
        $allEmployees = $canViewAll
            ? Employee::select('id', 'name', 'employee_code')->orderBy('employee_code')->get()
            : collect();

        return view('dashboard.payrolls.payslip', compact(
            'payrolls', 'allEmployees',
            'filterYear', 'filterMonth', 'filterEmployeeId',
            'isStaff', 'canViewAll'
        ));
    }

    /**
     * Show a single payslip detail — printable.
     */
    public function payslipShow(string $id)
    {
        $payroll = Payroll::where('status', 'paid')
            ->with([
                'employee:id,name,employee_code,nik,department_id,position_id,employee_type,bank_name,bank_account_number,join_date',
                'employee.department:id,name',
                'employee.position:id,name,base_salary_fulltime,base_salary_internship',
            ])
            ->findOrFail($id);

        // Policy enforces: admin/HR/finance = all, staff = own only, manager = denied
        Gate::authorize('viewPayslip', $payroll);

        // Load bonuses that were included in this payroll period
        $bonuses = Bonus::where('employee_id', $payroll->employee_id)
            ->where('year', $payroll->year)
            ->where('month', $payroll->month)
            ->where('status', 'approved')
            ->get();

        return view('dashboard.payrolls.payslip-show', compact('payroll', 'bonuses'));
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

        $fileName = 'payroll_'.$payroll->id.'_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
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
