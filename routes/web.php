<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\DepartmentController;
use App\Http\Controllers\Dashboard\PositionController;
use App\Http\Controllers\Dashboard\EmployeeController;
use App\Http\Controllers\Dashboard\AttendanceImportController;
use App\Http\Controllers\Dashboard\AttendanceRecordController;
use App\Http\Controllers\Dashboard\AttendanceAdjustmentController;
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
    });
    
    Route::middleware(['role:admin,HR'])->group(function () {
        Route::get('departments/create', [DepartmentController::class, 'create'])->name('departments.create');
        Route::post('departments', [DepartmentController::class, 'store'])->name('departments.store');
        Route::get('departments/{department}/edit', [DepartmentController::class, 'edit'])->name('departments.edit');
        Route::put('departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
        Route::patch('departments/{department}', [DepartmentController::class, 'update']);
        Route::delete('departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
        Route::post('departments/{id}/restore', [DepartmentController::class, 'restore'])->name('departments.restore');
        Route::delete('departments/{id}/force-delete', [DepartmentController::class, 'forceDelete'])->name('departments.force-delete');
    });
    
    Route::middleware(['role:admin,HR,manager'])->group(function () {
        Route::get('departments', [DepartmentController::class, 'index'])->name('departments.index');
        Route::get('departments/{department}', [DepartmentController::class, 'show'])->name('departments.show');
        Route::get('departments/{id}/export', [DepartmentController::class, 'export'])->name('departments.export');
    });

    // Position Routes - Admin & HR & Manager (read for staff)
    Route::middleware(['role:admin,HR,manager'])->group(function () {
        Route::get('positions/trash', [PositionController::class, 'trash'])->name('positions.trash');
    });
    
    Route::middleware(['role:admin,HR'])->group(function () {
        Route::get('positions/create', [PositionController::class, 'create'])->name('positions.create');
        Route::post('positions', [PositionController::class, 'store'])->name('positions.store');
        Route::get('positions/{position}/edit', [PositionController::class, 'edit'])->name('positions.edit');
        Route::put('positions/{position}', [PositionController::class, 'update'])->name('positions.update');
        Route::patch('positions/{position}', [PositionController::class, 'update']);
        Route::delete('positions/{position}', [PositionController::class, 'destroy'])->name('positions.destroy');
        Route::post('positions/{id}/restore', [PositionController::class, 'restore'])->name('positions.restore');
        Route::delete('positions/{id}/force-delete', [PositionController::class, 'forceDelete'])->name('positions.force-delete');
    });
    
    Route::middleware(['role:admin,HR,manager'])->group(function () {
        Route::get('positions', [PositionController::class, 'index'])->name('positions.index');
        Route::get('positions/{position}', [PositionController::class, 'show'])->name('positions.show');
        Route::get('positions/{id}/export', [PositionController::class, 'export'])->name('positions.export');
    });

    // Employee Routes - Admin & HR & Manager (read for staff)
    Route::middleware(['role:admin,HR,manager,staff'])->group(function () {
        Route::get('employees/trash', [EmployeeController::class, 'trash'])->name('employees.trash');
    });
    
    Route::middleware(['role:admin,HR,manager'])->group(function () {
        Route::get('employees/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('employees', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::patch('employees/{employee}', [EmployeeController::class, 'update']);
    });
    
    Route::middleware(['role:admin,HR'])->group(function () {
        Route::delete('employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
        Route::post('employees/{id}/restore', [EmployeeController::class, 'restore'])->name('employees.restore');
        Route::delete('employees/{id}/force-delete', [EmployeeController::class, 'forceDelete'])->name('employees.force-delete');
    });
    
    Route::middleware(['role:admin,HR,manager,staff'])->group(function () {
        Route::get('employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
        Route::get('employees/{id}/export', [EmployeeController::class, 'export'])->name('employees.export');
    });

    // Attendance Import Routes
    Route::middleware(['role:admin,HR,manager'])->group(function () {
        Route::get('attendance-imports/template', [AttendanceImportController::class, 'downloadTemplate'])->name('attendance-imports.template');
        Route::get('attendance-imports', [AttendanceImportController::class, 'index'])->name('attendance-imports.index');
        Route::get('attendance-imports/create', [AttendanceImportController::class, 'create'])->name('attendance-imports.create');
        Route::post('attendance-imports/preview', [AttendanceImportController::class, 'preview'])->name('attendance-imports.preview');
        Route::post('attendance-imports', [AttendanceImportController::class, 'store'])->name('attendance-imports.store');
        Route::get('attendance-imports/{attendanceImport}', [AttendanceImportController::class, 'show'])->name('attendance-imports.show');
    });

    // Attendance Record Routes
    Route::middleware(['role:admin,HR,manager,staff'])->group(function () {
        Route::get('attendance-records', [AttendanceRecordController::class, 'index'])->name('attendance-records.index');
        Route::get('attendance-records/{attendanceRecord}', [AttendanceRecordController::class, 'show'])->name('attendance-records.show');
    });

    Route::middleware(['role:admin,HR'])->group(function () {
        Route::get('attendance-records/{attendanceRecord}/edit', [AttendanceRecordController::class, 'edit'])->name('attendance-records.edit');
        Route::put('attendance-records/{attendanceRecord}', [AttendanceRecordController::class, 'update'])->name('attendance-records.update');
        Route::patch('attendance-records/{attendanceRecord}', [AttendanceRecordController::class, 'update']);
    });

    // Attendance Adjustment Routes
    Route::middleware(['role:admin,HR,manager,staff'])->group(function () {
        Route::get('attendance-adjustments', [AttendanceAdjustmentController::class, 'index'])->name('attendance-adjustments.index');
        Route::get('attendance-adjustments/{attendanceAdjustment}', [AttendanceAdjustmentController::class, 'show'])->name('attendance-adjustments.show');
    });

    Route::middleware(['role:admin,HR,manager'])->group(function () {
        Route::get('attendance-records/{attendanceRecord}/adjustments/create', [AttendanceAdjustmentController::class, 'create'])->name('attendance-adjustments.create');
        Route::post('attendance-records/{attendanceRecord}/adjustments', [AttendanceAdjustmentController::class, 'store'])->name('attendance-adjustments.store');
    });

    Route::middleware(['role:admin,HR'])->group(function () {
        Route::post('attendance-adjustments/{attendanceAdjustment}/approve', [AttendanceAdjustmentController::class, 'approve'])->name('attendance-adjustments.approve');
        Route::post('attendance-adjustments/{attendanceAdjustment}/reject', [AttendanceAdjustmentController::class, 'reject'])->name('attendance-adjustments.reject');
    });

    // Payroll Routes - Admin & HR & Manager (read for staff)
    Route::middleware(['role:admin,HR,manager,staff'])->group(function () {
        Route::get('payrolls/trash', [PayrollController::class, 'trash'])->name('payrolls.trash');
    });
    
    Route::middleware(['role:admin,HR,manager'])->group(function () {
        Route::get('payrolls/create', [PayrollController::class, 'create'])->name('payrolls.create');
        Route::post('payrolls', [PayrollController::class, 'store'])->name('payrolls.store');
        Route::get('payrolls/{payroll}/edit', [PayrollController::class, 'edit'])->name('payrolls.edit');
        Route::put('payrolls/{payroll}', [PayrollController::class, 'update'])->name('payrolls.update');
        Route::patch('payrolls/{payroll}', [PayrollController::class, 'update']);
    });
    
    Route::middleware(['role:admin,HR'])->group(function () {
        Route::delete('payrolls/{payroll}', [PayrollController::class, 'destroy'])->name('payrolls.destroy');
        Route::post('payrolls/{id}/restore', [PayrollController::class, 'restore'])->name('payrolls.restore');
        Route::delete('payrolls/{id}/force-delete', [PayrollController::class, 'forceDelete'])->name('payrolls.force-delete');
    });
    
    Route::middleware(['role:admin,HR,manager,staff'])->group(function () {
        Route::get('payrolls', [PayrollController::class, 'index'])->name('payrolls.index');
        Route::get('payrolls/{payroll}', [PayrollController::class, 'show'])->name('payrolls.show');
        Route::get('payrolls/{id}/export', [PayrollController::class, 'export'])->name('payrolls.export');
    });
});
