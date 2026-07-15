<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', Department::class);
        $departments = Department::select('id', 'name', 'description', 'created_at')
            ->latest()
            ->get();
            
        return view('dashboard.departments.index', compact('departments'));
    }

    /**
     * Display a listing of deleted resources.
     */
    public function trash()
    {
        Gate::authorize('viewAny', Department::class);
        $departments = Department::onlyTrashed()
            ->select('id', 'name', 'description', 'created_at')
            ->latest()
            ->get();
            
        return view('dashboard.departments.index', compact('departments'))->with('isTrash', true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', Department::class);
        return view('dashboard.departments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Department::class);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Department::create($validated);

        return redirect()->route('departments.index')->with('success', 'Berhasil menambahkan departemen baru.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $department = Department::withTrashed()->findOrFail($id);
        Gate::authorize('view', $department);
        return view('dashboard.departments.show', compact('department'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $department = Department::findOrFail($id);
        Gate::authorize('update', $department);
        return view('dashboard.departments.edit', compact('department'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $department = Department::findOrFail($id);
        Gate::authorize('update', $department);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $department->update($validated);

        return redirect()->route('departments.index')->with('success', 'Departemen berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $department = Department::findOrFail($id);
        Gate::authorize('delete', $department);
        $department->delete();

        return redirect()->route('departments.index')->with('success', 'Departemen dipindahkan ke tempat sampah.');
    }

    /**
     * Restore the specified deleted resource.
     */
    public function restore(string $id)
    {
        $department = Department::onlyTrashed()->findOrFail($id);
        Gate::authorize('restore', $department);
        $department->restore();

        return redirect()->route('departments.trash')->with('success', 'Departemen berhasil dipulihkan.');
    }

    /**
     * Permanently delete the specified resource.
     */
    public function forceDelete(string $id)
    {
        $department = Department::onlyTrashed()->findOrFail($id);
        Gate::authorize('forceDelete', $department);
        $department->forceDelete();

        return redirect()->route('departments.trash')->with('success', 'Departemen dihapus secara permanen.');
    }

    /**
     * Export single department to CSV.
     */
    public function export(string $id)
    {
        $department = Department::withTrashed()->findOrFail($id);
        Gate::authorize('view', $department);

        $fileName = 'department_' . $department->id . '_' . date('Y-m-d') . '.csv';
        $headers  = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($department) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['Field', 'Value']);
            
            // Department details
            fputcsv($file, ['ID', $department->id]);
            fputcsv($file, ['Nama Departemen', $department->name]);
            fputcsv($file, ['Deskripsi', $department->description]);
            fputcsv($file, ['Tanggal Dibuat', $department->created_at->format('Y-m-d H:i:s')]);
            fputcsv($file, ['Tanggal Diperbarui', $department->updated_at->format('Y-m-d H:i:s')]);
            if ($department->deleted_at) {
                fputcsv($file, ['Tanggal Dihapus', $department->deleted_at->format('Y-m-d H:i:s')]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
