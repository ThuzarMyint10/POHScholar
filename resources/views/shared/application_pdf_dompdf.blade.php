<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $pageTitlePrefix }} {{ $student->application_number ?: '' }}</title>
<style>
  body { font-family: DejaVu Sans, Helvetica, Arial, sans-serif; font-size: 12px; color: #2c2420; }
  h2 { font-family: Georgia, 'Times New Roman', serif; color: #8f1c2b; margin-bottom: 4px; }
  .ps-header { display: table; width: 100%; margin-bottom: 14px; border-bottom: 3px solid #a9834f; padding-bottom: 10px; }
  .ps-header img { height: 34px; }
  .ps-appnum { text-align: right; }
  .ps-appnum .label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; color: #7a6f63; }
  .ps-appnum .value { font-size: 16px; font-weight: bold; }
  .ps-photo { height: 90px; border-radius: 4px; margin-bottom: 10px; }
  .status-pill { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 10px; font-weight: bold; background: #eee; }
  .ps-section-title { font-family: Georgia, 'Times New Roman', serif; color: #8f1c2b; font-size: 14px; font-weight: bold; margin: 18px 0 6px; border-bottom: 1px solid #ddd0b8; padding-bottom: 4px; }
  table.ps-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
  table.ps-table th { text-align: left; width: 32%; color: #5a4f47; font-weight: 600; padding: 4px 6px; vertical-align: top; border-bottom: 1px solid #eee; }
  table.ps-table td { padding: 4px 6px; vertical-align: top; border-bottom: 1px solid #eee; }
  .ps-essay { white-space: pre-wrap; color: #3a332e; }
  ul.list-check { margin: 0; padding-left: 16px; }
  .footer-note { margin-top: 24px; font-size: 9px; color: #7a6f63; border-top: 1px solid #eee; padding-top: 8px; }
</style>
</head>
<body>

@php
$statusClass = ['Draft' => '#c9a227', 'Submitted' => '#2f6fa5', 'Under Review' => '#2f6fa5', 'Approved' => '#2f7d4f', 'Rejected' => '#a52f2f'][$student->status] ?? '#888';
@endphp

<div class="ps-header">
  <img src="{{ public_path('assets/images/pohscholar.png') }}" alt="{{ config('app.name') }}">
  <div class="ps-appnum">
    <div class="label">Application Reference</div>
    <div class="value">{{ $student->application_number ?: 'DRAFT - NOT YET SUBMITTED' }}</div>
  </div>
</div>

@if ($student->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($student->photo))
  <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->path($student->photo) }}" class="ps-photo">
@endif

<h2>{{ $student->full_name }}</h2>
<p>
  <span class="status-pill" style="color:{{ $statusClass }}; border:1px solid {{ $statusClass }};">{{ $student->status }}</span>
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
  <p>No documents attached.</p>
@else
  <ul class="list-check">
    @foreach ($documents as $d)
      <li>{{ $d->label ?: $d->original_filename }} (uploaded {{ format_date($d->uploaded_at) }})</li>
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

<div class="footer-note">
  Printed {{ now()->format('j M Y, g:i a') }} &middot; POH Scholarships Scheme &middot; Since 2009
</div>

</body>
</html>