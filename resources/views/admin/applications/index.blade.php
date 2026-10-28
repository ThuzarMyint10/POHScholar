@extends('layouts.admin')

@section('content')
<div class="panel">
  <div class="panel-body">
    <form method="get" class="toolbar">
      <input type="text" name="q" placeholder="Search name, email, phone, district&hellip;" value="{{ $search }}">
      <select name="status">
        <option value="">All Statuses</option>
        @foreach ($statuses as $s)
          <option value="{{ $s }}" {{ $statusFilter === $s ? 'selected' : '' }}>{{ $s }}</option>
        @endforeach
      </select>
      <button type="submit" class="btn btn--sm">Filter</button>
      <a href="{{ route('admin.applications.index') }}" class="btn btn--sm btn--outline">Reset</a>
      <span style="margin-left:auto; color:var(--ink-soft); font-size:0.88rem;">{{ $applications->count() }} result(s)</span>
    </form>
  </div>
  <div class="panel-body no-pad">
    <div class="table-wrap">
      <table class="data-table">
        <thead>
          <tr><th>Applicant</th><th>Program</th><th>District</th><th>Country</th><th>Phone</th><th>Status</th><th>Submitted</th><th></th></tr>
        </thead>
        <tbody>
          @foreach ($applications as $a)
          <tr>
            <td>{{ $a->full_name }}</td>
            <td>{{ $a->program->title ?? 'General' }}</td>
            <td>{{ $a->district }}</td>
            <td>{{ $a->country }}</td>
            <td>{{ $a->phone }}</td>
            <td><span class="badge badge--{{ status_badge_class($a->status) }}">{{ $a->status }}</span></td>
            <td>{{ format_date($a->created_at, 'j M Y') }}</td>
            <td><a href="{{ route('admin.applications.show', $a->id) }}">View</a></td>
          </tr>
          @endforeach
          @if ($applications->isEmpty())
            <tr><td colspan="8">No applications found.</td></tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
