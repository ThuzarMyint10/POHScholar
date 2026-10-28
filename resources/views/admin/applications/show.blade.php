@extends('layouts.admin')

@section('content')
<p><a href="{{ route('admin.applications.index') }}">&larr; Back to Applications</a></p>

<div class="grid grid--2" style="align-items:start;">
  <div class="panel">
    <div class="panel-head">
      <h3>{{ $app->full_name }}</h3>
      <span class="badge badge--{{ status_badge_class($app->status) }}">{{ $app->status }}</span>
    </div>
    <div class="panel-body">
      <table class="data-table">
        <tr><th style="width:40%;">Program</th><td>{{ $app->program->title ?? 'General application' }}</td></tr>
        <tr><th>Date of Birth</th><td>{{ format_date($app->date_of_birth) }}</td></tr>
        <tr><th>Gender</th><td>{{ $app->gender }}</td></tr>
        <tr><th>NIC / Birth Cert.</th><td>{{ $app->nic_or_birth_cert }}</td></tr>
        <tr><th>Address</th><td>{!! nl2br(e($app->address)) !!}</td></tr>
        <tr><th>District</th><td>{{ $app->district }}</td></tr>
        <tr><th>Country</th><td>{{ $app->country }}</td></tr>
        <tr><th>Phone</th><td>{{ $app->phone }}</td></tr>
        <tr><th>Email</th><td>{{ $app->email }}</td></tr>
        <tr><th>University / Institute</th><td>{{ $app->school_or_institute }}</td></tr>
        <tr><th>Year of Study</th><td>{{ $app->grade_or_year }}</td></tr>
        <tr><th>Guardian Name</th><td>{{ $app->guardian_name }}</td></tr>
        <tr><th>Guardian Occupation</th><td>{{ $app->guardian_occupation }}</td></tr>
        <tr><th>Household Income</th><td>{{ $app->household_income_band }}</td></tr>
        <tr><th>Submitted</th><td>{{ format_date($app->created_at, 'j M Y, g:i a') }}</td></tr>
      </table>
      <h4 style="margin-top:20px;">Reason for Applying</h4>
      <p style="color:var(--ink-soft);">{!! nl2br(e($app->reason_for_applying)) !!}</p>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head"><h3>Review &amp; Status</h3></div>
    <div class="panel-body">
      <form method="post" action="{{ route('admin.applications.update', $app->id) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
          <label for="status">Application Status</label>
          <select id="status" name="status">
            @foreach ($statuses as $s)
              <option value="{{ $s }}" {{ $app->status === $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label for="admin_notes">Internal Notes</label>
          <textarea id="admin_notes" name="admin_notes" placeholder="Notes visible to staff only">{{ $app->admin_notes }}</textarea>
        </div>
        <button type="submit" class="btn btn--block">Save Changes</button>
      </form>
    </div>
  </div>
</div>
@endsection
