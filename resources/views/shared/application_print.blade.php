<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $pageTitlePrefix }} {{ $student->application_number ?: '' }} | {{ config('app.name') }}</title>
<link rel="icon" type="image/png" href="{{ asset('assets/images/header.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/portal.css') }}">
</head>
<body class="portal-body">

@php
$statusClass = ['Draft' => 'draft', 'Submitted' => 'submitted', 'Under Review' => 'review', 'Approved' => 'approved', 'Rejected' => 'rejected'][$student->status] ?? 'draft';
@endphp

<div class="print-toolbar no-print">
  <div class="container">
    <a href="{{ $backUrl }}">&larr; Back</a>
    &nbsp;&nbsp;
    <button onclick="window.print()" class="btn btn--sm">&#128424; Print / Save as PDF</button>
    &nbsp;&nbsp;
    <a href="{{ $downloadUrl ?? '' }}" class="btn btn--sm btn--outline">&#11015; Download PDF</a>
  </div>
</div>

<div class="container">
  <div class="print-sheet">
    <div class="ps-header">
      <img src="{{ asset('assets/images/pohscholar.png') }}" alt="{{ config('app.name') }}">
      <div class="ps-appnum">
        <div style="font-size:0.78rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--ink-soft);">Application Reference</div>
        <div style="font-size:1.3rem; font-weight:700;">{{ $student->application_number ?: 'DRAFT - NOT YET SUBMITTED' }}</div>
      </div>
    </div>

    @if ($student->photo)
      <img src="{{ asset('storage/'.$student->photo) }}" alt="" class="ps-photo">
    @endif

    <h2>{{ $student->full_name }}</h2>
    <p style="color:var(--ink-soft); margin-bottom:6px;">
      <span class="status-pill status-pill--{{ $statusClass }}">{{ $student->status }}</span>
      &nbsp; Submitted: {{ $student->submitted_at ? format_date($student->submitted_at, 'j M Y, g:i a') : 'Not yet submitted' }}
    </p>

    <div class="ps-section-title">Applicant Details</div>
    <table class="ps-table">
      <tr><th>Program</th><td>{{ $student->program->title ?? 'General application' }}</td></tr>
      <tr><th>Date of Birth</th><td>{{ format_date($student->date_of_birth) }}</td></tr>
      <tr><th>Gender</th><td>{{ $student->gender }}</td></tr>
      <tr><th>NIC / Birth Cert.</th><td>{{ $student->nic_or_birth_cert }}</td></tr>
      <tr><th>Address</th><td>{!! nl2br(e($student->address)) !!}</td></tr>
      <tr><th>District</th><td>{{ $student->district }}</td></tr>
      <tr><th>Country</th><td>{{ $student->country }}</td></tr>
      <tr><th>Phone</th><td>{{ $student->phone }}</td></tr>
      <tr><th>Email</th><td>{{ $student->email }}</td></tr>
      <tr><th>University / Institute</th><td>{{ $student->school_or_institute }}</td></tr>
      <tr><th>Year of Study</th><td>{{ $student->grade_or_year }}</td></tr>
      <tr><th>Guardian Name</th><td>{{ $student->guardian_name }}</td></tr>
      <tr><th>Guardian Occupation</th><td>{{ $student->guardian_occupation }}</td></tr>
      <tr><th>Household Income</th><td>{{ $student->household_income_band }}</td></tr>
    </table>

    @include('shared._application_full_sections', ['student' => $student])

    <div class="ps-section-title">Essay</div>
    <p class="ps-essay">{{ $student->essay ?: '(No essay submitted)' }}</p>

    <div class="ps-section-title">Attached Documents ({{ $documents->count() }})</div>
    @if ($documents->isEmpty())
      <p style="color:var(--ink-soft);">No documents attached.</p>
    @else
      <ul class="list-check">
        @foreach ($documents as $d)
          <li>{{ $d->label ?: $d->original_filename }} <span style="color:var(--ink-soft);">(uploaded {{ format_date($d->uploaded_at) }})</span></li>
        @endforeach
      </ul>
    @endif

    @if ($showAdminFields ?? false)
      <div class="ps-section-title">Administrator Evaluation (internal)</div>
      <table class="ps-table">
        <tr><th>Evaluation Score</th><td>{{ $student->evaluation_score !== null ? $student->evaluation_score.' / 5' : 'Not yet evaluated' }}</td></tr>
        <tr><th>Evaluation Notes</th><td>{!! $student->evaluation_notes ? nl2br(e($student->evaluation_notes)) : '-' !!}</td></tr>
        <tr><th>Mrs. Poh's Remarks</th><td>{!! $student->mrs_poh_remarks ? nl2br(e($student->mrs_poh_remarks)) : '-' !!}</td></tr>
      </table>
    @endif

    <p style="margin-top:30px; font-size:0.8rem; color:var(--ink-soft); border-top:1px solid var(--line); padding-top:14px;">
      Printed {{ now()->format('j M Y, g:i a') }} &middot; POH Scholarships Scheme &middot; Since 2009
    </p>
  </div>
</div>

</body>
</html>