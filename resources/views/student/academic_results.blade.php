@extends('layouts.student')

@section('content')
<span class="eyebrow">Post-Acceptance</span>
<h1>Academic Results</h1>
<p style="color:var(--ink-soft); margin-bottom:26px;">
  As a POH scholar, you're asked to submit a short update on your academic progress every six months.
</p>

@unless ($unlocked)
  <div class="locked-note">
    This page will become available once your scholarship application has been accepted.
  </div>
@else

  @if ($openPeriod)
    @php($overdue = $openPeriod->due_date->toDateString() < $today)
    <div class="panel" {{ $overdue ? 'style="border:1px solid var(--maroon, #8f1c2b);"' : '' }}>
      <div class="panel-head"><h3>Period {{ $openPeriod->period_number }} - Due {{ format_date($openPeriod->due_date) }}{{ $overdue ? ' (Overdue)' : '' }}</h3></div>
      <div class="panel-body">
        <form method="post" action="{{ route('student.academic_results.store') }}" enctype="multipart/form-data">
          @csrf
          <div class="form-group required">
            <label for="result_summary">Summary of Your Academic Results</label>
            <textarea id="result_summary" name="result_summary" rows="6" placeholder="e.g. term/exam results, GPA, any awards or notable progress" required>{{ old('result_summary') }}</textarea>
          </div>
          <div class="form-group">
            <label for="result_file">Attach Result Sheet / Report Card (optional, PDF only)</label>
            <input type="file" id="result_file" name="result_file" accept="application/pdf">
            <div class="hint">PDF only, max 8MB.</div>
          </div>
          <button type="submit" class="btn btn--block">Submit Academic Result</button>
        </form>
      </div>
    </div>
  @endif

  <div class="panel">
    <div class="panel-head"><h3>Submission History</h3></div>
    <div class="panel-body no-pad">
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr><th>Period</th><th>Due Date</th><th>Status</th><th>Submitted</th></tr>
          </thead>
          <tbody>
            @foreach ($periods as $p)
              @php($pOverdue = !$p->submitted_at && $p->due_date->toDateString() < $today)
              <tr>
                <td>Period {{ $p->period_number }}</td>
                <td>{{ format_date($p->due_date) }}</td>
                <td>
                  @if ($p->submitted_at)
                    <span class="status-pill status-pill--approved">Submitted</span>
                  @elseif ($pOverdue)
                    <span class="status-pill status-pill--rejected">Overdue</span>
                  @else
                    <span class="status-pill status-pill--submitted">Pending</span>
                  @endif
                </td>
                <td>{{ $p->submitted_at ? format_date($p->submitted_at, 'j M Y') : '—' }}</td>
              </tr>
            @endforeach
            @if ($periods->isEmpty())
              <tr><td colspan="4">No periods yet.</td></tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endunless
@endsection
