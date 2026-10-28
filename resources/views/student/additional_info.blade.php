@extends('layouts.student')

@section('content')
<span class="eyebrow">Section J, K &amp; L</span>
<h1>Other Information &amp; Declaration</h1>
<p style="color:var(--ink-soft); margin-bottom:26px;">A few final questions, your character referees, and the declaration required to submit your application.</p>

@if ($locked)
  <div class="locked-note">Your application has been submitted, so this section is now locked for editing.</div>
@endif

<div class="form-card">
  <form method="post" action="{{ route('student.additional_info.update') }}" novalidate>
    @csrf
    @method('PUT')
    <fieldset {{ $locked ? 'disabled' : '' }}>

    <fieldset>
      <legend>Other Information</legend>

      <div class="form-group">
        <label>Have you previously applied for a scholarship or sponsorship with any organisation or institution, whether public or private?</label>
        <div style="display:flex; gap:20px; margin:6px 0;">
          <label style="font-weight:400;"><input type="radio" name="prev_scholarship_flag" value="1" {{ (string) old('prev_scholarship_flag', $student->prev_scholarship_flag) === '1' ? 'checked' : '' }} {{ $locked ? 'disabled' : '' }}> Yes</label>
          <label style="font-weight:400;"><input type="radio" name="prev_scholarship_flag" value="0" {{ (string) old('prev_scholarship_flag', $student->prev_scholarship_flag) === '0' ? 'checked' : '' }} {{ $locked ? 'disabled' : '' }}> No</label>
        </div>
        <div id="prevScholarshipDetails" style="{{ (string) old('prev_scholarship_flag', $student->prev_scholarship_flag) === '1' ? '' : 'display:none;' }}">
          <textarea name="prev_scholarship_details" placeholder="Please provide details, including the year and course" {{ $locked ? 'readonly' : '' }}>{{ old('prev_scholarship_details', $student->prev_scholarship_details) }}</textarea>
        </div>
      </div>

      <div class="form-group">
        <label>Have you ever suffered, or are you suffering, from any physical impairment or disease?</label>
        <div style="display:flex; gap:20px; margin:6px 0;">
          <label style="font-weight:400;"><input type="radio" name="health_condition_flag" value="1" {{ (string) old('health_condition_flag', $student->health_condition_flag) === '1' ? 'checked' : '' }} {{ $locked ? 'disabled' : '' }}> Yes</label>
          <label style="font-weight:400;"><input type="radio" name="health_condition_flag" value="0" {{ (string) old('health_condition_flag', $student->health_condition_flag) === '0' ? 'checked' : '' }} {{ $locked ? 'disabled' : '' }}> No</label>
        </div>
        <div id="healthDetails" style="{{ (string) old('health_condition_flag', $student->health_condition_flag) === '1' ? '' : 'display:none;' }}">
          <textarea name="health_condition_details" placeholder="Please provide details" {{ $locked ? 'readonly' : '' }}>{{ old('health_condition_details', $student->health_condition_details) }}</textarea>
        </div>
      </div>

      <div class="form-group">
        <label>Have you ever been charged in a court of law in any country?</label>
        <div style="display:flex; gap:20px; margin:6px 0;">
          <label style="font-weight:400;"><input type="radio" name="legal_charge_flag" value="1" {{ (string) old('legal_charge_flag', $student->legal_charge_flag) === '1' ? 'checked' : '' }} {{ $locked ? 'disabled' : '' }}> Yes</label>
          <label style="font-weight:400;"><input type="radio" name="legal_charge_flag" value="0" {{ (string) old('legal_charge_flag', $student->legal_charge_flag) === '0' ? 'checked' : '' }} {{ $locked ? 'disabled' : '' }}> No</label>
        </div>
        <div id="legalDetails" style="{{ (string) old('legal_charge_flag', $student->legal_charge_flag) === '1' ? '' : 'display:none;' }}">
          <textarea name="legal_charge_details" placeholder="Please provide details" {{ $locked ? 'readonly' : '' }}>{{ old('legal_charge_details', $student->legal_charge_details) }}</textarea>
        </div>
      </div>
    </fieldset>

    <fieldset>
      <legend>Character Referees</legend>
      <p class="hint" style="margin-top:0;">Name 2 persons who are not your relatives. Their reference letters should be attached on the Certificates page.</p>
      <div class="table-wrap">
        <table class="data-table">
          <thead><tr><th>Name</th><th>Address</th><th>Occupation</th><th style="width:13%;">Telephone</th><th style="width:11%;">Years Known</th></tr></thead>
          <tbody>
            @foreach ($referees as $row)
              <tr>
                <td><input type="text" name="ref_name[]" value="{{ $row['name'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
                <td><input type="text" name="ref_address[]" value="{{ $row['address'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
                <td><input type="text" name="ref_occupation[]" value="{{ $row['occupation'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
                <td><input type="tel" name="ref_telephone[]" inputmode="numeric" pattern="[0-9]*" maxlength="15" data-digits-only value="{{ $row['telephone'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
                <td><input type="text" name="ref_years_known[]" value="{{ $row['years_known'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </fieldset>

    <fieldset>
      <legend>Declaration</legend>
      <p style="color:var(--ink-soft);">
        I declare that the information supplied in this application (Sections A&ndash;K) is true and accurate and the
        attached essay is completely and wholly the result of my own endeavours. I have not wilfully suppressed or
        falsified any information.
      </p>
      <div class="form-row">
        <div class="form-group required">
          <label for="declaration_signature">Signature (type your full name)</label>
          <input type="text" id="declaration_signature" name="declaration_signature" value="{{ old('declaration_signature', $student->declaration_signature) }}" {{ $locked ? 'readonly' : '' }}>
        </div>
        <div class="form-group required">
          <label for="declaration_date">Date</label>
          <input type="date" id="declaration_date" name="declaration_date" value="{{ old('declaration_date', optional($student->declaration_date)->format('Y-m-d') ?: date('Y-m-d')) }}" {{ $locked ? 'readonly' : '' }}>
        </div>
      </div>
      <div class="form-group">
        <label style="font-weight:400; display:flex; gap:8px; align-items:flex-start;">
          <input type="checkbox" name="declaration_agreed" value="1" style="margin-top:4px;" {{ old('declaration_agreed', $student->declaration_agreed) ? 'checked' : '' }} {{ $locked ? 'disabled' : '' }}>
          <span>I agree to the declaration above. I understand this must be checked before my application can be submitted.</span>
        </label>
      </div>
    </fieldset>

    </fieldset>

    @unless ($locked)
      <button type="submit" class="btn btn--block">Save</button>
    @endunless
  </form>
</div>

@unless ($locked)
<script>
  function wireToggle(radioName, detailsId) {
    var radios = document.getElementsByName(radioName);
    var details = document.getElementById(detailsId);
    if (!radios.length || !details) return;
    var update = function () {
      var checked = document.querySelector('input[name="' + radioName + '"]:checked');
      details.style.display = (checked && checked.value === '1') ? '' : 'none';
    };
    for (var i = 0; i < radios.length; i++) {
      radios[i].addEventListener('change', update);
    }
  }
  wireToggle('prev_scholarship_flag', 'prevScholarshipDetails');
  wireToggle('health_condition_flag', 'healthDetails');
  wireToggle('legal_charge_flag', 'legalDetails');
</script>
@endunless
@endsection
