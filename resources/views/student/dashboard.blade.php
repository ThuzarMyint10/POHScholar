@extends('layouts.student')

@php
$statusClass = ['Draft' => 'draft', 'Submitted' => 'submitted', 'Under Review' => 'review', 'Approved' => 'approved', 'Rejected' => 'rejected'][$student->status] ?? 'draft';
@endphp

@section('content')
<div class="grid grid--2" style="align-items:start; margin-bottom:10px;">
  <div>
    <span class="eyebrow">Student Portal</span>
    <h1 style="margin-bottom:6px;">Welcome, {{ $student->full_name ?: $student->email }}</h1>
    <p style="color:var(--ink-soft);">Track and complete your POH Scholarship application below.</p>
  </div>
  <div style="text-align:right;">
    <span class="status-pill status-pill--{{ $statusClass }}">{{ $student->status }}</span>
    @if ($student->application_number)
      <div style="margin-top:8px; font-family:var(--serif); color:var(--maroon); font-size:1.1rem;">
        {{ $student->application_number }}
      </div>
    @endif
  </div>
</div>

@if ($student->status !== 'Draft')
  <div class="locked-note">
    Your application was submitted on {{ format_date($student->submitted_at, 'j M Y, g:i a') }} and is now locked for editing.
    You can still <a href="{{ route('student.application_pdf') }}">view / print a PDF copy</a>
    or <a href="{{ route('student.application_pdf.download') }}">download it as a PDF</a>.
  </div>
@endif

@if ($student->accepted_at)
  <div class="panel">
    <div class="panel-head"><h3>&#127881; You've Been Accepted!</h3></div>
    <div class="panel-body">
      <ul class="checklist">
        <li class="{{ $student->final_info_updated_at ? 'is-done' : '' }}">
          <span class="step-icon">{{ $student->final_info_updated_at ? '✓' : '!' }}</span>
          <div class="step-text">
            <strong>Final Information</strong>
            <span>Bank details for disbursement &amp; emergency contact.</span>
          </div>
          <a href="{{ route('student.final_info.edit') }}" class="btn btn--sm btn--outline">{{ $student->final_info_updated_at ? 'Review / Update' : 'Complete Now' }}</a>
        </li>
        <li class="{{ $openPeriod ? '' : 'is-done' }}">
          <span class="step-icon">{{ $openPeriod ? $openPeriod->period_number : '✓' }}</span>
          <div class="step-text">
            <strong>Academic Results</strong>
            <span>
              @if ($openPeriod)
                Period {{ $openPeriod->period_number }} due {{ format_date($openPeriod->due_date) }}
              @else
                All caught up.
              @endif
            </span>
          </div>
          <a href="{{ route('student.academic_results.index') }}" class="btn btn--sm btn--outline">View</a>
        </li>
      </ul>
    </div>
  </div>
@endif

<div class="panel">
  <div class="panel-head"><h3>Application Checklist</h3></div>
  <div class="panel-body">
    <ul class="checklist">
      <li class="{{ $profileComplete ? 'is-done' : '' }}">
        <span class="step-icon">{{ $profileComplete ? '✓' : '1' }}</span>
        <div class="step-text">
          <strong>Complete Your Profile</strong>
          <span>Personal details, school/guardian information, and a photo.</span>
        </div>
        <a href="{{ route('student.profile.edit') }}" class="btn btn--sm btn--outline">{{ $profileComplete ? 'Review' : 'Complete' }}</a>
      </li>
      <li class="{{ $academicRecordComplete ? 'is-done' : '' }}">
        <span class="step-icon">{{ $academicRecordComplete ? '✓' : '2' }}</span>
        <div class="step-text">
          <strong>Academic Record</strong>
          <span>Schools attended, O/L &amp; A/L results, and any academic or financial awards.</span>
        </div>
        <a href="{{ route('student.academic_record.edit') }}" class="btn btn--sm btn--outline">{{ $academicRecordComplete ? 'Review' : 'Complete' }}</a>
      </li>
      <li class="{{ $familyBackgroundPresent ? 'is-done' : '' }}">
        <span class="step-icon">{{ $familyBackgroundPresent ? '✓' : '3' }}</span>
        <div class="step-text">
          <strong>Family &amp; Background</strong>
          <span>Family particulars, and your own past employment record, if applicable.</span>
        </div>
        <a href="{{ route('student.family_background.edit') }}" class="btn btn--sm btn--outline">{{ $familyBackgroundPresent ? 'Review' : 'Complete' }}</a>
      </li>
      <li>
        <span class="step-icon">4</span>
        <div class="step-text">
          <strong>Activities &amp; Experience</strong>
          <span>Co-curricular activities, sports, leadership, and work experience.</span>
        </div>
        <a href="{{ route('student.activities.edit') }}" class="btn btn--sm btn--outline">Review</a>
      </li>
      <li class="{{ $essayComplete ? 'is-done' : '' }}">
        <span class="step-icon">{{ $essayComplete ? '✓' : '5' }}</span>
        <div class="step-text">
          <strong>Write Your Essay</strong>
          <span>Tell us about yourself and why you're applying.</span>
        </div>
        <a href="{{ route('student.essay.edit') }}" class="btn btn--sm btn--outline">{{ $essayComplete ? 'Review' : 'Write' }}</a>
      </li>
      <li class="{{ $documentsPresent ? 'is-done' : '' }}">
        <span class="step-icon">{{ $documentsPresent ? '✓' : '6' }}</span>
        <div class="step-text">
          <strong>Upload Certificates</strong>
          <span>{{ $docCount }} of {{ config('poh.max_student_documents') }} document(s) attached (PDF only, optional but recommended).</span>
        </div>
        <a href="{{ route('student.documents.index') }}" class="btn btn--sm btn--outline">Manage</a>
      </li>
      <li class="{{ $declarationComplete ? 'is-done' : '' }}">
        <span class="step-icon">{{ $declarationComplete ? '✓' : '7' }}</span>
        <div class="step-text">
          <strong>Other Information &amp; Declaration</strong>
          <span>Referees and the declaration you must sign before submitting.</span>
        </div>
        <a href="{{ route('student.additional_info.edit') }}" class="btn btn--sm btn--outline">{{ $declarationComplete ? 'Review' : 'Complete' }}</a>
      </li>
    </ul>

    @if ($student->status === 'Draft')
      <form method="post" action="{{ route('student.submit') }}" style="margin-top:10px;">
        @csrf
        @unless ($canSubmit)
          <p style="color:var(--ink-soft); font-size:0.9rem;">Complete your profile, essay and declaration above before submitting.</p>
        @endunless
        <button type="submit" class="btn btn--block" {{ $canSubmit ? '' : 'disabled' }}>Submit My Application</button>
      </form>
    @endif
  </div>
</div>
@endsection
