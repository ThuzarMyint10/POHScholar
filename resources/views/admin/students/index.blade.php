@extends('layouts.admin')

@section('content')
@if ($underReviewCount > 0)
<div class="panel" style="border:1px solid var(--maroon, #8f1c2b);">
  <div class="panel-body" style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
    <strong>{{ $underReviewCount }}</strong> application(s) are ready for AI ranking.
    <form method="post" action="{{ route('admin.students.ai_rank') }}" style="margin-left:auto;">
      @csrf
      <button type="submit" class="btn btn--sm" onclick="return confirm('Run AI ranking for all Under Review applications?');">Run AI Ranking</button>
    </form>
  </div>
</div>
@endif

@if ($overdueCount > 0)
<div class="panel" style="border:1px solid var(--maroon, #8f1c2b);">
  <div class="panel-body" style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
    <strong>{{ $overdueCount }}</strong> scholar(s) have an overdue 6-monthly academic result submission.
    <form method="post" action="{{ route('admin.students.bulk_remind') }}" style="margin-left:auto;">
      @csrf
      <button type="submit" class="btn btn--sm">Send Reminders to All Overdue</button>
    </form>
  </div>
</div>
@endif

<div class="panel">
  <div class="panel-body">
    <form method="get" class="toolbar">
      <input type="text" name="q" placeholder="Search name, email, application no., district&hellip;" value="{{ $search }}">
      <select name="status">
        <option value="">All Statuses</option>
        @foreach ($statuses as $s)
          <option value="{{ $s }}" {{ $statusFilter === $s ? 'selected' : '' }}>{{ $s }}</option>
        @endforeach
      </select>
      <button type="submit" class="btn btn--sm">Filter</button>
      <a href="{{ route('admin.students.index') }}" class="btn btn--sm btn--outline">Reset</a>
      <span style="margin-left:auto; color:var(--ink-soft); font-size:0.88rem;">{{ $students->count() }} result(s)</span>
    </form>
  </div>
  <div class="panel-body no-pad">
    <div class="table-wrap">
      <table class="data-table">
        <thead>
          <tr><th>Application #</th><th>Candidate</th><th>Program</th><th>District</th><th>Status</th><th>AI Score</th><th>Account</th><th>Submitted</th><th></th></tr>
        </thead>
        <tbody>
          @foreach ($students as $s)
          <tr>
            <td>{{ $s->application_number ?: '—' }}</td>
            <td>{{ $s->full_name ?: $s->email }}<br><span style="color:var(--ink-soft); font-size:0.82rem;">{{ $s->email }}</span></td>
            <td>{{ $s->program->title ?? 'General' }}</td>
            <td>{{ $s->district }}</td>
            <td>
              <span class="badge badge--{{ status_badge_class($s->status) }}">{{ $s->status }}</span>
              @if ($overdueIds->has($s->id))
                <br><span class="badge badge--rejected" style="margin-top:4px; display:inline-block;">Results Overdue</span>
              @endif
            </td>
            <td>
              @if ($s->ai_rank_composite_score !== null)
                @php $composite = (int) $s->ai_rank_composite_score; @endphp
                <span class="badge badge--{{ $composite >= 4 ? 'approved' : ($composite >= 2 ? 'review' : 'rejected') }}"
                      title="Family Background: {{ $s->ai_rank_family_score ?? '—' }} &middot; Academic Result: {{ $s->ai_rank_academic_score ?? '—' }} &middot; Economic Status: {{ $s->ai_rank_economic_score ?? '—' }} &middot; Essay: {{ $s->ai_rank_essay_score ?? '—' }}">
                  {{ $composite }} / 5
                </span>
                <div style="font-size:0.72rem; color:var(--ink-soft); margin-top:4px;">
                  F {{ $s->ai_rank_family_score ?? '—' }} &middot; A {{ $s->ai_rank_academic_score ?? '—' }}
                  &middot; E {{ $s->ai_rank_economic_score ?? '—' }} &middot; Es {{ $s->ai_rank_essay_score ?? '—' }}
                </div>
              @else
                <span style="color:var(--ink-soft);">&mdash;</span>
              @endif
            </td>
            <td><span class="badge badge--{{ $s->is_active ? 'approved' : 'rejected' }}">{{ $s->is_active ? 'Active' : 'Disabled' }}</span></td>
            <td>{{ format_date($s->submitted_at, 'j M Y') }}</td>
            <td><a href="{{ route('admin.students.show', $s->id) }}">View</a></td>
          </tr>
          @endforeach
          @if ($students->isEmpty())
            <tr><td colspan="9">No student registrations yet.</td></tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
