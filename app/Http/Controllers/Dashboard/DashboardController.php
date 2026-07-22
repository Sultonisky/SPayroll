<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Bonus;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $now         = Carbon::now();
        $currentYear = $now->year;
        $currentMonth = $now->month;

        // ── Stats Cards ──────────────────────────────────────────────

        // Employees
        $totalEmployees      = Employee::count();
        $activeEmployees     = Employee::where('employee_status', 'active')->count();
        $fulltimeEmployees   = Employee::where('employee_type', 'fulltime')->count();
        $internshipEmployees = Employee::where('employee_type', 'internship')->count();

        // New employees this month
        $newEmployeesThisMonth = Employee::whereYear('join_date', $currentYear)
            ->whereMonth('join_date', $currentMonth)
            ->count();

        // Departments & Positions
        $totalDepartments = Department::count();
        $totalPositions   = Position::count();

        // Users
        $totalUsers = User::count();

        // ── Payroll Stats ─────────────────────────────────────────────

        // Current month payroll
        $currentPeriodPayrolls = Payroll::where('year', $currentYear)
            ->where('month', $currentMonth)
            ->get();

        $currentPeriodDrafts    = $currentPeriodPayrolls->where('status', 'draft')->count();
        $currentPeriodApproved  = $currentPeriodPayrolls->where('status', 'approved')->count();
        $currentPeriodPaid      = $currentPeriodPayrolls->where('status', 'paid')->count();
        $currentPeriodTotal     = $currentPeriodPayrolls->count();
        $currentPeriodTotalSalary = $currentPeriodPayrolls->sum('total_salary');

        // All-time paid payroll total
        $allTimePaidTotal = Payroll::where('status', 'paid')->sum('total_salary');

        // Pending actions (drafts + approved waiting to be paid — all periods)
        $pendingDrafts    = Payroll::where('status', 'draft')->count();
        $pendingApprovals = Payroll::where('status', 'approved')->count();

        // ── Bonus Stats ───────────────────────────────────────────────

        // Pending = all-time (any period, needs action)
        $pendingBonuses  = Bonus::where('status', 'pending')->count();

        // Approved & amount = all-time
        $approvedBonuses = Bonus::where('status', 'approved')->count();
        $totalBonusAmount = Bonus::where('status', 'approved')->sum('amount');

        // ── Monthly Payroll Chart (last 6 months) ──────────────────────

        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date  = $now->copy()->subMonths($i);
            $label = $date->translatedFormat('M Y');
            $total = Payroll::where('year', $date->year)
                ->where('month', $date->month)
                ->whereIn('status', ['approved', 'paid'])
                ->sum('total_salary');

            $chartData[] = [
                'label'  => $label,
                'total'  => (float) $total,
                'paid'   => (float) Payroll::where('year', $date->year)
                    ->where('month', $date->month)
                    ->where('status', 'paid')
                    ->sum('total_salary'),
            ];
        }

        // ── Employee per Department (for donut chart) ─────────────────

        $deptData = Department::withCount('employees')
            ->orderByDesc('employees_count')
            ->limit(6)
            ->get()
            ->map(fn($d) => [
                'name'  => $d->name,
                'count' => $d->employees_count,
            ]);

        // ── Recent Payrolls ───────────────────────────────────────────

        $recentPayrolls = Payroll::with('employee:id,name,employee_code')
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();

        // ── Latest Bonuses Pending Approval ──────────────────────────

        $latestPendingBonuses = Bonus::with('employee:id,name,employee_code')
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // ── Employee Type breakdown ───────────────────────────────────
        $employeeTypeData = [
            ['label' => 'Fulltime',    'count' => $fulltimeEmployees],
            ['label' => 'Internship',  'count' => $internshipEmployees],
        ];

        return view('dashboard.index', compact(
            'totalEmployees', 'activeEmployees', 'fulltimeEmployees', 'internshipEmployees',
            'newEmployeesThisMonth', 'totalDepartments', 'totalPositions', 'totalUsers',
            'currentPeriodDrafts', 'currentPeriodApproved', 'currentPeriodPaid',
            'currentPeriodTotal', 'currentPeriodTotalSalary', 'allTimePaidTotal',
            'pendingDrafts', 'pendingApprovals',
            'pendingBonuses', 'approvedBonuses', 'totalBonusAmount',
            'chartData', 'deptData', 'employeeTypeData',
            'recentPayrolls', 'latestPendingBonuses',
            'currentYear', 'currentMonth'
        ));
    }
}
