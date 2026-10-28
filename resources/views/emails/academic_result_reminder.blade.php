@extends('emails.layout')

@section('body')
  <p>Dear {{ $student->full_name ?: 'Applicant' }},</p>
  <p>This is a friendly reminder from the POH Scholarships Scheme that your <strong>academic result update (Period {{ $period->period_number }})</strong> was due on <strong>{{ format_date($period->due_date) }}</strong> and has not yet been submitted.</p>
  <p>As a condition of your scholarship, we ask all scholars to submit a short update on their academic progress every six months. Please log in to your student portal at your earliest convenience to submit this update.</p>
  <p style="text-align:center; margin:26px 0;">
    <a href="{{ route('student.login') }}" style="background:#8f1c2b; color:#fff; padding:11px 22px; border-radius:4px; text-decoration:none; font-family:Arial,sans-serif; font-weight:bold; display:inline-block;">Log In and Submit</a>
  </p>
  <p style="font-size:13px; color:#5a4f47;">Direct link: <a href="{{ route('student.academic_results.index') }}">{{ route('student.academic_results.index') }}</a></p>
  <p>If you have already sent us this information by another means, or if you have any difficulty submitting online, please contact our office.</p>
  <p>Thank you,<br>POH Scholarships Scheme</p>
@endsection
