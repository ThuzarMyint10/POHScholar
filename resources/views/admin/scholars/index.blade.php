@extends('layouts.admin')

@section('content')
<div class="panel">
  <div class="panel-head">
    <div>
      <h3>{{ $statusFilter === 'Graduated' ? 'Graduated Alumni' : ($statusFilter === 'Discontinued' ? 'Discontinued Records' : ($statusFilter === 'All' ? 'All Scholar Records' : 'Current Scholars')) }}</h3>
      <p class="hint" style="margin:4px 0 0;">This register contains current scholarship recipients and their historical alumni records.</p>
    </div>
    <a href="{{ route('admin.scholars.create') }}" class="btn btn--sm">+ Add Scholar</a>
  </div>
  <div class="panel-body" style="display:flex; gap:18px; flex-wrap:wrap;">
    <a href="{{ route('admin.scholars.index', ['status' => 'Active']) }}" class="{{ $statusFilter === 'Active' ? 'is-active' : '' }}"><strong>{{ $statusCounts->get('Active', 0) }}</strong> Current Scholars</a>
    <a href="{{ route('admin.scholars.index', ['status' => 'Graduated']) }}" class="{{ $statusFilter === 'Graduated' ? 'is-active' : '' }}"><strong>{{ $statusCounts->get('Graduated', 0) }}</strong> Graduated Alumni</a>
    <a href="{{ route('admin.scholars.index', ['status' => 'Discontinued']) }}" class="{{ $statusFilter === 'Discontinued' ? 'is-active' : '' }}"><strong>{{ $statusCounts->get('Discontinued', 0) }}</strong> Discontinued <span class="hint">(support ended)</span></a>
    <a href="{{ route('admin.scholars.index', ['status' => 'All']) }}" class="{{ $statusFilter === 'All' ? 'is-active' : '' }}">View All Records</a>
  </div>
  <div class="panel-body no-pad">
    <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th>Name</th><th>Program</th><th>School</th><th>District</th><th>Status</th><th></th></tr></thead>
        <tbody>
          @foreach ($scholars as $s)
          <tr>
            <td>{{ $s->full_name }}</td>
            <td>{{ $s->program->title ?? '—' }}</td>
            <td>{{ $s->school_or_institute }}</td>
            <td>{{ $s->district }}</td>
            <td><span class="badge badge--{{ status_badge_class($s->status) }}">{{ $s->status }}</span></td>
            <td class="action-links">
              <a href="{{ route('admin.scholars.edit', $s->id) }}">Edit</a>
              <form method="post" action="{{ route('admin.scholars.destroy', $s->id) }}" style="display:inline;" onsubmit="return confirm('Delete this scholar record?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="link-danger">Delete</button>
              </form>
            </td>
          </tr>
          @endforeach
          @if ($scholars->isEmpty())
            <tr><td colspan="6">No scholar records yet.</td></tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
