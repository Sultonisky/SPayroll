@extends('layouts.app')
@section('title', 'Dashboard')

@section('contents')

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0 text-primary"><i class="fas fa-home me-2"></i>Dashboard</h4>
        <small class="text-body">{{ \Carbon\Carbon::now()->format('l, d F Y') }}</small>
    </div>
    <div class="text-end">
        <span class="badge bg-primary text-black rounded-pill px-3 py-2 fs-6">
            Period: {{ \Carbon\Carbon::create($currentYear, $currentMonth)->format('F Y') }}
        </span>
    </div>
</div>

{{-- ── ROW 1: Stats Cards ─────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">

    {{-- Total Employees --}}
    <div class="col-6 col-md-3">
        <div class="card border shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary flex-shrink-0">
                    <i class="fas fa-users fa-lg"></i>
                </div>
                <div>
                    <div class="text-body small">Total Employees</div>
                    <div class="fw-bold fs-4 lh-1">{{ $totalEmployees }}</div>
                    <small class="text-success"><i class="fas fa-check-circle me-1"></i>{{ $activeEmployees }} active</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Departments --}}
    <div class="col-6 col-md-3">
        <div class="card border shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-info bg-opacity-10 text-info flex-shrink-0">
                    <i class="fas fa-building fa-lg"></i>
                </div>
                <div>
                    <div class="text-body small">Departments</div>
                    <div class="fw-bold fs-4 lh-1">{{ $totalDepartments }}</div>
                    <small class="text-body">{{ $totalPositions }} positions</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Current Period Total Salary --}}
    <div class="col-6 col-md-3">
        <div class="card border shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success flex-shrink-0">
                    <i class="fas fa-money-bill-wave fa-lg"></i>
                </div>
                <div>
                    <div class="text-body small">Payroll This Month</div>
                    <div class="fw-bold fs-5 lh-1 text-primary">
                        Rp {{ number_format($currentPeriodTotalSalary, 0, ',', '.') }}
                    </div>
                    <small class="text-body">{{ $currentPeriodTotal }} records</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Pending Actions --}}
    <div class="col-6 col-md-3">
        <div class="card border shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning flex-shrink-0">
                    <i class="fas fa-clock fa-lg"></i>
                </div>
                <div>
                    <div class="text-body small">Needs Action</div>
                    <div class="fw-bold fs-4 lh-1">{{ $pendingDrafts + $pendingApprovals }}</div>
                    <small class="text-body">{{ $pendingBonuses }} bonus pending (all time)</small>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ── ROW 2: Payroll Status This Month + All-Time ────────────────────── --}}
<div class="row g-3 mb-4">

    <div class="col-sm-6 col-md-3">
        <div class="card border shadow-sm text-center py-3">
            <div class="card-body py-2">
                <div class="text-body small mb-1">Draft (This Month)</div>
                <div class="fw-bold fs-3 text-secondary">{{ $currentPeriodDrafts }}</div>
                <a href="{{ route('payrolls.drafts', ['year' => $currentYear, 'month' => $currentMonth]) }}" class="btn btn-outline-secondary btn-sm rounded-pill mt-2 px-3">
                    <i class="fas fa-inbox me-1"></i>View
                </a>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-3">
        <div class="card border shadow-sm text-center py-3">
            <div class="card-body py-2">
                <div class="text-body small mb-1">Approved (This Month)</div>
                <div class="fw-bold fs-3 text-info">{{ $currentPeriodApproved }}</div>
                <a href="{{ route('payrolls.approved', ['year' => $currentYear, 'month' => $currentMonth]) }}" class="btn btn-outline-info btn-sm rounded-pill mt-2 px-3">
                    <i class="fas fa-user-check me-1"></i>View
                </a>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-3">
        <div class="card border shadow-sm text-center py-3">
            <div class="card-body py-2">
                <div class="text-body small mb-1">Paid (This Month)</div>
                <div class="fw-bold fs-3 text-success">{{ $currentPeriodPaid }}</div>
                <a href="{{ route('payrolls.index', ['year' => $currentYear, 'month' => $currentMonth]) }}" class="btn btn-outline-success btn-sm rounded-pill mt-2 px-3">
                    <i class="fas fa-check-double me-1"></i>View
                </a>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-3">
        <div class="card border shadow-sm text-center py-3">
            <div class="card-body py-2">
                <div class="text-body small mb-1">Total Paid (All Time)</div>
                <div class="fw-bold fs-3 text-primary">Rp {{ number_format($allTimePaidTotal, 0, ',', '.') }}</div>
                <a href="{{ route('payrolls.periods') }}" class="btn btn-outline-primary btn-sm rounded-pill mt-2 px-3">
                    <i class="fas fa-calendar-check me-1"></i>Periods
                </a>
            </div>
        </div>
    </div>

