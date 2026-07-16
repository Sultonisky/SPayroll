<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', \App\Models\User::class);
        $users = User::select('id', 'name', 'email', 'role', 'created_at')
            ->latest()
            ->get();
            
        return view('dashboard.users.index', compact('users'));
    }

    /**
     * Display a listing of deleted resources.
     */
    public function trash()
    {
        Gate::authorize('viewAny', \App\Models\User::class);
        $users = User::onlyTrashed()
            ->select('id', 'name', 'email', 'role', 'created_at')
            ->latest()
            ->get();
            
        return view('dashboard.users.index', compact('users'))->with('isTrash', true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', \App\Models\User::class);
        return view('dashboard.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', \App\Models\User::class);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,HR,manager,staff',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'Berhasil menambahkan pengguna baru.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        Gate::authorize('view', $user);
        return view('dashboard.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->route('admin.dashboard')->with('info', 'Gunakan halaman profil untuk mengedit akun Anda.');
        }

        Gate::authorize('update', $user);
        return view('dashboard.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->route('admin.dashboard')->with('error', 'Anda tidak dapat memperbarui akun sendiri melalui manajemen pengguna.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,HR,manager,staff',
        ]);


        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        Gate::authorize('update', $user);
        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        Gate::authorize('delete', $user);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna dipindahkan ke tempat sampah.');
    }

    /**
     * Restore the specified deleted resource.
     */
    public function restore(string $id)
    {
        $user = User::onlyTrashed()->findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat memulihkan akun Anda sendiri dari tempat sampah.');
        }

        Gate::authorize('restore', $user);
        $user->restore();

        return redirect()->route('users.trash')->with('success', 'Pengguna berhasil dipulihkan.');
    }

    /**
     * Permanently delete the specified resource.
     */
    public function forceDelete(string $id)
    {
        $user = User::onlyTrashed()->findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus permanen akun Anda sendiri.');
        }

        Gate::authorize('forceDelete', $user);
        $user->forceDelete();

        return redirect()->route('users.trash')->with('success', 'Pengguna dihapus secara permanen.');
    }

    /**
     * Export single user to CSV.
     */
    public function export(string $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        Gate::authorize('view', $user);

        $fileName = 'user_' . $user->id . '_' . date('Y-m-d') . '.csv';
        $headers  = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($user) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['Field', 'Value']);
            
            // User details
            fputcsv($file, ['ID', $user->id]);
            fputcsv($file, ['Nama', $user->name]);
            fputcsv($file, ['Email', $user->email]);
            fputcsv($file, ['Role', $user->role]);
            fputcsv($file, ['Tanggal Dibuat', $user->created_at->format('Y-m-d H:i:s')]);
            fputcsv($file, ['Tanggal Diperbarui', $user->updated_at->format('Y-m-d H:i:s')]);
            if ($user->deleted_at) {
                fputcsv($file, ['Tanggal Dihapus', $user->deleted_at->format('Y-m-d H:i:s')]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
