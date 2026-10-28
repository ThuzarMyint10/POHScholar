<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Login | {{ config('app.name') }}</title>
<link rel="icon" type="image/png" href="{{ asset('assets/images/header.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
@if (config('services.recaptcha.mode') === 'v2')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@else
<script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
@endif
</head>
<body>
<div class="login-wrap">
  <div class="login-card">
    <img src="{{ asset('assets/images/pohscholar.png') }}" alt="{{ config('app.name') }}" style="height:44px; margin-bottom:22px;">
    <h2 style="margin-bottom:4px;">Student Portal Login</h2>
    <p style="color:var(--ink-soft); margin-bottom:24px;">Log in to manage your scholarship application.</p>

    @if ($timeout ?? false)
      <div class="alert alert--info">You were logged out due to inactivity.</div>
    @endif
    @if ($disabled ?? false)
      <div class="alert alert--error">Your account has been disabled. Please contact our office for assistance.</div>
    @endif
    @if (session('error'))
      <div class="alert alert--error">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
      <div class="alert alert--error">{{ $errors->first() }}</div>
    @endif

    <form method="post" action="{{ route('student.login.store') }}" style="text-align:left;">
      @csrf
      <div class="form-group required">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
      </div>
      <div class="form-group required">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      </div>
      @if (config('services.recaptcha.mode') === 'v2')
      <div class="form-group recaptcha-container">
        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
      </div>
      @else
      <div class="form-group">
        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
      </div>
      @endif
      <button type="submit" class="btn btn--block">Log In</button>
    </form>
    <script>
      @if (config('services.recaptcha.mode') !== 'v2')
      document.querySelector('form[action="{{ route('student.login.store') }}"]').addEventListener('submit', function (e) {
        e.preventDefault();
        var form = this;
        grecaptcha.ready(function () {
          grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', { action: 'login' }).then(function (token) {
            document.getElementById('g-recaptcha-response').value = token;
            form.submit();
          });
        });
      });
      @endif
    </script>
    <p style="margin-top:18px;">New here? <a href="{{ route('student.register') }}">Create an account</a></p>
    <p style="margin-top:10px;"><a href="{{ route('home') }}">&larr; Back to Public Site</a></p>
    <p class="recaptcha-attribution">
      This site is protected by reCAPTCHA and the Google
      <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">Privacy Policy</a> and
      <a href="https://policies.google.com/terms" target="_blank" rel="noopener">Terms of Service</a> apply.
    </p>
  </div>
</div>
</body>
</html>