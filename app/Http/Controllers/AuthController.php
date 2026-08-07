<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditLogService;
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
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string',
        ], [
            'email.exists' => 'Email not registered in our system.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! Hash::check($request->password, $user->password)) {
            // Log failed login attempt
            AuditLogService::log(
                'login_failed',
                $user,
                "Failed login attempt for '{$user->email}'"
            );

            return back()->with('error', 'Password is incorrect.')->withInput($request->only('email'));
        }

        if (in_array($user->role, ['admin', 'HR', 'manager', 'staff', 'finance'])) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            // Log successful login
            AuditLogService::log(
                'login',
                $user,
                "User '{$user->name}' logged in"
            );

            return redirect()->intended(route('dashboard'));
        }

        Auth::logout();

        // Log failed access (role not permitted)
        AuditLogService::log(
            'login_failed',
            $user,
            "Login denied for '{$user->email}' — role '{$user->role}' not permitted"
        );

        return back()->with('error', 'You do not have access to this area yet.')->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        // Log logout before session invalidation
        if ($user) {
            AuditLogService::log(
                'logout',
                $user,
                "User '{$user->name}' logged out"
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
