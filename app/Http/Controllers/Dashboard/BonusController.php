<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Bonus;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BonusController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Bonus::class);

        $year       = $request->input('year');       // null = all years
        $month      = $request->input('month');      // null = all months
        $employeeId = $request->input('employee_id');
        $status     = $request->input('status');

        $query = Bonus::select('id', 'employee_id', 'year', 'month', 'type', 'amount', 'status', 'created_at')
            ->with('employee:id,name,employee_code');

        if ($year) {
            $query->where('year', $year);
        }
        if ($month) {
            $query->where('month', $month);
        }
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }
        if ($status && in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        $bonuses = $query->orderByDesc('year')
            ->orderByDesc('month')
            ->orderByDesc('created_at')
            ->get();

        $allEmployees = Employee::select('id', 'name', 'nik')->orderBy('name')->get();

        return view('dashboard.bonuses.index', compact('bonuses', 'year', 'month', 'status', 'employeeId', 'allEmployees'));
    }

    public function trash()
    {
        Gate::authorize('viewAny', Bonus::class);

        $bonuses = Bonus::onlyTrashed()
            ->select('id', 'employee_id', 'year', 'month', 'type', 'amount', 'status', 'created_at')
            ->with('employee:id,name,employee_code')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderByDesc('created_at')
            ->get();

        return view('dashboard.bonuses.index', compact('bonuses'))->with('isTrash', true);
    }

    public function create()
    {
        Gate::authorize('create', Bonus::class);

        $employees = Employee::select('id', 'name', 'employee_code')
            ->where('employee_status', 'active')
            ->orderBy('name')
            ->get();

        return view('dashboard.bonuses.create', compact('employees'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Bonus::class);

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'year'        => 'required|integer|min:2000|max:2100',
            'month'       => 'required|integer|min:1|max:12',
            'type'        => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'amount'      => 'required|numeric|min:1',
            'notes'       => 'nullable|string|max:500',
        ]);

        Bonus::create($validated);

        return redirect()->route('bonuses.index')
            ->with('success', 'Bonus submitted successfully and is pending approval.');
    }

    public function show(string $id)
    {
        $bonus = Bonus::withTrashed()->with(['employee', 'approvedBy'])->findOrFail($id);
        Gate::authorize('view', $bonus);

        return view('dashboard.bonuses.show', compact('bonus'));
    }

    public function edit(string $id)
    {
        $bonus = Bonus::findOrFail($id);
        Gate::authorize('update', $bonus);

        $employees = Employee::select('id', 'name', 'employee_code')
            ->where('employee_status', 'active')
            ->orderBy('name')
            ->get();

        return view('dashboard.bonuses.edit', compact('bonus', 'employees'));
    }

    public function update(Request $request, string $id)
    {
        $bonus = Bonus::findOrFail($id);
        Gate::authorize('update', $bonus);

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'year'        => 'required|integer|min:2000|max:2100',
            'month'       => 'required|integer|min:1|max:12',
            'type'        => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'amount'      => 'required|numeric|min:1',
            'notes'       => 'nullable|string|max:500',
        ]);

        $bonus->update($validated);

        return redirect()->route('bonuses.index')
            ->with('success', 'Bonus updated successfully.');
    }

    /**
     * Approve a pending bonus.
     */
    public function approve(string $id)
    {
        $bonus = Bonus::findOrFail($id);
        Gate::authorize('approve', $bonus);

        $bonus->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', "Bonus approved for {$bonus->employee?->name}.");
    }

    /**
     * Reject a pending bonus.
     */
    public function reject(Request $request, string $id)
    {
        $bonus = Bonus::findOrFail($id);
        Gate::authorize('approve', $bonus);

        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $bonus->update([
            'status' => 'rejected',
            'notes'  => $request->notes,
        ]);

        return redirect()->back()
            ->with('success', 'Bonus rejected.');
    }

    public function destroy(string $id)
    {
        $bonus = Bonus::findOrFail($id);
        Gate::authorize('delete', $bonus);
        $bonus->delete();

        return redirect()->route('bonuses.index')
            ->with('success', 'Bonus moved to trash.');
    }

    public function restore(string $id)
    {
        $bonus = Bonus::onlyTrashed()->findOrFail($id);
        Gate::authorize('restore', $bonus);
        $bonus->restore();

        return redirect()->route('bonuses.trash')
            ->with('success', 'Bonus restored successfully.');
    }

    public function forceDelete(string $id)
    {
        $bonus = Bonus::onlyTrashed()->findOrFail($id);
        Gate::authorize('forceDelete', $bonus);
        $bonus->forceDelete();

        return redirect()->route('bonuses.trash')
            ->with('success', 'Bonus permanently deleted.');
    }
}
