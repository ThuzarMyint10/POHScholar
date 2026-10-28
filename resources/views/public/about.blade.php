@extends('layouts.public')

@section('content')
<div class="breadcrumb-bar">
  <div class="container">
    <span class="eyebrow">About Us</span>
    <h1>Our Story &amp; Mission</h1>
    <p>Since 2009 &mdash; committed to giving every deserving student a chance at a university education.</p>
  </div>
</div>

<section class="section">
  <div class="container about-grid">
    <div class="about-emblem">
      <img src="{{ asset('assets/images/header.png') }}" alt="">
      <h3>Our Emblem</h3>
      <p style="color:var(--ink-soft);">A symbol of the guidance and care we offer every scholar in our programme.</p>
    </div>
    <div>
      <span class="eyebrow">How We Began</span>
      <h2>Our History</h2>
      <p>{{ setting('about_history') }}</p>

      <span class="eyebrow" style="margin-top:20px; display:inline-block;">Looking Ahead</span>
      <h2>Our Vision</h2>
      <p>{{ setting('about_vision') }}</p>
    </div>
  </div>
</section>

<section class="section section--pearl-deep">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Our Mission</span>
      <h2>What Drives Us</h2>
      <p>{{ setting('mission_statement') }}</p>
    </div>
    <div class="stats">
      <div class="stat-card">
        <div class="num">{{ setting('stat_years_active') }}</div>
        <div class="label">Years Active</div>
      </div>
      <div class="stat-card">
        <div class="num">{{ setting('stat_scholars_supported') }}</div>
        <div class="label">Scholars Supported</div>
      </div>
      <div class="stat-card">
        <div class="num">{{ setting('stat_districts_reached') }}</div>
        <div class="label">Districts Reached</div>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Our Values</span>
      <h2>How We Work</h2>
    </div>
    <div class="grid grid--3">
      <div class="card"><div class="card-body">
        <h3>Integrity</h3>
        <p style="color:var(--ink-soft);">Every application is reviewed fairly, and funds are directed transparently to the students who need them most.</p>
      </div></div>
      <div class="card"><div class="card-body">
        <h3>Dignity</h3>
        <p style="color:var(--ink-soft);">We support students and families with respect, without judgement, so they can focus on learning.</p>
      </div></div>
      <div class="card"><div class="card-body">
        <h3>Community</h3>
        <p style="color:var(--ink-soft);">We work hand in hand with universities, community leaders and donors to reach the students who need us.</p>
      </div></div>
    </div>
  </div>
</section>

<section class="section section--maroon">
  <div class="container" style="text-align:center;">
    <h2>Join Us in Making a Difference</h2>
    <p style="max-width:600px; margin:0 auto 26px; color:#f3e3d9;">Whether you're a student in need of support or a supporter who wants to help, we would love to hear from you.</p>
    <div class="hero-actions" style="justify-content:center;">
      <a href="{{ route('apply') }}" class="btn btn--gold">Apply for a Scholarship</a>
      <a href="{{ route('contact') }}" class="btn btn--outline" style="border-color:#fff;color:#fff;">Contact Us</a>
    </div>
  </div>
</section>
@endsection
