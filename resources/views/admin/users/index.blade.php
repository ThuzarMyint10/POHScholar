@extends('layouts.admin')

@section('content')
<div class="panel">
  <div class="panel-head">
    <h3>Admin Users</h3>
    <a href="{{ route('admin.users.create') }}" class="btn btn--sm">+ Add User</a>
  </div>
  <div class="panel-body no-pad">
    <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th>Name</th><th>Username</th><th>Role</th><th>Status</th><th>Last Login</th><th></th></tr></thead>
        <tbody>
          @foreach ($users as $u)
          <tr>
            <td>{{ $u->full_name }}</td>
            <td>{{ $u->username }}</td>
            <td>{{ ucfirst($u->role) }}</td>
            <td>{{ $u->is_active ? 'Active' : 'Disabled' }}</td>
            <td>{{ format_date($u->last_login_at, 'j M Y, g:i a') }}</td>
            <td class="action-links">
              <a href="{{ route('admin.users.edit', $u->id) }}">Edit</a>
              @if ($u->id !== auth('admin')->id())
                <form method="post" action="{{ route('admin.users.destroy', $u->id) }}" style="display:inline;" onsubmit="return confirm('Delete this admin user?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="link-danger">Delete</button>
                </form>
              @else
                <span style="color:var(--ink-soft);">(you)</span>
              @endif
            </td>
          </tr>
          @endforeach
          @if ($users->isEmpty())
            <tr><td colspan="6">No admin users yet.</td></tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
