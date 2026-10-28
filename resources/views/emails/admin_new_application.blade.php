@extends('emails.layout')

@section('body')
  <p>A new scholarship application has been submitted.</p>
  <table style="width:100%; border-collapse:collapse; margin:16px 0;">
    <tr><td style="padding:6px 0; color:#5a4f47; width:160px;">Application Number</td><td style="padding:6px 0; font-weight:bold;">{{ $student->application_number }}</td></tr>
    <tr><td style="padding:6px 0; color:#5a4f47;">Candidate</td><td style="padding:6px 0;">{{ $student->full_name ?: 'Applicant' }}</td></tr>
    <tr><td style="padding:6px 0; color:#5a4f47;">Email</td><td style="padding:6px 0;">{{ $student->email }}</td></tr>
    <tr><td style="padding:6px 0; color:#5a4f47;">District</td><td style="padding:6px 0;">{{ $student->district }}</td></tr>
    <tr><td style="padding:6px 0; color:#5a4f47;">Country</td><td style="padding:6px 0;">{{ $student->country }}</td></tr>
    <tr><td style="padding:6px 0; color:#5a4f47;">Submitted</td><td style="padding:6px 0;">{{ format_date($student->submitted_at, 'j M Y, g:i a') }}</td></tr>
  </table>
  <p style="text-align:center; margin:22px 0;">
    <a href="{{ route('admin.students.show', $student->id) }}" style="background:#8f1c2b; color:#fff; padding:11px 22px; border-radius:4px; text-decoration:none; font-family:Arial,sans-serif; font-weight:bold; display:inline-block;">Review Application</a>
  </p>
@endsection
