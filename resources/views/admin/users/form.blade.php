@extends('layouts.admin')

@section('content')
<p><a href="{{ route('admin.users.index') }}">&larr; Back to Admin Users</a></p>

<div class="panel">
  <div class="panel-body">
    <form method="post" action="{{ $user ? route('admin.users.update', $user->id) : route('admin.users.store') }}">
      @csrf
      @if ($user)
        @method('PUT')
      @endif
      <div class="form-row">
        <div class="form-group required">
          <label for="full_name">Full Name</label>
          <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $user->full_name ?? '') }}" required>
        </div>
        <div class="form-group required">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" value="{{ old('username', $user->username ?? '') }}" required>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="email">Email</label>
          <input type="text" id="email" name="email" value="{{ old('email', $user->email ?? '') }}">
        </div>
        <div class="form-group">
          <label for="role">Role</label>
          <select id="role" name="role">
            <option value="editor" {{ old('role', $user->role ?? 'editor') === 'editor' ? 'selected' : '' }}>Editor</option>
            <option value="administrator" {{ old('role', $user->role ?? 'editor') === 'administrator' ? 'selected' : '' }}>Administrator</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="password">{{ $user ? 'New Password (leave blank to keep current)' : 'Password' }}</label>
          <input type="password" id="password" name="password" autocomplete="new-password">
        </div>
        <div class="form-group">
          <label for="password_confirm">Confirm Password</label>
          <input type="password" id="password_confirm" name="password_confirm" autocomplete="new-password">
        </div>
      </div>
      <div class="form-group">
        <label><input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }} style="width:auto; margin-right:8px;">Account active</label>
      </div>
      <button type="submit" class="btn">{{ $user ? 'Save Changes' : 'Create User' }}</button>
    </form>
  </div>
</div>
@endsection
