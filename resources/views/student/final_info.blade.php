@extends('layouts.student')

@section('content')
<span class="eyebrow">Post-Acceptance</span>
<h1>Final Information</h1>
<p style="color:var(--ink-soft); margin-bottom:26px;">
  Congratulations on your acceptance! Please provide your bank details for scholarship disbursement and an emergency contact. You can update this information at any time.
</p>

@unless ($unlocked)
  <div class="locked-note">
    This form will become available once your scholarship application has been accepted. Please check back after you receive your acceptance notification.
  </div>
@else

  <div class="form-card">
    <form method="post" action="{{ route('student.final_info.update') }}" novalidate>
      @csrf
      @method('PUT')

      <fieldset>
        <legend>Bank Details (for Disbursement)</legend>
        <div class="form-group required">
          <label for="bank_account_name">Account Holder Name</label>
          <input type="text" id="bank_account_name" name="bank_account_name" value="{{ old('bank_account_name', $student->bank_account_name) }}" required>
        </div>
        <div class="form-row">
          <div class="form-group required">
            <label for="bank_name">Bank Name</label>
            <input type="text" id="bank_name" name="bank_name" value="{{ old('bank_name', $student->bank_name) }}" required>
          </div>
          <div class="form-group">
            <label for="bank_branch">Branch</label>
            <input type="text" id="bank_branch" name="bank_branch" value="{{ old('bank_branch', $student->bank_branch) }}">
          </div>
        </div>
        <div class="form-group required">
          <label for="bank_account_number">Account Number</label>
          <input type="text" id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number', $student->bank_account_number) }}" required>
        </div>
      </fieldset>

      <fieldset>
        <legend>Emergency Contact</legend>
        <div class="form-row">
          <div class="form-group required">
            <label for="emergency_contact_name">Contact Name</label>
            <input type="text" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name', $student->emergency_contact_name) }}" required>
          </div>
          <div class="form-group required">
            <label for="emergency_contact_phone">Contact Phone</label>
            <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone" inputmode="numeric" pattern="[0-9]*" maxlength="15" data-digits-only value="{{ old('emergency_contact_phone', $student->emergency_contact_phone) }}" required>
          </div>
        </div>
        <div class="form-group">
          <label for="emergency_contact_relationship">Relationship to You</label>
          <input type="text" id="emergency_contact_relationship" name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship', $student->emergency_contact_relationship) }}" placeholder="e.g. Mother, Father, Guardian">
        </div>
      </fieldset>

      @if ($student->final_info_updated_at)
        <p class="hint">Last updated {{ format_date($student->final_info_updated_at, 'j M Y, g:i a') }}</p>
      @endif

      <button type="submit" class="btn btn--block">Save Final Information</button>
    </form>
  </div>
@endunless
@endsection