</div>

{{-- ── ROW 3: Chart Payroll + Donut Department ────────────────────────── --}}
<div class="row g-3 mb-4">

    {{-- Bar Chart: Monthly Payroll Last 6 Months --}}
    <div class="col-lg-7">
        <div class="card border shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-3 pb-0 px-4">
                <h6 class="fw-bold mb-0 text-primary">
                    <i class="fas fa-chart-bar me-2"></i>Payroll - Last 6 Months
                </h6>
                <small class="text-body">Total salary processed per month</small>
            </div>
            <div class="card-body pt-2">
                <canvas id="payrollMonthlyChart" height="220"></canvas>
            </div>
        </div>
    </div>

    {{-- Donut Chart: Employees per Department --}}
    <div class="col-lg-5">
        <div class="card border shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-3 pb-0 px-4">
                <h6 class="fw-bold mb-0 text-primary">
                    <i class="fas fa-chart-pie me-2"></i>Employees by Department
                </h6>
                <small class="text-body">Active employee distribution</small>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center pt-2">
                @if ($deptData->sum('count') > 0)
                    <canvas id="deptDonutChart" height="220"></canvas>
                @else
                    <div class="text-center text-body py-5">
                        <i class="fas fa-building fa-3x mb-3 opacity-25"></i>
                        <p class="mb-0">No department data yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- ── ROW 4: Employee Type + Bonus Stats + Quick Actions ─────────────── --}}
