<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Rules\Recaptcha;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(Request $request): View|RedirectResponse
    {
        if (Auth::guard('student')->check()) {
            return redirect()->route('student.dashboard');
        }

        return view('student.auth.login', [
            'timeout' => $request->boolean('timeout'),
            'disabled' => $request->boolean('disabled'),
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'g-recaptcha-response' => [new Recaptcha],
        ]);

        $credentials = $request->only('email', 'password');

        $student = Student::query()->where('email', $credentials['email'])->first();

        if (! $student) {
            return back()->withInput($request->only('email'))
                ->with('error', 'No account found with that email address.');
        }

        if (! $student->is_active) {
            return redirect()->route('student.login', ['disabled' => 1]);
        }

        if (! Auth::guard('student')->attempt($credentials)) {
            return back()->withInput($request->only('email'))
                ->with('error', 'Incorrect password. Please try again.');
        }

        $request->session()->regenerate();

        $student->forceFill(['last_login_at' => now()])->save();

        return redirect()->intended(route('student.dashboard'));
    }

    public function showRegister(): View|RedirectResponse
    {
        if (Auth::guard('student')->check()) {
            return redirect()->route('student.dashboard');
        }

        return view('student.auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:190', 'unique:students,email'],
            'full_name' => ['nullable', 'string', 'max:200'],
            'password' => ['required', 'string', 'min:8'],
            'password_confirmation' => ['required', 'same:password'],
            'g-recaptcha-response' => [new Recaptcha],
        ], [
            'email.unique' => 'An account with that email address already exists. Please log in instead.',
            'password.min' => 'Password must be at least 8 characters.',
            'password_confirmation.same' => 'Passwords do not match.',
        ]);

        if ($validator->fails()) {
            return back()->withInput($request->except('password', 'password_confirmation'))
                ->with('error', $validator->errors()->first());
        }

        $data = $validator->validated();

        $student = Student::query()->create([
            'email' => $data['email'],
            'password_hash' => Hash::make($data['password']),
            'full_name' => $data['full_name'] ?? null,
        ]);

        Auth::guard('student')->login($student);
        $request->session()->regenerate();

        return redirect()->route('student.profile.edit')
            ->with('success', 'Welcome! Your account has been created. Please complete your profile to continue your application.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('student')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('student.login');
    }
}