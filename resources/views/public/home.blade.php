@extends('layouts.public')

@section('content')
<section class="hero">
  <div class="container">
    <div>
      <span class="hero-badge">&#127942; Since 2009 &mdash; 17 Years of Impact</span>
      <h1>{{ setting('hero_heading', 'Every Deserving Student Deserves a Chance at University') }}</h1>
      <p class="lead">{{ setting('hero_subheading') }}</p>
      <div class="hero-actions">
        <a href="{{ route('student.register') }}" class="btn btn--gold">Apply for a Scholarship</a>
        <a href="{{ route('about') }}" class="btn btn--outline" style="background:transparent;border-color:#fff;color:#fff;">Learn About Us</a>
      </div>
    </div>
    <div class="hero-emblem">
      <img src="{{ asset('assets/images/header.png') }}" alt="POH Scholarships Scheme emblem">
    </div>
  </div>
</section>

<div class="container">
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

<section class="section">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Our Mission</span>
      <h2>Education Changes Everything</h2>
      <p>{{ setting('mission_statement') }}</p>
    </div>
  </div>
</section>

@if ($featuredScholars->isNotEmpty())
<section class="section">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Our Scholars</span>
      <h2>Meet Our Current Scholars</h2>
      <p>These students are currently receiving support through the POH Scholarships Scheme.</p>
    </div>
    <div class="grid grid--3">
      @foreach ($featuredScholars as $scholar)
      <div class="card">
        @if ($scholar->photo)
          <img src="{{ asset('storage/'.$scholar->photo) }}" alt="{{ $scholar->full_name }}" style="width:100%; height:220px; object-fit:cover;">
        @endif
        <div class="card-body">
          <h3>{{ $scholar->full_name }}</h3>
          @if ($scholar->program)
            <p style="color:var(--ink-soft); margin-bottom:8px;">{{ $scholar->program->title }}</p>
          @endif
          @if ($scholar->school_or_institute)
            <p style="margin:0;">{{ $scholar->school_or_institute }}</p>
          @endif
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>
@endif

<section class="section section--pearl-deep">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">What We Offer</span>
      <h2>Our Scholarship Program</h2>
      <p>We support outstanding students in completing their university education, covering tuition and other costs that might otherwise stand in the way.</p>
    </div>
    <div class="grid grid--3">
      @forelse ($programs as $i => $p)
      <div class="card">
        <div class="card-media"><span class="num-badge">0{{ $i + 1 }}</span></div>
        <div class="card-body">
          <h3>{{ $p->title }}</h3>
          <p>{{ $p->summary }}</p>
          <a href="{{ route('programs') }}#{{ $p->slug }}" class="btn btn--outline btn--sm">Learn More</a>
        </div>
      </div>
      @empty
        <p>Programme information will appear here shortly.</p>
      @endforelse
    </div>
    <div style="text-align:center; margin-top:34px;">
      <a href="{{ route('programs') }}" class="btn">View All Programs &amp; How to Apply</a>
    </div>
  </div>
</section>

<section class="section">
  <div class="container about-grid">
    <div class="about-emblem">
      <img src="{{ asset('assets/images/header.png') }}" alt="">
      <h3>Since 2009</h3>
      <p style="color:var(--ink-soft);">Supporting outstanding students to complete their university education, without interruption.</p>
    </div>
    <div>
      <span class="eyebrow">Why It Matters</span>
      <h2>Breaking the Cycle of Poverty Through Education</h2>
      <p>{{ setting('about_history') }}</p>
      <ul class="list-check">
        <li>Tuition and/or maintenance support for students at recognised universities</li>
        <li>Renewable each year, subject to satisfactory academic progress</li>
        <li>Awarded to outstanding students facing genuine financial hardship</li>
        <li>Ongoing mentorship and community support</li>
      </ul>
      <a href="{{ route('about') }}" class="btn btn--outline" style="margin-top:10px;">More About Us</a>
    </div>
  </div>
</section>

@if ($news->isNotEmpty())
<section class="section section--pearl-deep">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Latest Updates</span>
      <h2>News &amp; Announcements</h2>
    </div>
    <div class="grid grid--3">
      @foreach ($news as $n)
      <div class="card">
        <div class="card-body">
          <span class="tag">{{ $n->category }}</span>
          <h3>{{ $n->title }}</h3>
          <p>{{ $n->excerpt }}</p>
          <span class="hint" style="color:var(--ink-soft); font-size:0.82rem;">{{ format_date($n->event_date ?: $n->created_at) }}</span>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>
@endif

<section class="section section--maroon">
  <div class="container" style="text-align:center;">
    <h2>Help Us Reach More Students</h2>
    <p style="max-width:620px; margin:0 auto 26px; color:#f3e3d9;">{{ setting('donate_info') }}</p>
    <a href="{{ route('contact') }}" class="btn btn--gold">Get in Touch</a>
  </div>
</section>
@endsection
