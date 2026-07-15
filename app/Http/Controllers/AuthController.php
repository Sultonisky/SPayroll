<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function authLogin(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|string',
        ], [
            'email.exists' => 'Email not registered in our system.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Password is incorrect.')->withInput($request->only('email'));
        }

        if ($user->role === 'admin' || $user->role === 'staff') {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        Auth::logout();
        return back()->with('error', 'You do not have access to this area yet.')->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
