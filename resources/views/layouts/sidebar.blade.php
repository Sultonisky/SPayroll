@php
    $user = auth()->user();
    $role = $user?->role;
    $isDemo = $user?->isDemo();
    $isAdmin = $role === 'admin';
    $isHR = $role === 'HR';
    $isManager = $role === 'manager';
    $isStaff = $role === 'staff';
    $isFinance = $role === 'finance';
    $canPayroll = in_array($role, ['admin', 'HR', 'manager', 'staff', 'finance']);
    $canBonus = in_array($role, ['admin', 'HR', 'manager', 'finance']);
    $canDept = in_array($role, ['admin', 'HR', 'manager']);
    $canGenerate = in_array($role, ['admin', 'HR', 'finance']);
@endphp

<div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">
    <div class="sidebar-header border-bottom px-4" style="height: 64px;">
        <div class="sidebar-brand">
            <div class="sidebar-brand-full d-flex align-items-center">
                <span class="fw-bold">Dashboard <span class="text-primary">{{ Brand::name() }}</span></span>
            </div>
            <div class="sidebar-brand-narrow">
                <i class="fas fa-calculator" style="font-size: 1.5rem; color: var(--cui-primary);"></i>
            </div>
        </div>
        <button class="btn-close d-lg-none" type="button" data-coreui-dismiss="offcanvas" data-coreui-theme="dark"
            aria-label="Close"
            onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()"></button>
    </div>

    <ul class="sidebar-nav" data-coreui="navigation" data-simplebar>

        {{-- Dashboard --}}
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="nav-icon fas fa-home"></i>
                Dashboard
            </a>
        </li>

        {{-- ── Master Data ─────────────────────────────── --}}
        <li class="nav-title">Master Data</li>

        {{-- Departments: admin, HR, manager --}}
        @if ($canDept)
            <li class="nav-item">
                <a class="nav-link" href="{{ route('departments.index') }}">
                    <i class="nav-icon fas fa-building"></i>
                    Departments
                </a>
            </li>
        @endif

        {{-- Positions: admin, HR, manager --}}
        @if ($canDept)
            <li class="nav-item">
                <a class="nav-link" href="{{ route('positions.index') }}">
                    <i class="nav-icon fas fa-briefcase"></i>
                    Positions
                </a>
            </li>
        @endif

        {{-- Employees: all roles --}}
        <li class="nav-item">
            <a class="nav-link" href="{{ route('employees.index') }}">
                <i class="nav-icon fas fa-users"></i>
                Employees
            </a>
        </li>

        {{-- Employee Bonus: admin, HR, manager --}}
        @if ($canBonus)
            <li class="nav-item">
                <a class="nav-link" href="{{ route('bonuses.index') }}">
                    <i class="nav-icon fas fa-gift"></i>
                    Employee Bonus
                </a>
            </li>
        @endif

        {{-- ── Payroll System ───────────────────────────── --}}
        @if ($canPayroll)
            <li class="nav-title">Payroll System</li>

            {{-- Generate: admin, HR only --}}
            @if ($canGenerate)
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('payrolls.generate') }}">
                        <i class="nav-icon fas fa-play-circle"></i>
                        Generate
                    </a>
                </li>
            @endif

            {{-- Drafts: admin, HR, manager, staff --}}
            <li class="nav-item">
                <a class="nav-link" href="{{ route('payrolls.drafts') }}">
                    <i class="nav-icon fas fa-inbox"></i>
                    Drafts
                </a>
            </li>

            {{-- Approved: admin, HR, manager, staff --}}
            <li class="nav-item">
                <a class="nav-link" href="{{ route('payrolls.approved') }}">
                    <i class="nav-icon fas fa-user-check"></i>
                    Approved
                </a>
            </li>

            {{-- Periods: admin, HR, manager, staff --}}
            <li class="nav-item">
                <a class="nav-link" href="{{ route('payrolls.periods') }}">
                    <i class="nav-icon fas fa-calendar-check"></i>
                    Periods
                </a>
            </li>

            {{-- Records (Paid): admin, HR, manager, staff --}}
            <li class="nav-item">
                <a class="nav-link" href="{{ route('payrolls.index') }}">
                    <i class="nav-icon fas fa-file-invoice-dollar"></i>
                    Records (Paid)
                </a>
            </li>

            {{-- Payslip: admin, HR, finance (all) + staff (own) — manager excluded --}}
            @if (in_array($role, ['admin', 'HR', 'finance', 'staff']))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('payrolls.payslip') }}">
                        <i class="nav-icon fas fa-file-invoice"></i>
                        Payslip
                    </a>
                </li>
            @endif
        @endif

         {{-- Users: admin only, hidden for demo --}}
        @if ($isAdmin && !$isDemo)
            <li class="nav-item">
                <a class="nav-link" href="{{ route('users.index') }}">
                    <i class="nav-icon fas fa-user-lock"></i>
                    Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('audit-logs.index') }}">
                    <i class="nav-icon fas fa-shield-alt"></i>
                    Audit Log
                </a>
            </li>
        @endif

    </ul>

    <div class="sidebar-footer border-top d-none d-md-flex">
        <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
    </div>
</div>
