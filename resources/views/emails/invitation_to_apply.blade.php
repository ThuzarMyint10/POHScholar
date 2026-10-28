@extends('emails.layout')

@section('body')
  <p style="font-family:Georgia, serif; font-size:22px; color:#8f1c2b; font-weight:bold; margin-bottom:10px;">Dear {{ $name ?: 'there' }},</p>

  <p>Congratulations on your achievement! We'd love to help you take the next step - please apply for a POH scholarship to support your university studies. Since 2009, we've helped students across Sri Lanka complete their degrees without financial worry, and we'd be glad to do the same for you. The online application only takes a few minutes to start, and our team is happy to help if you have any questions along the way.</p>

  <p style="text-align:center; margin:26px 0;">
    <a href="{{ route('student.register') }}" style="background:#8f1c2b; color:#fff; padding:12px 26px; border-radius:4px; text-decoration:none; font-family:Arial,sans-serif; font-weight:bold; display:inline-block;">Apply Now</a>
  </p>
  <p style="font-size:13px; color:#5a4f47;"><a href="{{ route('student.register') }}">{{ route('student.register') }}</a></p>

  <p>Warmly,<br>Program Coordinator<br>POH Scholarships Scheme</p>
@endsection