<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', Employee::class);
        $employees = Employee::select('id', 'nik', 'name', 'email', 'department_id', 'position_id', 'status', 'created_at')
            ->with(['department', 'position'])
            ->latest()
            ->get();
            
        return view('dashboard.employees.index', compact('employees'));
    }

    /**
     * Display a listing of deleted resources.
     */
    public function trash()
    {
        Gate::authorize('viewAny', Employee::class);
        $employees = Employee::onlyTrashed()
            ->select('id', 'nik', 'name', 'email', 'department_id', 'position_id', 'status', 'created_at')
            ->with(['department', 'position'])
            ->latest()
            ->get();
            
        return view('dashboard.employees.index', compact('employees'))->with('isTrash', true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', Employee::class);
        $departments = Department::all();
        $positions = Position::all();
        $users = User::all();
        return view('dashboard.employees.create', compact('departments', 'positions', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Employee::class);
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'nik' => 'required|string|max:50|unique:employees,nik',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'join_date' => 'required|date',
            'birth_date' => 'nullable|date',
            'status' => 'required|in:active,inactive',
            'base_salary' => 'required|numeric|min:0',
        ]);

        Employee::create($validated);

        return redirect()->route('employees.index')->with('success', 'Berhasil menambahkan karyawan baru.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employee = Employee::withTrashed()->with(['department', 'position', 'user'])->findOrFail($id);
        Gate::authorize('view', $employee);
        return view('dashboard.employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $employee = Employee::findOrFail($id);
        Gate::authorize('update', $employee);
        $departments = Department::all();
        $positions = Position::all();
        $users = User::all();
        return view('dashboard.employees.edit', compact('employee', 'departments', 'positions', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $employee = Employee::findOrFail($id);
        Gate::authorize('update', $employee);
        
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'nik' => 'required|string|max:50|unique:employees,nik,' . $id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'join_date' => 'required|date',
            'birth_date' => 'nullable|date',
            'status' => 'required|in:active,inactive',
            'base_salary' => 'required|numeric|min:0',
        ]);

        $employee->update($validated);

        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $employee = Employee::findOrFail($id);
        Gate::authorize('delete', $employee);
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Karyawan dipindahkan ke tempat sampah.');
    }

    /**
     * Restore the specified deleted resource.
     */
    public function restore(string $id)
    {
        $employee = Employee::onlyTrashed()->findOrFail($id);
        Gate::authorize('restore', $employee);
        $employee->restore();

        return redirect()->route('employees.trash')->with('success', 'Karyawan berhasil dipulihkan.');
    }

    /**
     * Permanently delete the specified resource.
     */
    public function forceDelete(string $id)
    {
        $employee = Employee::onlyTrashed()->findOrFail($id);
        Gate::authorize('forceDelete', $employee);
        $employee->forceDelete();

        return redirect()->route('employees.trash')->with('success', 'Karyawan dihapus secara permanen.');
    }

    /**
     * Export single employee to CSV.
     */
    public function export(string $id)
    {
        $employee = Employee::withTrashed()->with(['department', 'position', 'user'])->findOrFail($id);
        Gate::authorize('view', $employee);

        $fileName = 'employee_' . $employee->id . '_' . date('Y-m-d') . '.csv';
        $headers  = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($employee) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['Field', 'Value']);
            
            // Employee details
            fputcsv($file, ['ID', $employee->id]);
            fputcsv($file, ['NIK', $employee->nik]);
            fputcsv($file, ['Nama Karyawan', $employee->name]);
            fputcsv($file, ['Email', $employee->email]);
            fputcsv($file, ['Telepon', $employee->phone]);
            fputcsv($file, ['Alamat', $employee->address]);
            fputcsv($file, ['Tanggal Bergabung', $employee->join_date->format('Y-m-d')]);
            fputcsv($file, ['Tanggal Lahir', $employee->birth_date ? $employee->birth_date->format('Y-m-d') : '-']);
            fputcsv($file, ['Status', $employee->status]);
            fputcsv($file, ['Gaji Pokok', $employee->base_salary]);
            fputcsv($file, ['Departemen', $employee->department ? $employee->department->name : '-']);
            fputcsv($file, ['Jabatan', $employee->position ? $employee->position->name : '-']);
            fputcsv($file, ['User', $employee->user ? $employee->user->name : '-']);
            fputcsv($file, ['Tanggal Dibuat', $employee->created_at->format('Y-m-d H:i:s')]);
            fputcsv($file, ['Tanggal Diperbarui', $employee->updated_at->format('Y-m-d H:i:s')]);
            if ($employee->deleted_at) {
                fputcsv($file, ['Tanggal Dihapus', $employee->deleted_at->format('Y-m-d H:i:s')]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
