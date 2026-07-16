<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\DepartmentController;
use App\Http\Controllers\Dashboard\PositionController;
use App\Http\Controllers\Dashboard\EmployeeController;
use App\Http\Controllers\Dashboard\AttendanceController;
use App\Http\Controllers\Dashboard\PayrollController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Auth Routes
Route::get('admin/login', [AuthController::class, 'loginForm'])
    ->name('login')
    ->middleware('guest');

Route::post('admin/login', [AuthController::class, 'authLogin'])
    ->name('admin.login')
    ->middleware(['guest', 'throttle:login']);

Route::post('admin/logout', [AuthController::class, 'logout'])
    ->name('admin.logout')
    ->middleware('auth');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('welcome');
    })->name('admin.dashboard');

    // User Routes - Admin Only
    Route::middleware(['role:admin'])->group(function () {
        Route::get('users/trash', [UserController::class, 'trash'])->name('users.trash');
        Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
        Route::delete('users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.force-delete');
        Route::get('users/{id}/export', [UserController::class, 'export'])->name('users.export');
        Route::resource('users', UserController::class);
    });

    // Department Routes - Admin & HR & Manager (read for staff)
    Route::middleware(['role:admin,HR,manager'])->group(function () {
        Route::get('departments/trash', [DepartmentController::class, 'trash'])->name('departments.trash');
        Route::get('departments/{id}/export', [DepartmentController::class, 'export'])->name('departments.export');
        Route::resource('departments', DepartmentController::class)->only(['index', 'show']);
    });
    
    Route::middleware(['role:admin,HR'])->group(function () {
        Route::post('departments/{id}/restore', [DepartmentController::class, 'restore'])->name('departments.restore');
        Route::delete('departments/{id}/force-delete', [DepartmentController::class, 'forceDelete'])->name('departments.force-delete');
        Route::resource('departments', DepartmentController::class)->except(['index', 'show']);
    });

    // Position Routes - Admin & HR & Manager (read for staff)
    Route::middleware(['role:admin,HR,manager'])->group(function () {
        Route::get('positions/trash', [PositionController::class, 'trash'])->name('positions.trash');
        Route::get('positions/{id}/export', [PositionController::class, 'export'])->name('positions.export');
        Route::resource('positions', PositionController::class)->only(['index', 'show']);
    });
    
    Route::middleware(['role:admin,HR'])->group(function () {
        Route::post('positions/{id}/restore', [PositionController::class, 'restore'])->name('positions.restore');
        Route::delete('positions/{id}/force-delete', [PositionController::class, 'forceDelete'])->name('positions.force-delete');
        Route::resource('positions', PositionController::class)->except(['index', 'show']);
    });

    // Employee Routes - Admin & HR & Manager (read for staff)
    Route::middleware(['role:admin,HR,manager,staff'])->group(function () {
        Route::get('employees/trash', [EmployeeController::class, 'trash'])->name('employees.trash');
        Route::get('employees/{id}/export', [EmployeeController::class, 'export'])->name('employees.export');
        Route::resource('employees', EmployeeController::class)->only(['index', 'show']);
    });
    
    Route::middleware(['role:admin,HR,manager'])->group(function () {
        Route::resource('employees', EmployeeController::class)->only(['create', 'store', 'edit', 'update']);
    });
    
    Route::middleware(['role:admin,HR'])->group(function () {
        Route::post('employees/{id}/restore', [EmployeeController::class, 'restore'])->name('employees.restore');
        Route::delete('employees/{id}/force-delete', [EmployeeController::class, 'forceDelete'])->name('employees.force-delete');
        Route::resource('employees', EmployeeController::class)->only(['destroy']);
    });

    // Attendance Routes - Admin & HR & Manager (read for staff)
    Route::middleware(['role:admin,HR,manager,staff'])->group(function () {
        Route::get('attendances/trash', [AttendanceController::class, 'trash'])->name('attendances.trash');
        Route::get('attendances/{id}/export', [AttendanceController::class, 'export'])->name('attendances.export');
        Route::resource('attendances', AttendanceController::class)->only(['index', 'show']);
    });
    
    Route::middleware(['role:admin,HR,manager'])->group(function () {
        Route::resource('attendances', AttendanceController::class)->only(['create', 'store', 'edit', 'update']);
    });
    
    Route::middleware(['role:admin,HR'])->group(function () {
        Route::post('attendances/{id}/restore', [AttendanceController::class, 'restore'])->name('attendances.restore');
        Route::delete('attendances/{id}/force-delete', [AttendanceController::class, 'forceDelete'])->name('attendances.force-delete');
        Route::resource('attendances', AttendanceController::class)->only(['destroy']);
    });

    // Payroll Routes - Admin & HR & Manager (read for staff)
    Route::middleware(['role:admin,HR,manager,staff'])->group(function () {
        Route::get('payrolls/trash', [PayrollController::class, 'trash'])->name('payrolls.trash');
        Route::get('payrolls/{id}/export', [PayrollController::class, 'export'])->name('payrolls.export');
        Route::resource('payrolls', PayrollController::class)->only(['index', 'show']);
    });
    
    Route::middleware(['role:admin,HR,manager'])->group(function () {
        Route::resource('payrolls', PayrollController::class)->only(['create', 'store', 'edit', 'update']);
    });
    
    Route::middleware(['role:admin,HR'])->group(function () {
        Route::post('payrolls/{id}/restore', [PayrollController::class, 'restore'])->name('payrolls.restore');
        Route::delete('payrolls/{id}/force-delete', [PayrollController::class, 'forceDelete'])->name('payrolls.force-delete');
        Route::resource('payrolls', PayrollController::class)->only(['destroy']);
    });
});
