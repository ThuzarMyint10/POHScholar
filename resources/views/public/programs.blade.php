@extends('layouts.public')

@section('content')
<div class="breadcrumb-bar">
  <div class="container">
    <span class="eyebrow">Scholarship Programs</span>
    <h1>Programs &amp; How to Apply</h1>
    <p>Explore our scholarship programmes below, then follow the simple steps to apply online.</p>
  </div>
</div>

<section class="section">
  <div class="container">
    @foreach ($programs as $p)
    <div id="{{ $p->slug }}" class="card" style="margin-bottom:28px; flex-direction:row; scroll-margin-top:110px;">
      <div class="card-body">
        <span class="tag">Program</span>
        <h2>{{ $p->title }}</h2>
        <p style="color:var(--ink-soft);">{!! nl2br(e($p->description)) !!}</p>
        <div class="grid grid--2" style="margin-top:18px;">
          <div>
            <h4 style="color:var(--maroon); font-family:var(--sans); text-transform:uppercase; font-size:0.85rem; letter-spacing:0.05em;">Eligibility</h4>
            <p style="color:var(--ink-soft);">{!! nl2br(e($p->eligibility)) !!}</p>
          </div>
          <div>
            <h4 style="color:var(--maroon); font-family:var(--sans); text-transform:uppercase; font-size:0.85rem; letter-spacing:0.05em;">Benefits</h4>
            <p style="color:var(--ink-soft);">{!! nl2br(e($p->benefits)) !!}</p>
          </div>
        </div>
        <a href="{{ route('student.register') }}" class="btn">Apply for This Program</a>
        <!-- <a href="{{ route('apply', ['program' => $p->id]) }}" class="btn">Apply for This Program</a> -->
      </div>
    </div>
    @endforeach
    @if ($programs->isEmpty())
      <p>Programme information will be published here shortly. Please check back soon.</p>
    @endif
  </div>
</section>

<section class="section section--pearl-deep">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Application Process</span>
      <h2>How to Apply</h2>
      <p>Applying for a POH scholarship takes just a few minutes. Here's what to expect.</p>
    </div>
    <div class="grid grid--2">
      <ol class="steps">
        <li>
          <div>
            <h3 style="margin-bottom:4px;">Check Eligibility</h3>
            <p style="color:var(--ink-soft); margin:0;">Review the eligibility criteria for our university scholarship, above.</p>
          </div>
        </li>
        <li>
          <div>
            <h3 style="margin-bottom:4px;">Complete the Online Form</h3>
            <p style="color:var(--ink-soft); margin:0;">Fill in the student's details, household information and reason for applying on our secure application form.</p>
          </div>
        </li>
        <li>
          <div>
            <h3 style="margin-bottom:4px;">Application Review</h3>
            <p style="color:var(--ink-soft); margin:0;">Our team reviews every application and may reach out for supporting documents or a short interview.</p>
          </div>
        </li>
        <li>
          <div>
            <h3 style="margin-bottom:4px;">Decision &amp; Onboarding</h3>
            <p style="color:var(--ink-soft); margin:0;">Successful applicants are notified and welcomed into the POH Scholars community.</p>
          </div>
        </li>
      </ol>
      <div class="form-card" style="text-align:center;">
        <h3>Ready to Apply?</h3>
        <p style="color:var(--ink-soft);">Have your personal details, university/institute information and household details ready before you begin.</p>
        <a href="{{ route('student.register') }}" class="btn btn--block">Start Your Application</a>
      </div>
    </div>
  </div>
</section>
@endsection
