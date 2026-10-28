<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login | {{ config('app.name') }}</title>
<link rel="icon" type="image/png" href="{{ asset('assets/images/header.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
<script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
</head>
<body>
<div class="login-wrap">
  <div class="login-card">
    <img src="{{ asset('assets/images/pohscholar.png') }}" alt="{{ config('app.name') }}" style="height:44px; margin-bottom:22px;">
    <h2 style="margin-bottom:4px;">Admin Login</h2>
    <p style="color:var(--ink-soft); margin-bottom:24px;">Staff access to the POH Scholarships dashboard.</p>

    @if ($timeout ?? false)
      <div class="alert alert--info">You were logged out due to inactivity.</div>
    @endif
    @if (session('error'))
      <div class="alert alert--error">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
      <div class="alert alert--error">{{ $errors->first() }}</div>
    @endif

    <form method="post" action="{{ route('admin.login.store') }}" style="text-align:left;">
      @csrf
      <div class="form-group required">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus>
      </div>
      <div class="form-group required">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      </div>
      <div class="form-group">
        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
      </div>
      <button type="submit" class="btn btn--block">Log In</button>
    </form>
    <script>
      document.querySelector('form[action="{{ route('admin.login.store') }}"]').addEventListener('submit', function (e) {
        e.preventDefault();
        var form = this;
        grecaptcha.ready(function () {
          grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', { action: 'login' }).then(function (token) {
            document.getElementById('g-recaptcha-response').value = token;
            form.submit();
          });
        });
      });
    </script>
    <p style="margin-top:20px;"><a href="{{ route('home') }}">&larr; Back to Public Site</a></p>
  </div>
</div>
</body>
</html>