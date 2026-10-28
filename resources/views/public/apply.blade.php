@extends('layouts.public')

@section('content')
<div class="breadcrumb-bar">
  <div class="container">
    <span class="eyebrow">Scholarship Application</span>
    <h1>Apply for a Scholarship</h1>
    <p>Please complete the form below as accurately as possible. All information is kept confidential and used only to assess eligibility.</p>
  </div>
</div>

<section class="section">
  <div class="container" style="max-width:860px;">

    @if (session('success'))
      <div class="alert alert--success">
        <strong>Thank you &mdash; your application has been received!</strong><br>
        Our team will review the details and get in touch using the contact information provided.
      </div>
    @endif

    @if ($errors->any())
      <div class="alert alert--error">
        <strong>Please correct the following before submitting:</strong>
        <ul style="margin:8px 0 0 18px;">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="form-card">
      <form method="post" action="{{ route('apply.store') }}" novalidate>
        @csrf

        <fieldset>
          <legend>Scholarship Program</legend>
          <div class="form-group">
            <label for="program_id">Which programme are you applying for?</label>
            <select id="program_id" name="program_id">
              <option value="">Not sure / general application</option>
              @foreach ($programs as $p)
                <option value="{{ $p->id }}" {{ (string) ($old['program_id'] ?? '') === (string) $p->id ? 'selected' : '' }}>{{ $p->title }}</option>
              @endforeach
            </select>
          </div>
        </fieldset>

        <fieldset>
          <legend>Applicant Details</legend>
          <div class="form-row">
            <div class="form-group required">
              <label for="full_name">Full Name</label>
              <input type="text" id="full_name" name="full_name" value="{{ $old['full_name'] ?? '' }}" required>
            </div>
            <div class="form-group required">
              <label for="date_of_birth">Date of Birth</label>
              <input type="date" id="date_of_birth" name="date_of_birth" value="{{ $old['date_of_birth'] ?? '' }}" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group required">
              <label for="gender">Gender</label>
              <select id="gender" name="gender" required>
                <option value="">Select&hellip;</option>
                @foreach (['Male', 'Female', 'Other'] as $g)
                  <option value="{{ $g }}" {{ ($old['gender'] ?? '') === $g ? 'selected' : '' }}>{{ $g }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="nic_or_birth_cert">NIC / Birth Certificate No.</label>
              <input type="text" id="nic_or_birth_cert" name="nic_or_birth_cert" value="{{ $old['nic_or_birth_cert'] ?? '' }}">
            </div>
          </div>
          <div class="form-group required">
            <label for="address">Home Address</label>
            <textarea id="address" name="address" required>{{ $old['address'] ?? '' }}</textarea>
          </div>
          @include('shared.address-fields', [
            'addressOptions' => $addressOptions,
            'selectedCountry' => $old['country'] ?? 'Sri Lanka',
            'selectedDistrict' => $old['district'] ?? '',
          ])
          <div class="form-row">
            <div class="form-group required">
              <label for="phone">Contact Phone</label>
              <input type="tel" id="phone" name="phone" inputmode="numeric" pattern="[0-9]*" maxlength="15" data-digits-only value="{{ $old['phone'] ?? '' }}" required>
            </div>
          </div>
          <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="{{ $old['email'] ?? '' }}">
            <div class="hint">Optional, but helpful if we need to follow up.</div>
          </div>
        </fieldset>

        <fieldset>
          <legend>Education</legend>
          <div class="form-row">
            <div class="form-group required">
              <label for="school_or_institute">University / Institute</label>
              <input type="text" id="school_or_institute" name="school_or_institute" value="{{ $old['school_or_institute'] ?? '' }}" required placeholder="e.g. University of Colombo">
            </div>
            <div class="form-group">
              <label for="grade_or_year">Year of Study</label>
              <input type="text" id="grade_or_year" name="grade_or_year" value="{{ $old['grade_or_year'] ?? '' }}" placeholder="e.g. 1st Year">
            </div>
          </div>
        </fieldset>

        <fieldset>
          <legend>Parent / Guardian &amp; Household</legend>
          <div class="form-row">
            <div class="form-group required">
              <label for="guardian_name">Parent / Guardian Name</label>
              <input type="text" id="guardian_name" name="guardian_name" value="{{ $old['guardian_name'] ?? '' }}" required>
            </div>
            <div class="form-group">
              <label for="guardian_occupation">Parent / Guardian Occupation</label>
              <input type="text" id="guardian_occupation" name="guardian_occupation" value="{{ $old['guardian_occupation'] ?? '' }}">
            </div>
          </div>
          <div class="form-group">
            <label for="household_income_band">Approximate Monthly Household Income</label>
            <select id="household_income_band" name="household_income_band">
              <option value="">Prefer not to say</option>
              @foreach ($incomeBands as $band)
                @if ($band !== 'Prefer not to say')
                  <option value="{{ $band }}" {{ ($old['household_income_band'] ?? '') === $band ? 'selected' : '' }}>{{ $band }}</option>
                @endif
              @endforeach
            </select>
            <div class="hint">Used only to help us assess financial need; kept strictly confidential.</div>
          </div>
        </fieldset>

        <fieldset>
          <legend>Application Statement</legend>
          <div class="form-group required">
            <label for="reason_for_applying">Why are you applying for this scholarship?</label>
            <textarea id="reason_for_applying" name="reason_for_applying" required placeholder="Tell us a little about the student's situation and educational goals.">{{ $old['reason_for_applying'] ?? '' }}</textarea>
          </div>
        </fieldset>

        <button type="submit" class="btn btn--block">Submit Application</button>
      </form>
    </div>
  </div>
</section>
@endsection
