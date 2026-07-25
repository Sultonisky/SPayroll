<?php

namespace App\Providers;

use App\Models\Bonus;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Position;
use App\Models\User;
use App\Observers\BonusObserver;
use App\Observers\DepartmentObserver;
use App\Observers\EmployeeObserver;
use App\Observers\PayrollObserver;
use App\Observers\PositionObserver;
use App\Observers\UserObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        Department::observe(DepartmentObserver::class);
        Employee::observe(EmployeeObserver::class);
        Payroll::observe(PayrollObserver::class);
        Position::observe(PositionObserver::class);
        Bonus::observe(BonusObserver::class);
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->email.$request->ip());
        });

        // Implicitly grant "Admin" role all permissions
        // Demo accounts are excluded — they go through normal policy checks
        Gate::before(function ($user, $ability) {
            if ($user->isAdmin() && ! $user->isDemo()) {
                return true;
            }
        });
    }
}
