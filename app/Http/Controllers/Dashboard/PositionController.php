<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', Position::class);
        $positions = Position::select('id', 'name', 'description', 'base_salary', 'created_at')
            ->latest()
            ->get();
            
        return view('dashboard.positions.index', compact('positions'));
    }

    /**
     * Display a listing of deleted resources.
     */
    public function trash()
    {
        Gate::authorize('viewAny', Position::class);
        $positions = Position::onlyTrashed()
            ->select('id', 'name', 'description', 'base_salary', 'created_at')
            ->latest()
            ->get();
            
        return view('dashboard.positions.index', compact('positions'))->with('isTrash', true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', Position::class);
        return view('dashboard.positions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Position::class);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_salary' => 'required|numeric|min:0',
        ]);

        Position::create($validated);

        return redirect()->route('positions.index')->with('success', 'Success create new position data.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $position = Position::withTrashed()->findOrFail($id);
        Gate::authorize('view', $position);
        return view('dashboard.positions.show', compact('position'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $position = Position::findOrFail($id);
        Gate::authorize('update', $position);
        return view('dashboard.positions.edit', compact('position'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $position = Position::findOrFail($id);
        Gate::authorize('update', $position);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_salary' => 'required|numeric|min:0',
        ]);

        $position->update($validated);

        return redirect()->route('positions.index')->with('success', 'Success update position data.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $position = Position::findOrFail($id);
        Gate::authorize('delete', $position);
        $position->delete();

        return redirect()->route('positions.index')->with('success', 'Success move position data to trash.');
    }

    /**
     * Restore the specified deleted resource.
     */
    public function restore(string $id)
    {
        $position = Position::onlyTrashed()->findOrFail($id);
        Gate::authorize('restore', $position);
        $position->restore();

        return redirect()->route('positions.trash')->with('success', 'Success restore position data.');
    }

    /**
     * Permanently delete the specified resource.
     */
    public function forceDelete(string $id)
    {
        $position = Position::onlyTrashed()->findOrFail($id);
        Gate::authorize('forceDelete', $position);
        $position->forceDelete();

        return redirect()->route('positions.trash')->with('success', 'Success permanently delete position data.');
    }

    /**
     * Export single position to CSV.
     */
    public function export(string $id)
    {
        $position = Position::withTrashed()->findOrFail($id);
        Gate::authorize('view', $position);

        $fileName = 'position_' . $position->id . '_' . date('Y-m-d') . '.csv';
        $headers  = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($position) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['Field', 'Value']);
            
            // Position details
            fputcsv($file, ['ID', $position->id]);
            fputcsv($file, ['Position Name', $position->name]);
            fputcsv($file, ['Description', $position->description]);
            fputcsv($file, ['Base Salary', $position->base_salary]);
            fputcsv($file, ['Created At', $position->created_at->format('Y-m-d H:i:s')]);
            fputcsv($file, ['Updated At', $position->updated_at->format('Y-m-d H:i:s')]);
            if ($position->deleted_at) {
                fputcsv($file, ['Deleted At', $position->deleted_at->format('Y-m-d H:i:s')]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
