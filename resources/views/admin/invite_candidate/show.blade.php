@extends('layouts.admin')

@section('content')
<div class="panel">
  <div class="panel-head"><h3>Invite a Candidate to Apply</h3></div>
  <div class="panel-body">
    <p style="color:var(--ink-soft); margin-top:0;">
      Use this to invite a prospective candidate to apply for a <strong>university scholarship</strong>. They
      don't need an account yet; this sends them a short congratulatory email with a link to the online
      application form.
    </p>

    <form method="post" action="{{ route('admin.invite.send') }}" novalidate>
      @csrf
      <div class="form-row">
        <div class="form-group required">
          <label for="name">Candidate Full Name</label>
          <input type="text" id="name" name="name" value="{{ old('name') }}" required>
        </div>
        <div class="form-group required">
          <label for="email">Candidate Email</label>
          <input type="email" id="email" name="email" value="{{ old('email') }}" required>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="achievement">Notable Achievement (optional, for your reference)</label>
          <textarea id="achievement" name="achievement">{{ old('achievement') }}</textarea>
        </div>
        <div class="form-group">
          <label for="recommended_by">Recommended By (optional)</label>
          <input type="text" id="recommended_by" name="recommended_by" value="{{ old('recommended_by') }}">
        </div>
      </div>
      <button type="submit" class="btn btn--block">Send Invitation Email</button>
    </form>
  </div>
</div>
@endsection
