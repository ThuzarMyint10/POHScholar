<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $pageTitle ?? 'My Application' }} | Student Portal | {{ config('app.name') }}</title>
<link rel="icon" type="image/png" href="{{ asset('assets/images/header.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/portal.css') }}">
</head>
<body class="portal-body

@php($student = auth('student')->user())

<header class="portal-header">
  <div class="container">
    <a href="{{ route('student.dashboard') }}" class="brand">
      <img src="{{ asset('assets/images/pohscholar.png') }}" alt="{{ config('app.name') }}" class="brand-logo">
    </a>
    <nav class="portal-nav">
      <a href="{{ route('student.dashboard') }}" class="{{ ($active ?? '') === 'dashboard' ? 'is-active' : '' }}">Dashboard</a>
      <a href="{{ route('student.profile.edit') }}" class="{{ ($active ?? '') === 'profile' ? 'is-active' : '' }}">My Profile</a>
      <a href="{{ route('student.academic_record.edit') }}" class="{{ ($active ?? '') === 'academic_record' ? 'is-active' : '' }}">Academic Record</a>
      <a href="{{ route('student.family_background.edit') }}" class="{{ ($active ?? '') === 'family_background' ? 'is-active' : '' }}">Family &amp; Background</a>
      <a href="{{ route('student.activities.edit') }}" class="{{ ($active ?? '') === 'activities' ? 'is-active' : '' }}">Activities</a>
      <a href="{{ route('student.essay.edit') }}" class="{{ ($active ?? '') === 'essay' ? 'is-active' : '' }}">Essay</a>
      <a href="{{ route('student.additional_info.edit') }}" class="{{ ($active ?? '') === 'additional_info' ? 'is-active' : '' }}">Other Info</a>
      <a href="{{ route('student.documents.index') }}" class="{{ ($active ?? '') === 'documents' ? 'is-active' : '' }}">Certificates</a>
      @if ($student && $student->status !== 'Draft')
        <a href="{{ route('student.application_pdf') }}" class="{{ ($active ?? '') === 'pdf' ? 'is-active' : '' }}">Print / PDF</a>
        <a href="{{ route('student.application_pdf.download') }}">Download PDF</a>
      @endif
      @if ($student && $student->accepted_at)
        <a href="{{ route('student.final_info.edit') }}" class="{{ ($active ?? '') === 'final_info' ? 'is-active' : '' }}">Final Information</a>
        <a href="{{ route('student.academic_results.index') }}" class="{{ ($active ?? '') === 'academic_results' ? 'is-active' : '' }}">Academic Results</a>
      @endif
      <span class="portal-user">{{ $student->email ?? '' }}</span>
      <form method="post" action="{{ route('student.logout') }}" style="display:inline;">
        @csrf
        <button type="submit" class="btn btn--sm btn--outline">Log Out</button>
      </form>
    </nav>
  </div>
</header>

<main class="portal-main">
  <div class="container">
    @if (session('success'))
      <div class="alert alert--success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
      <div class="alert alert--error">{{ session('error') }}</div>
    @endif
    @if (session('info'))
      <div class="alert alert--info">{{ session('info') }}</div>
    @endif
    @if ($errors->any())
      <div class="alert alert--error">
        <strong>Please correct the following:</strong>
        <ul style="margin:8px 0 0 18px;">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @yield('content')
  </div>
</main>
<script src="{{ asset('assets/js/main.js') }}"></script>
</body>
</html>
