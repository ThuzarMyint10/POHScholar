<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $pageTitle ?? config('app.name') }}</title>
<meta name="description" content="POH Scholarships Scheme - providing university scholarships to deserving students since 2009.">
<link rel="icon" type="image/png" href="{{ asset('assets/images/header.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>

<div class="topbar">
  <div class="container">
    <div class="topbar-links">
      <span>&#9742; {{ setting('contact_phone') }}</span>
      <span>&#9993; {{ setting('contact_email') }}</span>
    </div>
    <div class="topbar-links">
      <span>{{ config('app.name') }} &mdash; Empowering University Students</span>
    </div>
  </div>
</div>

<header class="site-header">
  <div class="container">
    <a href="{{ route('home') }}" class="brand">
      <img src="{{ asset('assets/images/pohscholar.png') }}" alt="{{ config('app.name') }}" class="brand-logo">
    </a>

    <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation" aria-expanded="false">
      <span></span>
    </button>

    <nav class="main-nav" id="mainNav">
      <ul>
        <li><a href="{{ route('home') }}" class="{{ ($activeNav ?? '') === 'home' ? 'is-active' : '' }}">Home</a></li>
        <li><a href="{{ route('about') }}" class="{{ ($activeNav ?? '') === 'about' ? 'is-active' : '' }}">About Us</a></li>
        <li><a href="{{ route('contact') }}" class="{{ ($activeNav ?? '') === 'contact' ? 'is-active' : '' }}">Contact</a></li>
        <li><a href="{{ route('student.login') }}" class="{{ ($activeNav ?? '') === 'student-portal' ? 'is-active' : '' }}">Student Portal</a></li>
      </ul>
      <a href="{{ route('admin.login') }}" class="btn btn--sm" style="color:#fff;">Admin Login</a>
    </nav>
  </div>
</header>

@if (session('success'))
  <div class="container" style="padding-top:18px;"><div class="alert alert--success">{{ session('success') }}</div></div>
@endif
@if (session('error'))
  <div class="container" style="padding-top:18px;"><div class="alert alert--error">{{ session('error') }}</div></div>
@endif

@yield('content')

<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div>
        <div class="footer-brand">
          <img src="{{ asset('assets/images/pohscholar-footer.png') }}" alt="{{ config('app.name') }}">
        </div>
        <p style="max-width:320px; color:#d9c5b4;">{{ setting('footer_about') }}</p>
      </div>
      <div>
        <h4>Quick Links</h4>
        <ul>
          <li><a href="{{ route('about') }}">About Us</a></li>
          <li><a href="{{ route('programs') }}">Scholarship Programs</a></li>
          <li><a href="{{ route('student.register') }}">Apply for a Scholarship</a></li>
          <li><a href="{{ route('contact') }}">Contact Us</a></li>
        </ul>
      </div>
      <div>
        <h4>Contact</h4>
        <ul>
          <li>{{ setting('contact_address') }}</li>
          <li>{{ setting('contact_phone') }}</li>
          <li>{{ setting('contact_email') }}</li>
          <li>{{ setting('contact_office_hours') }}</li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</span>
      <span>Since 2009</span>
    </div>
  </div>
</footer>
<script src="{{ asset('assets/js/main.js') }}"></script>
</body>
</html>
