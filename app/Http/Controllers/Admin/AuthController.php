<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Rules\Recaptcha;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(Request $request): View|RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login', [
            'timeout' => $request->boolean('timeout'),
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'g-recaptcha-response' => [new Recaptcha],
        ]);

        $credentials = $request->only('username', 'password');

        $admin = AdminUser::query()->where('username', $credentials['username'])->first();

        if (! $admin || ! $admin->is_active) {
            return back()->withInput($request->only('username'))
                ->with('error', 'Invalid username or password.');
        }

        if (! Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            return back()->withInput($request->only('username'))
                ->with('error', 'Invalid username or password.');
        }

        $request->session()->regenerate();

        $admin->forceFill(['last_login_at' => now()])->save();

        return redirect()->intended(route('admin.dashboard'))
            ->with('success', 'Welcome back, '.$admin->full_name.'.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}