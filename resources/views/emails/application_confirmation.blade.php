@extends('emails.layout')

@section('body')
  <p>Dear {{ $student->full_name ?: 'Applicant' }},</p>
  <p><strong>Congratulations and thank you</strong> - your scholarship application has been submitted successfully.</p>
  <p style="background:#f6f1e7; border:1px solid #ddd0b8; border-radius:4px; padding:14px 18px; text-align:center;">
    Your Application Reference Number is<br>
    <span style="font-size:20px; font-weight:bold; color:#8f1c2b;">{{ $student->application_number }}</span>
  </p>
  <p>Please keep this number for your records - you'll need it if you contact our office about your application.</p>
  <p>Our team will now review your profile, essay and attached documents. We may contact you if we need anything further. You can log in at any time to check your application status, or download a PDF copy of your submission for your records.</p>
  <p style="text-align:center; margin:26px 0;">
    <a href="{{ route('student.dashboard') }}" style="background:#8f1c2b; color:#fff; padding:11px 22px; border-radius:4px; text-decoration:none; font-family:Arial,sans-serif; font-weight:bold; display:inline-block;">View My Application</a>
  </p>
  <p style="font-size:13px; color:#5a4f47;">You can also download a printable PDF copy of your submitted application here: <a href="{{ route('student.application_pdf') }}">{{ route('student.application_pdf') }}</a></p>
  <p>We warmly welcome your application and look forward to supporting your educational journey.</p>
  <p>Yours sincerely,<br>POH Scholarships Scheme</p>
@endsection