<div class="row g-3 mb-4">

    {{-- Employee Type Cards --}}
    <div class="col-md-4">
        <div class="card border shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-3 pb-0 px-4">
                <h6 class="fw-bold mb-0 text-primary">
                    <i class="fas fa-id-badge me-2"></i>Employee Type
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between border rounded-3 p-3 mb-3">
                    <div>
                        <div class="text-body small">Fulltime</div>
                        <div class="fw-bold fs-4">{{ $fulltimeEmployees }}</div>
                    </div>
                    <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-user-tie fa-lg"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between border rounded-3 p-3 mb-3">
                    <div>
                        <div class="text-body small">Internship</div>
                        <div class="fw-bold fs-4">{{ $internshipEmployees }}</div>
                    </div>
                    <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-user-graduate fa-lg"></i>
                    </div>
                </div>
                <div class="rounded-3 p-3">
                    <div class="fw-bold text-body">Total Employees</div>
                    <div class="fw-bold fs-4 text-primary">{{ $totalEmployees }} <span class="text-body small">Employees</span></div>
                </div>
                @if ($newEmployeesThisMonth > 0)
                    <div class="alert alert-success py-2 mb-0 rounded-3 small">
                        <i class="fas fa-user-plus me-1"></i>
                        <strong>{{ $newEmployeesThisMonth }}</strong> new employee(s) joined this month.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Bonus Stats --}}
    <div class="col-md-4">
        <div class="card border shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-3 pb-0 px-4">
                <h6 class="fw-bold mb-0 text-primary">
                    <i class="fas fa-comments-dollar me-2"></i>Bonus Overview
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between border rounded-3 p-3 mb-3">
                    <div>
                        <div class="text-body small">Pending Approval (All Time)</div>
                        <div class="fw-bold fs-4 text-warning">{{ $pendingBonuses }}</div>
                    </div>
                    <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-hourglass-half fa-lg"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between border rounded-3 p-3 mb-3">
                    <div>
                        <div class="text-body small">Approved (All Time)</div>
                        <div class="fw-bold fs-4 text-success">{{ $approvedBonuses }}</div>
                    </div>
                    <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success">
                        <i class="fas fa-check-circle fa-lg"></i>
                    </div>
                </div>
                <div class=" rounded-3 p-3">
                    <div class="fw-bold text-body">Total Approved Bonus (All Time)</div>
                    <div class="fw-bold fs-4 text-primary">Rp {{ number_format($totalBonusAmount, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="col-md-4">
        <div class="card border shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-3 pb-0 px-4">
                <h6 class="fw-bold mb-0 text-primary">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h6>
            </div>
            <div class="card-body d-flex flex-column gap-2">
                @if (auth()->user()->isAdmin() || auth()->user()->role === 'HR')
                    <a href="{{ route('payrolls.generate') }}"
                        class="btn btn-primary rounded-pill fw-bold shadow-sm">
                        <i class="fas fa-play-circle me-2"></i>Generate Payroll
                    </a>
                    <a href="{{ route('payrolls.drafts') }}"
                        class="btn btn-outline-secondary rounded-pill shadow-sm">
                        <i class="fas fa-inbox me-2"></i>View Draft Payrolls
                        @if ($pendingDrafts > 0)
                            <span class="badge bg-secondary ms-1">{{ $pendingDrafts }}</span>
                        @endif
                    </a>
                    <a href="{{ route('payrolls.approved') }}"
                        class="btn btn-outline-info rounded-pill shadow-sm">
                        <i class="fas fa-user-check me-2"></i>Approved Payrolls
                        @if ($pendingApprovals > 0)
                            <span class="badge bg-info ms-1">{{ $pendingApprovals }}</span>
                        @endif
                    </a>
                    <a href="{{ route('bonuses.index') }}"
                        class="btn btn-outline-warning rounded-pill shadow-sm">
                        <i class="fas fa-comments-dollar me-2"></i>Manage Bonuses
                        @if ($pendingBonuses > 0)
                            <span class="badge bg-warning text-dark ms-1">{{ $pendingBonuses }}</span>
                        @endif
                    </a>
                @endif
                <a href="{{ route('employees.index') }}"
                    class="btn btn-outline-primary rounded-pill shadow-sm">
                    <i class="fas fa-users me-2"></i>Employee Data
                </a>
                <a href="{{ route('payrolls.periods') }}"
                    class="btn btn-outline-success rounded-pill shadow-sm">
                    <i class="fas fa-calendar-check me-2"></i>Payroll History
                </a>
            </div>
        </div>
    </div>

</div>

{{-- ── ROW 5: Recent Payrolls + Pending Bonuses ───────────────────────── --}}
<div class="row g-3 mb-5">

    {{-- Recent Payroll Activity --}}
    <div class="col-lg-7">
        <div class="card border shadow-sm">
            <div class="card-header bg-transparent border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 text-primary">
                    <i class="fas fa-history me-2"></i>Recent Payroll Activity
                </h6>
                <a href="{{ route('payrolls.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    View All
                </a>
            </div>
            <div class="card-body p-0">
                @forelse ($recentPayrolls as $payroll)
                    <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:38px;height:38px;">
                            <i class="fas fa-file-invoice-dollar small"></i>
                        </div>
                        <div class="flex-grow-1 min-width-0">
                            <div class="fw-semibold text-truncate">{{ $payroll->employee?->name ?? '-' }}</div>
                            <small class="text-muted font-monospace">{{ $payroll->employee?->employee_code ?? '' }} - </small>
                            <small class="text-muted" style="font-size:.7rem;">
                                {{ \Carbon\Carbon::create($payroll->year, $payroll->month)->format('M Y') }}
                            </small>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <div class="small fw-bold">
                                Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}
                            </div>
                            <div>
                                @php
                                    $badgeClass = match($payroll->status) {
                                        'paid'     => 'bg-success',
                                        'approved' => 'bg-info',
                                        default    => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} rounded-pill px-2" style="font-size:.7rem;">
                                    {{ ucfirst($payroll->status) }}
                                </span>
                            </div>
                            
                        </div>
                    </div>
                @empty
                    <div class="text-center text-body py-5">
                        <i class="fas fa-inbox fa-2x mb-2 opacity-25"></i>
                        <p class="mb-0 small">No payroll records yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Pending Bonuses --}}
    <div class="col-lg-5">
        <div class="card border shadow-sm">
            <div class="card-header bg-transparent border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 text-primary">
                    <i class="fas fa-hourglass-half me-2"></i>Bonuses Awaiting Approval
                </h6>
                <a href="{{ route('bonuses.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    View All
                </a>
            </div>
            <div class="card-body p-0">
                @forelse ($latestPendingBonuses as $bonus)
                    <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                        <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:38px;height:38px;">
                            <i class="fas fa-gift small"></i>
                        </div>
                        <div class="flex-grow-1 min-width-0">
                            <div class="fw-semibold text-truncate">{{ $bonus->employee?->name ?? '-' }}</div>
                            <small class="text-muted">{{ ucfirst($bonus->type) }} - {{ \Carbon\Carbon::create($bonus->year, $bonus->month)->format('M Y') }}</small>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <div class="small fw-bold text-warning">
                                Rp {{ number_format($bonus->amount, 0, ',', '.') }}
                            </div>
                            <span class="badge bg-warning text-dark fw-bold rounded-pill px-2" style="font-size:.7rem;">Pending</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-body py-5">
                        <i class="fas fa-check-circle fa-2x mb-2 opacity-25 text-success"></i>
                        <p class="mb-0 small">No pending bonuses.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Chart 1: Monthly Payroll Bar Chart ───────────────────────────
    const monthlyCtx = document.getElementById('payrollMonthlyChart');
    if (monthlyCtx) {
        const chartData = @json($chartData);
        const labels    = chartData.map(d => d.label);
        const totals    = chartData.map(d => d.total);
        const paids     = chartData.map(d => d.paid);

        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Total Processed',
                        data: totals,
                        backgroundColor: 'rgba(50, 115, 220, 0.25)',
                        borderColor: 'rgba(50, 115, 220, 0.8)',
                        borderWidth: 2,
                        borderRadius: 6,
                    },
                    {
                        label: 'Paid',
                        data: paids,
                        backgroundColor: 'rgba(35, 183, 100, 0.7)',
                        borderColor: 'rgba(35, 183, 100, 1)',
                        borderWidth: 2,
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                return ' Rp ' + ctx.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (val) {
                                if (val >= 1_000_000) return 'Rp ' + (val / 1_000_000).toFixed(1) + 'M';
                                return 'Rp ' + val.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    }

    // ── Chart 2: Department Donut Chart ──────────────────────────────
    const deptCtx = document.getElementById('deptDonutChart');
    if (deptCtx) {
        const deptData = @json($deptData);
        const palette  = [
            '#3273dc','#23b764','#ff6b35','#f7c948',
            '#9b59b6','#1abc9c','#e74c3c','#2ecc71'
        ];

        new Chart(deptCtx, {
            type: 'doughnut',
            data: {
                labels: deptData.map(d => d.name),
                datasets: [{
                    data: deptData.map(d => d.count),
                    backgroundColor: deptData.map((_, i) => palette[i % palette.length]),
                    borderWidth: 2,
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, padding: 10, font: { size: 11 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                return ' ' + ctx.label + ': ' + ctx.parsed + ' people';
                            }
                        }
                    }
                }
            }
        });
    }

});
</script>
@endpush
