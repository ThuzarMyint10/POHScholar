@extends('layouts.admin')

@section('content')
<div class="panel">
  <div class="panel-head">
    <h3>All Programs</h3>
    <a href="{{ route('admin.programs.create') }}" class="btn btn--sm">+ Add Program</a>
  </div>
  <div class="panel-body no-pad">
    <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th>Order</th><th>Title</th><th>Status</th><th></th></tr></thead>
        <tbody>
          @foreach ($programs as $p)
          <tr>
            <td>{{ $p->display_order }}</td>
            <td>{{ $p->title }}</td>
            <td>{{ $p->is_active ? 'Active' : 'Hidden' }}</td>
            <td class="action-links">
              <a href="{{ route('admin.programs.edit', $p->id) }}">Edit</a>
              <form method="post" action="{{ route('admin.programs.update', $p->id) }}" style="display:inline;">
                @csrf
                @method('PUT')
                <input type="hidden" name="title" value="{{ $p->title }}">
                <input type="hidden" name="summary" value="{{ $p->summary }}">
                <input type="hidden" name="description" value="{{ $p->description }}">
                <input type="hidden" name="eligibility" value="{{ $p->eligibility }}">
                <input type="hidden" name="benefits" value="{{ $p->benefits }}">
                <input type="hidden" name="display_order" value="{{ $p->display_order }}">
                @if (!$p->is_active)
                  <input type="hidden" name="is_active" value="1">
                @endif
                <button type="submit" style="background:none;border:none;padding:0;font:inherit;cursor:pointer;color:var(--maroon);text-decoration:none;">{{ $p->is_active ? 'Hide' : 'Publish' }}</button>
              </form>
              <form method="post" action="{{ route('admin.programs.destroy', $p->id) }}" style="display:inline;" onsubmit="return confirm('Delete this program? Existing applications will keep their history but show \'General\'.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="link-danger">Delete</button>
              </form>
            </td>
          </tr>
          @endforeach
          @if ($programs->isEmpty())
            <tr><td colspan="4">No programs yet.</td></tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
