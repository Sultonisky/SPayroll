<div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">
    <div class="sidebar-header border-bottom px-4" style="height: 64px;">
        <div class="sidebar-brand">
            <div class="sidebar-brand-full d-flex align-items-center">
                <span class="fw-bold">S-Payroll <span class="text-primary">Dashboard</span></span>
            </div>
            <div class="sidebar-brand-narrow">
                <i class="fas fa-calculator" style="font-size: 1.5rem; color: var(--cui-primary);"></i>
            </div>
        </div>
        <button class="btn-close d-lg-none" type="button" data-coreui-dismiss="offcanvas" data-coreui-theme="dark" aria-label="Close" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()"></button>
    </div>
    
    <ul class="sidebar-nav" data-coreui="navigation" data-simplebar>
        {{-- Dashboard --}}
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="nav-icon fas fa-home"></i>
                Dashboard
            </a>
        </li>

        <li class="nav-title">Master Data</li>

        <!-- Users -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('users.index') }}">
                <i class="nav-icon fas fa-user-lock"></i>
                Users
            </a>
        </li>

        <!-- Departments -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('departments.index') }}">
                <i class="nav-icon fas fa-building"></i>
                Departments
            </a>
        </li>

        <!-- Positions -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('positions.index') }}">
                <i class="nav-icon fas fa-briefcase"></i>
                Positions
            </a>
        </li>

        <!-- Employees -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('employees.index') }}">
                <i class="nav-icon fas fa-users"></i>
                Employees
            </a>
        </li>

        <li class="nav-title">Payroll</li>

        <!-- Attendances -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('attendances.index') }}">
                <i class="nav-icon fas fa-calendar-check"></i>
                Attendances
            </a>
        </li>

        <!-- Payrolls -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('payrolls.index') }}">
                <i class="nav-icon fas fa-money-check-alt"></i>
                Payrolls
            </a>
        </li>
    </ul>
    
    <div class="sidebar-footer border-top d-none d-md-flex">
        <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
    </div>
</div>