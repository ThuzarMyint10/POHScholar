@extends('layouts.admin')

@section('content')
<div class="admin-cards">
  <div class="admin-card">
    <div class="num">{{ $stats['total_students'] }}</div>
    <div class="label">Student Applications</div>
  </div>
  <div class="admin-card">
    <div class="num">{{ $stats['pending_students'] }}</div>
    <div class="label">Awaiting Review</div>
  </div>
  <div class="admin-card">
    <div class="num">{{ $stats['total_scholars'] }}</div>
    <div class="label">Active Scholars</div>
  </div>
  <div class="admin-card">
    <div class="num">{{ $stats['graduated_scholars'] }}</div>
    <div class="label">Graduated Scholars</div>
  </div>
  <div class="admin-card">
    <div class="num">{{ $stats['unread_messages'] }}</div>
    <div class="label">Unread Messages</div>
  </div>
  <div class="admin-card">
    <div class="num">{{ $stats['overdue_results'] }}</div>
    <div class="label">Overdue Academic Results</div>
  </div>
</div>

<div class="panel">
  <div class="panel-head">
    <h3>Recent Student Applications</h3>
    <a href="{{ route('admin.students.index') }}" class="btn btn--sm btn--outline">View All</a>
  </div>
  <div class="panel-body no-pad">
    <div class="table-wrap">
      <table class="data-table">
        <thead>
          <tr><th>Application #</th><th>Candidate</th><th>Program</th><th>District</th><th>Status</th><th>Submitted</th><th></th></tr>
        </thead>
        <tbody>
          @foreach ($recentStudents as $s)
          <tr>
            <td>{{ $s->application_number }}</td>
            <td>{{ $s->full_name }}</td>
            <td>{{ $s->program->title ?? 'General' }}</td>
            <td>{{ $s->district }}</td>
            <td><span class="badge badge--{{ status_badge_class($s->status) }}">{{ $s->status }}</span></td>
            <td>{{ format_date($s->submitted_at, 'j M Y') }}</td>
            <td><a href="{{ route('admin.students.show', $s->id) }}">View</a></td>
          </tr>
          @endforeach
          @if ($recentStudents->isEmpty())
            <tr><td colspan="7">No student applications submitted yet.</td></tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="grid grid--2">
  <div class="panel">
    <div class="panel-head"><h3>Quick Links</h3></div>
    <div class="panel-body">
      <p><a href="{{ route('admin.students.index') }}">&rarr; Review Student Applications</a></p>
      <p><a href="{{ route('admin.applications.index') }}">&rarr; Quick Applications ({{ $stats['total_applications'] }})</a></p>
      <p><a href="{{ route('admin.scholars.index') }}">&rarr; Manage Scholar Records</a></p>
      <p><a href="{{ route('admin.programs.index') }}">&rarr; Manage Scholarship Programs</a></p>
      <p><a href="{{ route('admin.news.index') }}">&rarr; Manage News &amp; Events</a></p>
      <p style="margin-bottom:0;"><a href="{{ route('admin.settings.edit') }}">&rarr; Edit Site Content</a></p>
    </div>
  </div>
  <div class="panel">
    <div class="panel-head"><h3>Logged in as</h3></div>
    <div class="panel-body">
      <p><strong>{{ $admin->full_name }}</strong> ({{ $admin->username }})</p>
      <p style="margin-bottom:0;">Role: {{ ucfirst($admin->role) }}</p>
    </div>
  </div>
</div>
@endsection
