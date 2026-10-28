<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    private function ensureAdministrator(): void
    {
        if (auth('admin')->user()->role !== 'administrator') {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function index(): View
    {
        $this->ensureAdministrator();

        $users = AdminUser::query()->orderBy('id')->get();

        return view('admin.users.index', [
            'pageTitle' => 'Admin Users',
            'active' => 'users',
            'users' => $users,
        ]);
    }

    public function create(): View
    {
        $this->ensureAdministrator();

        return view('admin.users.form', [
            'pageTitle' => 'Add Admin User',
            'active' => 'users',
            'user' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureAdministrator();

        $data = $this->validateData($request, null);

        AdminUser::query()->create([
            'username' => $data['username'],
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'is_active' => $data['is_active'],
            'password_hash' => Hash::make($data['password']),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created.');
    }

    public function edit(AdminUser $user): View
    {
        $this->ensureAdministrator();

        return view('admin.users.form', [
            'pageTitle' => 'Edit Admin User',
            'active' => 'users',
            'user' => $user,
        ]);
    }

    public function update(Request $request, AdminUser $user): RedirectResponse
    {
        $this->ensureAdministrator();

        $data = $this->validateData($request, $user);

        $fill = [
            'username' => $data['username'],
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'is_active' => $data['is_active'],
        ];

        // Leave blank to keep current password - only hash+update when a new one was submitted.
        if ($data['password'] !== '') {
            $fill['password_hash'] = Hash::make($data['password']);
        }

        $user->forceFill($fill)->save();

        return redirect()->route('admin.users.index')->with('success', 'User updated.');
    }

    public function destroy(AdminUser $user): RedirectResponse
    {
        $this->ensureAdministrator();

        if ($user->id === auth('admin')->id()) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account while logged in.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }

    private function validateData(Request $request, ?AdminUser $user): array
    {
        $data = $request->validate([
            'username' => [
                'required', 'string', 'regex:/^[a-zA-Z0-9_.\-]{3,60}$/',
                Rule::unique('admin_users', 'username')->ignore($user?->id),
            ],
            'full_name' => ['required', 'string', 'max:200'],
            'email' => ['nullable', 'string', 'max:200'],
            'role' => ['required', 'in:administrator,editor'],
            'password' => ['nullable', 'string', 'min:8'],
            'password_confirm' => ['nullable', 'same:password'],
        ], [
            'username.required' => 'Username must be 3-60 characters (letters, numbers, . _ -).',
            'username.regex' => 'Username must be 3-60 characters (letters, numbers, . _ -).',
            'username.unique' => 'That username is already taken.',
            'full_name.required' => 'Please enter the full name.',
            'role.in' => 'Invalid role.',
            'password.min' => 'Password must be at least 8 characters.',
            'password_confirm.same' => 'Passwords do not match.',
        ]);

        $password = trim((string) ($data['password'] ?? ''));

        if (! $user && $password === '') {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'password' => 'Please set a password for the new user.',
            ]);
        }

        return [
            'username' => trim((string) $data['username']),
            'full_name' => trim((string) $data['full_name']),
            'email' => trim((string) ($data['email'] ?? '')),
            'role' => $data['role'],
            'is_active' => $request->boolean('is_active') ? 1 : 0,
            'password' => $password,
        ];
    }
}
