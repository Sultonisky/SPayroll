<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Traits\ProcessesImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    use ProcessesImages;
    public function index()
    {
        $user = auth()->user();
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        return view('dashboard.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'current_password' => 'nullable|required_with:password|current_password',
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('foto')) {
            $newPath = $this->processAndStoreImage($request->file('foto'), 'avatars', 600, 82);

            if (!empty($user->foto)) {
                Storage::disk('supabase')->delete($user->foto);
            }

            $user->foto = $newPath;
        }

        $user->save();

        return redirect()->route('profile.index')->with('success', 'Success update Profile.');
    }
}
