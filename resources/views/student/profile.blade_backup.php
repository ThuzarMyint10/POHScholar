@extends('layouts.student')

@section('content')
<span class="eyebrow">Section A</span>
<h1>My Profile</h1>
<p style="color:var(--ink-soft); margin-bottom:26px;">Your personal particulars, for the POH Scholarships application form.</p>

@if ($locked)
  <div class="locked-note">Your application has been submitted, so your profile is now locked for editing. Contact our office if something needs correcting.</div>
@endif

<div class="form-card">
  <form method="post" action="{{ route('student.profile.update') }}" enctype="multipart/form-data" novalidate>
    @csrf
    @method('PUT')
    <fieldset {{ $locked ? 'disabled' : '' }}>

    <fieldset>
      <legend>Your Photo</legend>
      <div style="display:flex; align-items:center; gap:20px;">
        @if ($student->photo)
          {{-- NOTE: onerror lets you SEE when the path is wrong instead of a silent blank box --}}
          <img src="{{ asset('storage/'.$student->photo) }}"
               alt=""
               class="profile-photo-preview"
               onerror="this.onerror=null;this.src='{{ asset('images/placeholder-avatar.png') }}';this.dataset.broken='1';">
        @endif
        <div class="form-group required" style="margin:0; flex:1;">
          <label for="photo">{{ $student->photo ? 'Replace Photo' : 'Upload a recent photograph' }}</label>
          <input type="file" id="photo" name="photo" accept="image/png,image/jpeg,image/webp" {{ $locked ? 'disabled' : '' }}>
        </div>
      </div>
    </fieldset>

    <fieldset>
      <legend>Undergraduate Course &amp; Institution</legend>
      <div class="form-row">
        <div class="form-group">
          <label for="undergraduate_course">Undergraduate Course</label>
          <input type="text" id="undergraduate_course" name="undergraduate_course" value="{{ old('undergraduate_course', $student->undergraduate_course) }}" placeholder="e.g. BSc in Computer Science" {{ $locked ? 'readonly' : '' }}>
        </div>
        <div class="form-group required">
          <label for="institution_select">Institution</label>
          <select id="institution_select" name="institution_select" required {{ $locked ? 'disabled' : '' }}>
            <option value="">Select&hellip;</option>
            @foreach ($designatedInstitutions as $inst)
              <option value="{{ $inst }}" {{ old('institution_select', $institutionSelectValue) === $inst ? 'selected' : '' }}>{{ $inst }}</option>
            @endforeach
            <option value="Other" {{ old('institution_select', $institutionSelectValue) === 'Other' ? 'selected' : '' }}>Other</option>
          </select>
        </div>
      </div>
      <div class="form-group" id="institutionOtherGroup" style="{{ old('institution_select', $institutionSelectValue) === 'Other' ? '' : 'display:none;' }}">
        <label for="institution_other">Name of Institution</label>
        <input type="text" id="institution_other" name="institution_other" value="{{ old('institution_other', $institutionOtherValue) }}" {{ $locked ? 'readonly' : '' }}>
      </div>
      <div class="form-group">
        <label for="grade_or_year">Year of Study</label>
        <input type="text" id="grade_or_year" name="grade_or_year" value="{{ old('grade_or_year', $student->grade_or_year) }}" placeholder="e.g. 1st Year" {{ $locked ? 'readonly' : '' }}>
      </div>
      <div class="form-group">
        <label for="program_id">Which POH scholarship programme are you applying for?</label>
        <select id="program_id" name="program_id" {{ $locked ? 'disabled' : '' }}>
          <option value="">Not sure / general application</option>
          @foreach ($programs as $p)
            <option value="{{ $p->id }}" {{ (string) old('program_id', $student->program_id) === (string) $p->id ? 'selected' : '' }}>{{ $p->title }}</option>
          @endforeach
        </select>
      </div>
    </fieldset>

    <fieldset>
      <legend>Personal Particulars</legend>
      <div class="form-row">
        <div class="form-group required">
          <label for="full_name">Name</label>
          <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $student->full_name) }}" required {{ $locked ? 'readonly' : '' }}>
        </div>
        <div class="form-group required">
          <label for="nic_or_birth_cert">NIC / Fin No.</label>
          <input type="text" id="nic_or_birth_cert" name="nic_or_birth_cert" value="{{ old('nic_or_birth_cert', $student->nic_or_birth_cert) }}" required {{ $locked ? 'readonly' : '' }}>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group required">
          <label for="citizenship">Citizenship</label>
          <select id="citizenship" name="citizenship" required {{ $locked ? 'disabled' : '' }}>
            <option value="">Select&hellip;</option>
            @foreach ($countries as $c)
              <option value="{{ $c }}" {{ old('citizenship', $citizenship) === $c ? 'selected' : '' }}>{{ $c }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group required">
          <label for="gender">Gender</label>
          <select id="gender" name="gender" required {{ $locked ? 'disabled' : '' }}>
            <option value="">Select&hellip;</option>
            @foreach (['Male', 'Female', 'Other'] as $g)
              <option value="{{ $g }}" {{ old('gender', $student->gender) === $g ? 'selected' : '' }}>{{ $g }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="form-group required">
        <label for="date_of_birth">Date of Birth</label>
        <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', optional($student->date_of_birth)->format('Y-m-d')) }}" required {{ $locked ? 'readonly' : '' }}>
      </div>
      <div class="form-group required">
        <label for="address">Postal Address</label>
        <textarea id="address" name="address" required {{ $locked ? 'readonly' : '' }}>{{ old('address', $student->address) }}</textarea>
      </div>

      {{-- ===== REORDERED: Country now comes first, District depends on it ===== --}}
      <div class="form-row">
        <div class="form-group required">
          <label for="country">Country</label>
          <select id="country" name="country" required {{ $locked ? 'disabled' : '' }}>
            <option value="">Select&hellip;</option>
            @foreach ($countries as $c)
              <option value="{{ $c }}" {{ old('country', $country) === $c ? 'selected' : '' }}>{{ $c }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group required">
          <label for="district">District</label>
          <select id="district" name="district" required {{ $locked ? 'disabled' : '' }}>
            <option value="">Select a country first&hellip;</option>
            @foreach ($districts as $d)
              <option value="{{ $d }}" {{ old('district', $student->district) === $d ? 'selected' : '' }}>{{ $d }}</option>
            @endforeach
          </select>
          {{-- Shown instead of the dropdown when the selected country has no district list configured --}}
          <input type="text" id="district_freetext" name="district_freetext"
                 value="{{ old('district_freetext') }}"
                 placeholder="State / Province / District"
                 style="display:none;" {{ $locked ? 'readonly' : '' }}>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group required">
          <label for="phone">Contact / Handphone No.</label>
          <input type="tel" id="phone" name="phone"
                 inputmode="numeric" pattern="[0-9]*" maxlength="15"
                 value="{{ old('phone', $student->phone) }}" required {{ $locked ? 'readonly' : '' }}>
          <div class="hint">Numbers only, no spaces or dashes.</div>
        </div>
        <div class="form-group">
          <label>Email Address (account login)</label>
          <input type="email" value="{{ $student->email }}" disabled>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="ethnic_group">Ethnic Group</label>
          <select id="ethnic_group" name="ethnic_group" {{ $locked ? 'disabled' : '' }}>
            <option value="">Prefer not to say</option>
            @foreach ($ethnicGroups as $eg)
              <option value="{{ $eg }}" {{ old('ethnic_group', $ethnicSelectValue) === $eg ? 'selected' : '' }}>{{ $eg }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group" id="ethnicOtherGroup" style="{{ old('ethnic_group', $ethnicSelectValue) === 'Other' ? '' : 'display:none;' }}">
          <label for="ethnic_group_other">Please state</label>
          <input type="text" id="ethnic_group_other" name="ethnic_group_other" value="{{ old('ethnic_group_other', $ethnicOtherValue) }}" {{ $locked ? 'readonly' : '' }}>
        </div>
      </div>
      <div class="form-group">
        <label for="religion">Religion</label>
        <input type="text" id="religion" name="religion" value="{{ old('religion', $student->religion) }}" {{ $locked ? 'readonly' : '' }}>
        <div class="hint">Optional.</div>
      </div>
    </fieldset>

    <fieldset>
      <legend>Parent / Guardian &amp; Household</legend>
      <div class="form-row">
        <div class="form-group required">
          <label for="guardian_name">Parent / Guardian Name</label>
          <input type="text" id="guardian_name" name="guardian_name" value="{{ old('guardian_name', $student->guardian_name) }}" required {{ $locked ? 'readonly' : '' }}>
        </div>
        <div class="form-group">
          <label for="guardian_occupation">Parent / Guardian Occupation</label>
          <input type="text" id="guardian_occupation" name="guardian_occupation" value="{{ old('guardian_occupation', $student->guardian_occupation) }}" {{ $locked ? 'readonly' : '' }}>
        </div>
      </div>
      <div class="form-group">
        <label for="household_income_band">Approximate Monthly Household Income</label>
        <select id="household_income_band" name="household_income_band" {{ $locked ? 'disabled' : '' }}>
          <option value="">Prefer not to say</option>
          @foreach ($incomeBands as $band)
            @if ($band !== 'Prefer not to say')
              <option value="{{ $band }}" {{ old('household_income_band', $student->household_income_band) === $band ? 'selected' : '' }}>{{ $band }}</option>
            @endif
          @endforeach
        </select>
      </div>
      <p class="hint">Full family particulars (parents and siblings) are collected on the next step, <a href="{{ route('student.family_background.edit') }}">Family &amp; Background</a>.</p>
    </fieldset>

    </fieldset>

    @unless ($locked)
      <button type="submit" class="btn btn--block">Save Profile</button>
    @endunless
  </form>
</div>

@unless ($locked)
{{-- Searchable-dropdown library. Loaded only from Choices.js' CDN. --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/choices.js/10.2.0/styles/choices.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/choices.js/10.2.0/choices.min.js"></script>

<script>
  // ---- Institution "Other" toggle (unchanged) ----
  var instSelect = document.getElementById('institution_select');
  var instOther = document.getElementById('institutionOtherGroup');
  function toggleInstOther() {
    instOther.style.display = instSelect.value === 'Other' ? '' : 'none';
  }
  if (instSelect) { instSelect.addEventListener('change', toggleInstOther); }

  // ---- Ethnic group "Other" toggle (unchanged) ----
  var ethSelect = document.getElementById('ethnic_group');
  var ethOther = document.getElementById('ethnicOtherGroup');
  function toggleEthOther() {
    ethOther.style.display = ethSelect.value === 'Other' ? '' : 'none';
  }
  if (ethSelect) { ethSelect.addEventListener('change', toggleEthOther); }

  // ---- Phone: digits only, strip anything else as the user types ----
  var phoneInput = document.getElementById('phone');
  if (phoneInput) {
    phoneInput.addEventListener('input', function () {
      this.value = this.value.replace(/\D/g, '');
    });
  }

  // ---- Country -> District dependency ----
  // We only have a real district list for one "home" country (the $districts
  // array passed in from the controller). Set HOME_COUNTRY below to match
  // that country's exact label as it appears in your $countries list.
  var HOME_COUNTRY = "Sri Lanka"; // <-- change this to match your data

  var countrySelect = document.getElementById('country');
  var districtSelect = document.getElementById('district');
  var districtFreetext = document.getElementById('district_freetext');

  function syncDistrictField() {
    if (!countrySelect) return;
    var isHome = countrySelect.value === HOME_COUNTRY;

    districtSelect.style.display = isHome ? '' : 'none';
    districtSelect.disabled = !isHome;
    districtSelect.required = isHome;

    districtFreetext.style.display = isHome ? 'none' : '';
    districtFreetext.disabled = isHome;
    districtFreetext.required = !isHome && countrySelect.value !== '';
  }

  if (countrySelect) {
    countrySelect.addEventListener('change', syncDistrictField);
    syncDistrictField(); // run once on load, e.g. when editing an existing profile
  }

  // ---- Turn Country + District into searchable dropdowns ----
  // (District stays a plain select while hidden/disabled; Choices.js still
  // works on it once it's visible/enabled again.)
  if (window.Choices) {
    new Choices(countrySelect, { searchEnabled: true, itemSelectText: '', shouldSort: false });
    new Choices(districtSelect, { searchEnabled: true, itemSelectText: '', shouldSort: false });
  }
</script>
@endunless
@endsection