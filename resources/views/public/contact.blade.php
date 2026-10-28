@extends('layouts.public')

@section('content')
<div class="breadcrumb-bar">
  <div class="container">
    <span class="eyebrow">Contact Us</span>
    <h1>Get in Touch</h1>
    <p>Questions about our scholarship programmes, donating, or volunteering? We would love to hear from you.</p>
  </div>
</div>

<section class="section">
  <div class="container grid grid--2" style="align-items:start;">
    <div>
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

      <div class="form-card">
        <h3>Send Us a Message</h3>
        <form method="post" action="{{ route('contact.store') }}" novalidate>
          @csrf
          <div class="form-row">
            <div class="form-group required">
              <label for="name">Full Name</label>
              <input type="text" id="name" name="name" value="{{ $old['name'] ?? '' }}" required>
            </div>
            <div class="form-group required">
              <label for="email">Email Address</label>
              <input type="email" id="email" name="email" value="{{ $old['email'] ?? '' }}" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input type="tel" id="phone" name="phone" inputmode="numeric" pattern="[0-9]*" maxlength="15" data-digits-only value="{{ $old['phone'] ?? '' }}">
            </div>
            <div class="form-group">
              <label for="subject">Subject</label>
              <input type="text" id="subject" name="subject" value="{{ $old['subject'] ?? '' }}">
            </div>
          </div>
          <div class="form-group required">
            <label for="message">Message</label>
            <textarea id="message" name="message" required>{{ $old['message'] ?? '' }}</textarea>
          </div>
          <button type="submit" class="btn">Send Message</button>
        </form>
      </div>
    </div>

    <div>
      <div class="card" style="margin-bottom:20px;"><div class="card-body">
        <h3>Our Office</h3>
        <p style="color:var(--ink-soft);">{{ setting('contact_address') }}</p>
        <p style="color:var(--ink-soft); margin-bottom:0;">{{ setting('contact_office_hours') }}</p>
      </div></div>
      <div class="card" style="margin-bottom:20px;"><div class="card-body">
        <h3>Phone &amp; Email</h3>
        <p style="color:var(--ink-soft); margin-bottom:6px;">&#9742; {{ setting('contact_phone') }}</p>
        <p style="color:var(--ink-soft); margin-bottom:0;">&#9993; {{ setting('contact_email') }}</p>
      </div></div>
      <div class="card"><div class="card-body">
        <h3>Support Our Work</h3>
        <p style="color:var(--ink-soft); margin-bottom:0;">{{ setting('donate_info') }}</p>
      </div></div>
    </div>
  </div>
</section>
@endsection
