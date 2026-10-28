<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register | Student Portal | {{ config('app.name') }}</title>
<link rel="icon" type="image/png" href="{{ asset('assets/images/header.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
<script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
</head>
<body>
<div class="login-wrap">
  <div class="login-card" style="max-width:440px;">
    <img src="{{ asset('assets/images/pohscholar.png') }}" alt="{{ config('app.name') }}" style="height:44px; margin-bottom:22px;">
    <h2 style="margin-bottom:4px;">Create Your Student Account</h2>
    <p style="color:var(--ink-soft); margin-bottom:24px;">Register with your email to start your scholarship application.</p>

    @if (session('error'))
      <div class="alert alert--error" style="text-align:left;">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
      <div class="alert alert--error" style="text-align:left;">
        <ul style="margin:0 0 0 18px;">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="post" action="{{ route('student.register.store') }}" style="text-align:left;" novalidate>
      @csrf
      <div class="form-group required">
        <label for="full_name">Full Name</label>
        <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required autofocus>
      </div>
      <div class="form-group required">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required>
        <div class="hint">You'll use this to log in and check your application later.</div>
      </div>
      <div class="form-group required">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required autocomplete="new-password">
        <div class="hint">At least 8 characters.</div>
      </div>
      <div class="form-group required">
        <label for="password_confirmation">Confirm Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
      </div>
      <div class="form-group">
        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
      </div>
      <button type="submit" class="btn btn--block">Create Account</button>
    </form>
    <script>
      document.querySelector('form[action="{{ route('student.register.store') }}"]').addEventListener('submit', function (e) {
        e.preventDefault();
        var form = this;
        grecaptcha.ready(function () {
          grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', { action: 'register' }).then(function (token) {
            document.getElementById('g-recaptcha-response').value = token;
            form.submit();
          });
        });
      });
    </script>
    <p style="margin-top:18px;">Already registered? <a href="{{ route('student.login') }}">Log in</a></p>
    <p style="margin-top:10px;"><a href="{{ route('home') }}">&larr; Back to Public Site</a></p>
  </div>
</div>
</body>
</html>