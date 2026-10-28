<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $pageTitle ?? 'Admin Dashboard' }} | Admin | {{ config('app.name') }}</title>
<link rel="icon" type="image/png" href="{{ asset('assets/images/header.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
</head>
<body class="admin-body">
<div class="admin-shell">

@php
$admin = auth('admin')->user();
try {
    $pendingCount = \App\Models\QuickApplication::query()->where('status', 'Pending')->count();
    $unreadCount = \App\Models\ContactMessage::query()->where('is_read', false)->count();
    $studentsSubmittedCount = \App\Models\Student::query()->whereIn('status', ['Submitted', 'Under Review'])->count();
} catch (\Throwable $e) {
    $pendingCount = 0; $unreadCount = 0; $studentsSubmittedCount = 0;
}
@endphp

  <header class="admin-topheader">
    <div class="admin-topheader-inner">
      <a href="{{ route('admin.dashboard') }}" class="admin-logo-link">
        <img src="{{ asset('assets/images/pohscholar.png') }}" alt="{{ config('app.name') }}" class="admin-logo">
      </a>
      <div class="admin-user">
        <span>{{ $admin->full_name ?? '' }} <span style="color:var(--ink-soft);">({{ ucfirst($admin->role ?? '') }})</span></span>
        <span class="avatar">{{ strtoupper(substr($admin->full_name ?? '?', 0, 1)) }}</span>
      </div>
    </div>
  </header>

  <aside class="admin-sidebar">
    <ul class="admin-nav">
      <li><a href="{{ route('admin.dashboard') }}" class="{{ ($active ?? '') === 'dashboard' ? 'is-active' : '' }}">&#9632; Dashboard</a></li>

      <li class="nav-section-label">Scholarships</li>
      <li><a href="{{ route('admin.students.index') }}" class="{{ ($active ?? '') === 'students' ? 'is-active' : '' }}">&#127979; Student Applications {{ $studentsSubmittedCount ? '('.$studentsSubmittedCount.')' : '' }}</a></li>
      <li><a href="{{ route('admin.applications.index') }}" class="{{ ($active ?? '') === 'applications' ? 'is-active' : '' }}">&#128196; Quick Applications {{ $pendingCount ? '('.$pendingCount.')' : '' }}</a></li>
      <li><a href="{{ route('admin.scholars.index') }}" class="{{ ($active ?? '') === 'scholars' ? 'is-active' : '' }}">&#127891; Scholar Register</a></li>
      <li><a href="{{ route('admin.programs.index') }}" class="{{ ($active ?? '') === 'programs' ? 'is-active' : '' }}">&#9733; Scholarship Programs</a></li>
      <li><a href="{{ route('admin.addresses.index') }}" class="{{ ($active ?? '') === 'addresses' ? 'is-active' : '' }}">&#9873; Address Settings</a></li>
      <li><a href="{{ route('admin.invite.show') }}" class="{{ ($active ?? '') === 'invite' ? 'is-active' : '' }}">&#9993; Invite a Candidate</a></li>

      <li class="nav-section-label">Content</li>
      <li><a href="{{ route('admin.news.index') }}" class="{{ ($active ?? '') === 'news' ? 'is-active' : '' }}">&#128240; News &amp; Events</a></li>
      <li><a href="{{ route('admin.messages.index') }}" class="{{ ($active ?? '') === 'messages' ? 'is-active' : '' }}">&#9993; Messages {{ $unreadCount ? '('.$unreadCount.')' : '' }}</a></li>
      <li><a href="{{ route('admin.settings.edit') }}" class="{{ ($active ?? '') === 'settings' ? 'is-active' : '' }}">&#9881; Site Settings</a></li>

      @if (($admin->role ?? '') === 'administrator')
      <li class="nav-section-label">Administration</li>
      <li><a href="{{ route('admin.users.index') }}" class="{{ ($active ?? '') === 'users' ? 'is-active' : '' }}">&#128100; Admin Users</a></li>
      @endif

      <li class="nav-section-label">&nbsp;</li>
      <li><a href="{{ route('home') }}" target="_blank">&#8599; View Public Site</a></li>
      <li>
        <form method="post" action="{{ route('admin.logout') }}">
          @csrf
          <button type="submit" style="background:none;border:none;padding:0;font:inherit;cursor:pointer;color:inherit;text-align:left;width:100%;">&#8630; Log Out</button>
        </form>
      </li>
    </ul>
  </aside>

  <div class="admin-main">
    <div class="admin-topbar">
      <h1>{{ $pageTitle ?? 'Admin Dashboard' }}</h1>
    </div>
    <div class="admin-content">
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
  </div>
</div>
</body>
</html>
