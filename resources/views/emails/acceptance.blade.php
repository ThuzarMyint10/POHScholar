@extends('emails.layout')

@section('body')
  <p>Dear {{ $student->full_name ?: 'Applicant' }},</p>
  <p style="font-family:Georgia, serif; font-size:19px; color:#8f1c2b; font-weight:bold;">Congratulations!</p>
  <p>We are delighted to let you know that your scholarship application <strong>{{ $student->application_number }}</strong> has been <strong>accepted</strong> by the POH Scholarships Scheme.</p>
  <p>To help us process your scholarship, please log in to your student portal and complete your <strong>Final Information</strong> - your bank details for disbursement and an emergency contact.</p>
  <p style="text-align:center; margin:26px 0;">
    <a href="{{ route('student.login') }}" style="background:#8f1c2b; color:#fff; padding:11px 22px; border-radius:4px; text-decoration:none; font-family:Arial,sans-serif; font-weight:bold; display:inline-block;">Log In to My Portal</a>
  </p>
  <p style="font-size:13px; color:#5a4f47;">Once logged in, the Final Information form is here: <a href="{{ route('student.final_info.edit') }}">{{ route('student.final_info.edit') }}</a></p>
  <p>Please also note that as a POH scholar, you will be required to submit a short academic result update every six months - your portal will show you when the next update is due.</p>
  <p>Once again, congratulations, and welcome to the POH Scholarships family.</p>
  <p>Yours sincerely,<br>POH Scholarships Scheme</p>
@endsection
