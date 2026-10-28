@extends('layouts.admin')

@php
$yn = function ($flag) {
    if ($flag === null || $flag === '') return '—';
    return $flag ? 'Yes' : 'No';
};
$catLabel = ['school_activity' => 'School Activity', 'sport' => 'Sport', 'leadership' => 'Leadership / Community'];
@endphp

@section('content')
<style>
  .section-rank-row { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin: 22px 0 8px; }
  .section-rank-row h4 { margin: 0; }
  .section-rank-inputs { display: flex; gap: 14px; }
  .section-rank-inputs label { font-size: 0.82rem; color: var(--ink-soft); display: flex; align-items: center; gap: 6px; font-weight: 600; }
  .section-rank-inputs input { width: 62px; padding: 4px 6px; font-size: 0.85rem; border: 1px solid var(--line, #ddd0b8); border-radius: 4px; font-weight: 400; }
</style>

<p><a href="{{ route('admin.students.index') }}">&larr; Back to Student Applications</a></p>

<div class="grid grid--2" style="align-items:start; margin-bottom:10px;">
  <div>
    <h2 style="margin-bottom:4px;">{{ $student->full_name ?: $student->email }}</h2>
    <p style="color:var(--ink-soft);">
      <span class="badge badge--{{ status_badge_class($student->status) }}">{{ $student->status }}</span>
      &nbsp;{{ $student->application_number ?: 'Not yet submitted' }}
      &nbsp;<span class="badge badge--{{ $student->is_active ? 'approved' : 'rejected' }}">{{ $student->is_active ? 'Account Active' : 'Account Disabled' }}</span>
    </p>
  </div>
  <div style="text-align:right;">
    @if ($student->status !== 'Draft')
      <a href="{{ route('admin.students.pdf', $student->id) }}" class="btn btn--sm btn--outline" target="_blank">Print / PDF Copy</a>
      <a href="{{ route('admin.students.pdf_download', $student->id) }}" class="btn btn--sm btn--outline">Download PDF</a>
    @endif
  </div>
</div>

@if (in_array($student->status, ['Submitted', 'Under Review'], true))
<div class="panel" style="border:1px solid var(--maroon, #8f1c2b);">
  <div class="panel-body" style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
    <strong>Ready for a decision?</strong>
    <form method="post" action="{{ route('admin.students.update_status', $student->id) }}" style="margin:0;">
      @csrf
      @method('PUT')
      <input type="hidden" name="status" value="Approved">
      <button type="submit" class="btn btn--sm" onclick="return confirm('Accept this application? The candidate will be emailed immediately to complete their Final Information.');">&#10003; Accept &amp; Notify Candidate</button>
    </form>
    <form method="post" action="{{ route('admin.students.update_status', $student->id) }}" style="margin:0;">
      @csrf
      @method('PUT')
      <input type="hidden" name="status" value="Rejected">
      <button type="submit" class="btn btn--sm btn--outline" onclick="return confirm('Mark this application as rejected?');">&#10007; Reject</button>
    </form>
  </div>
</div>
@endif

<div class="grid grid--2" style="align-items:start;">
  <div>
    <div class="panel">
      <div class="panel-head"><h3>Applicant Details</h3></div>
      <div class="panel-body">
        @if ($student->photo)
          <img src="{{ asset('storage/'.$student->photo) }}" alt="" style="height:90px; border-radius:4px; margin-bottom:14px;">
        @endif
        <table class="data-table">
          <tr><th style="width:40%;">Email</th><td>{{ $student->email }}</td></tr>
          <tr><th>Program</th><td>{{ $student->program->title ?? 'General application' }}</td></tr>
          <tr><th>Date of Birth</th><td>{{ format_date($student->date_of_birth) }}</td></tr>
          <tr><th>Gender</th><td>{{ $student->gender }}</td></tr>
          <tr><th>NIC / Birth Cert.</th><td>{{ $student->nic_or_birth_cert }}</td></tr>
          <tr><th>Address</th><td>{!! nl2br(e($student->address)) !!}</td></tr>
          <tr><th>District</th><td>{{ $student->district }}</td></tr>
          <tr><th>Country</th><td>{{ $student->country }}</td></tr>
          <tr><th>Phone</th><td>{{ $student->phone }}</td></tr>
          <tr><th>University / Institute</th><td>{{ $student->school_or_institute }}</td></tr>
          <tr><th>Year of Study</th><td>{{ $student->grade_or_year }}</td></tr>
          <tr><th>Guardian Name</th><td>{{ $student->guardian_name }}</td></tr>
          <tr><th>Guardian Occupation</th><td>{{ $student->guardian_occupation }}</td></tr>
          <tr><th>Household Income</th><td>{{ $student->household_income_band }}</td></tr>
          <tr><th>Registered</th><td>{{ format_date($student->created_at, 'j M Y') }}</td></tr>
          <tr><th>Submitted</th><td>{{ $student->submitted_at ? format_date($student->submitted_at, 'j M Y, g:i a') : '—' }}</td></tr>
        </table>
        <form method="post" action="{{ route('admin.students.update_section_ranks', $student->id) }}" style="margin:0;">
          @csrf
          @method('PUT')
          @include('admin.students._section_heading', ['key' => 'I', 'label' => 'Essay'])
          <p style="color:var(--ink-soft); white-space:pre-wrap;">{{ $student->essay ?: '(Not written yet)' }}</p>
          <button type="submit" class="btn btn--sm btn--outline">Save Essay Rank</button>
        </form>
      </div>
    </div>

    <div class="panel">
      <div class="panel-head"><h3>Attached Documents ({{ $documents->count() }})</h3></div>
      <div class="panel-body">
        @if ($documents->isEmpty())
          <p style="color:var(--ink-soft); margin:0;">No documents attached.</p>
        @else
          <ul class="doc-list" style="margin:0;">
            @foreach ($documents as $d)
              <li>
                <span class="doc-name">&#128196; <a href="{{ route('admin.students.documents.view', [$student->id, $d->id]) }}" target="_blank" rel="noopener">{{ $d->label ?: $d->original_filename }}</a></span>
                <span style="color:var(--ink-soft); font-size:0.82rem;">{{ format_date($d->uploaded_at) }}</span>
              </li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  </div>

  <div>
    <div class="panel">
      <div class="panel-head"><h3>Application Status</h3></div>
      <div class="panel-body">
        <form method="post" action="{{ route('admin.students.update_status', $student->id) }}">
          @csrf
          @method('PUT')
          <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status">
              @foreach ($statuses as $s)
                <option value="{{ $s }}" {{ $student->status === $s ? 'selected' : '' }}>{{ $s }}</option>
              @endforeach
            </select>
          </div>
          <button type="submit" class="btn btn--block btn--sm">Update Status</button>
        </form>
      </div>
    </div>

    <div class="panel">
      <div class="panel-head"><h3>Account Access</h3></div>
      <div class="panel-body">
        <p style="color:var(--ink-soft); font-size:0.88rem;">
          Control whether this candidate can log in to the student portal at all. Disabling takes effect immediately, even mid-session.
        </p>
        <form method="post" action="{{ route('admin.students.update_account_access', $student->id) }}">
          @csrf
          @method('PUT')
          @if ($student->is_active)
            <input type="hidden" name="is_active" value="0">
            <button type="submit" class="btn btn--block btn--sm btn--outline" onclick="return confirm('Disable this student\'s account? They will be logged out immediately and unable to sign in again until re-enabled.');">Disable Account</button>
          @else
            <input type="hidden" name="is_active" value="1">
            <button type="submit" class="btn btn--block btn--sm">Enable Account</button>
          @endif
        </form>
      </div>
    </div>

    <div class="panel">
      <div class="panel-head"><h3>Evaluation</h3></div>
      <div class="panel-body">
        <form method="post" action="{{ route('admin.students.update_evaluation', $student->id) }}">
          @csrf
          @method('PUT')
          <div class="form-group">
            <label for="evaluation_score">Score (0&ndash;5)</label>
            <input type="text" id="evaluation_score" name="evaluation_score" value="{{ $student->evaluation_score !== null ? (string) $student->evaluation_score : '' }}" placeholder="e.g. 4.5">
          </div>
          <div class="form-group">
            <label for="evaluation_notes">Evaluation Notes</label>
            <textarea id="evaluation_notes" name="evaluation_notes" placeholder="Internal notes on this candidate">{{ $student->evaluation_notes }}</textarea>
          </div>
          @if ($student->evaluated_at)
            <p class="hint">Last evaluated {{ format_date($student->evaluated_at, 'j M Y, g:i a') }}</p>
          @endif
          <button type="submit" class="btn btn--block btn--sm">Save Evaluation</button>
        </form>
      </div>
    </div>

    <div class="panel">
      <div class="panel-head"><h3>Mrs. Poh's Remarks</h3></div>
      <div class="panel-body">
        <form method="post" action="{{ route('admin.students.update_mrs_poh', $student->id) }}">
          @csrf
          @method('PUT')
          <div class="form-group">
            <label for="mrs_poh_remarks">Remarks</label>
            <textarea id="mrs_poh_remarks" name="mrs_poh_remarks" placeholder="Mrs. Poh's personal notes on this candidate">{{ $student->mrs_poh_remarks }}</textarea>
          </div>
          @if ($student->mrs_poh_updated_at)
            <p class="hint">Last updated {{ format_date($student->mrs_poh_updated_at, 'j M Y, g:i a') }}</p>
          @endif
          <button type="submit" class="btn btn--block btn--sm">Save Remarks</button>
        </form>
      </div>
    </div>
  </div>
</div>

@php
$academicAwards = $student->academic_awards ?: [];
$financialAwards = $student->financial_awards ?: [];
$familyMembers = $student->family_members ?: [];
$employmentRecords = $student->employment_records ?: [];
$allCocurricular = $student->cocurricular_activities ?: [];
$workExperience = $student->work_experience ?: [];
$referees = $student->referees ?: [];
@endphp

<div class="panel">
  <div class="panel-head"><h3>Full Application (Official Form, Sections A&ndash;L)</h3></div>
  <div class="panel-body">
  <form method="post" action="{{ route('admin.students.update_section_ranks', $student->id) }}">
    @csrf
    @method('PUT')
    <p class="hint" style="margin:0 0 6px;">Each section below has a rank field for Admin and for Mrs. Poh - enter a rank and use Save Section Ranks at the bottom.</p>

    @include('admin.students._section_heading', ['key' => 'A', 'label' => "Section A - Additional Personal Particulars"])
    <table class="data-table">
      <tr><th style="width:25%;">Citizenship</th><td>{{ $student->citizenship ?: '—' }}</td><th style="width:25%;">Ethnic Group</th><td>{{ $student->ethnic_group ?: '—' }}</td></tr>
      <tr><th>Religion</th><td>{{ $student->religion ?: '—' }}</td><th>Undergraduate Course</th><td>{{ $student->undergraduate_course ?: '—' }}</td></tr>
    </table>

    @include('admin.students._section_heading', ['key' => 'B', 'label' => "Section B - Schools Attended"])
    <table class="data-table">
      <tr><th style="width:25%;">Secondary School (O/L)</th><td>{{ $student->secondary_school ?: '—' }}</td><th style="width:25%;">College (A/L)</th><td>{{ $student->al_college ?: '—' }}</td></tr>
    </table>
    <div class="table-wrap" style="margin-top:8px;">
      <table class="data-table">
        <thead><tr><th></th><th>A</th><th>B</th><th>C</th><th>S</th><th>W/F</th><th>Ranking / Z-Score</th></tr></thead>
        <tbody>
          <tr>
            <th>GCE 'O' Level</th>
            <td>{{ $student->ol_grade_a ?: '—' }}</td><td>{{ $student->ol_grade_b ?: '—' }}</td><td>{{ $student->ol_grade_c ?: '—' }}</td>
            <td>{{ $student->ol_grade_s ?: '—' }}</td><td>{{ $student->ol_grade_wf ?: '—' }}</td><td>{{ $student->ol_national_ranking ?: '—' }}</td>
          </tr>
          <tr>
            <th>GCE 'A' Level</th>
            <td>{{ $student->al_grade_a ?: '—' }}</td><td>{{ $student->al_grade_b ?: '—' }}</td><td>{{ $student->al_grade_c ?: '—' }}</td>
            <td>{{ $student->al_grade_s ?: '—' }}</td><td>{{ $student->al_grade_wf ?: '—' }}</td><td>{{ $student->al_zscore ?: '—' }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <p style="color:var(--ink-soft); font-size:0.88rem;">A' Level National Ranking by Stream: {{ $student->al_national_ranking ?: '—' }}</p>

    @include('admin.students._section_heading', ['key' => 'C', 'label' => "Section C - Academic Awards"])
    @if (empty($academicAwards))
      <p style="color:var(--ink-soft);">None listed.</p>
    @else
      <table class="data-table"><thead><tr><th style="width:20%;">Year</th><th>Award</th></tr></thead><tbody>
        @foreach ($academicAwards as $row)
          <tr><td>{{ $row['year_of_award'] ?? '' }}</td><td>{{ $row['name_of_award'] ?? '' }}</td></tr>
        @endforeach
      </tbody></table>
    @endif

    @include('admin.students._section_heading', ['key' => 'D', 'label' => "Section D - Family Particulars"])
    @if (empty($familyMembers))
      <p style="color:var(--ink-soft);">None listed.</p>
    @else
      <div class="table-wrap">
        <table class="data-table">
          <thead><tr><th>Name</th><th>Relationship</th><th>DOB</th><th>Citizenship</th><th>Marital Status</th><th>Staying With Applicant?</th><th>Occupation</th><th>Employer / School</th><th>Monthly Income</th></tr></thead>
          <tbody>
            @foreach ($familyMembers as $row)
              <tr>
                <td>{{ $row['name'] ?? '' }}</td><td>{{ $row['relationship'] ?? '' }}</td><td>{{ $row['date_of_birth'] ?? '' }}</td>
                <td>{{ $row['citizenship'] ?? '' }}</td><td>{{ $row['marital_status'] ?? '' }}</td><td>{{ $row['staying_with_applicant'] ?? '' }}</td>
                <td>{{ $row['occupation'] ?? '' }}</td><td>{{ $row['employer_or_school'] ?? '' }}</td><td>{{ $row['monthly_income'] ?? '' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

    @include('admin.students._section_heading', ['key' => 'E', 'label' => "Section E - Past Employment Record"])
    @if (empty($employmentRecords))
      <p style="color:var(--ink-soft);">None listed.</p>
    @else
      <div class="table-wrap">
        <table class="data-table">
          <thead><tr><th>Company</th><th>Designation</th><th>Income (LKR)</th><th>From</th><th>To</th><th>Years/Months</th><th>Job Description</th><th>Staff Reporting</th></tr></thead>
          <tbody>
            @foreach ($employmentRecords as $row)
              <tr>
                <td>{{ $row['company_name'] ?? '' }}</td><td>{{ $row['designation'] ?? '' }}</td><td>{{ $row['gross_monthly_income'] ?? '' }}</td>
                <td>{{ $row['period_from'] ?? '' }}</td><td>{{ $row['period_to'] ?? '' }}</td><td>{{ $row['years_months'] ?? '' }}</td>
                <td>{{ $row['job_description'] ?? '' }}</td><td>{{ $row['staff_reporting'] ?? '' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

    @include('admin.students._section_heading', ['key' => 'F', 'label' => "Section F - Financial Awards / Scholarships / Loans Held"])
    @if (empty($financialAwards))
      <p style="color:var(--ink-soft);">None listed.</p>
    @else
      <table class="data-table"><thead><tr><th>Award</th><th style="width:25%;">Tenure</th><th style="width:20%;">Value (LKR)</th></tr></thead><tbody>
        @foreach ($financialAwards as $row)
          <tr><td>{{ $row['name_of_award'] ?? '' }}</td><td>{{ $row['tenure_of_award'] ?? '' }}</td><td>{{ $row['value_of_award'] ?? '' }}</td></tr>
        @endforeach
      </tbody></table>
    @endif

    @include('admin.students._section_heading', ['key' => 'G', 'label' => "Section G - Co-Curricular Activities"])
    @if (empty($allCocurricular))
      <p style="color:var(--ink-soft);">None listed.</p>
    @else
      <div class="table-wrap">
        <table class="data-table">
          <thead><tr><th style="width:16%;">Category</th><th style="width:16%;">Period</th><th>Detail</th><th>Level / Role</th></tr></thead>
          <tbody>
            @foreach ($allCocurricular as $row)
              <tr>
                <td>{{ $catLabel[$row['category'] ?? ''] ?? ($row['category'] ?? '') }}</td>
                <td>{{ $row['period'] ?? '' }}</td><td>{{ $row['detail'] ?? '' }}</td><td>{{ $row['level_role'] ?? '' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

    @include('admin.students._section_heading', ['key' => 'H', 'label' => "Section H - Work Experience"])
    @if (empty($workExperience))
      <p style="color:var(--ink-soft);">None listed.</p>
    @else
      <table class="data-table"><thead><tr><th style="width:16%;">Period</th><th>Description</th><th>Organisation / Position</th></tr></thead><tbody>
        @foreach ($workExperience as $row)
          <tr><td>{{ $row['period'] ?? '' }}</td><td>{{ $row['description'] ?? '' }}</td><td>{{ $row['org_position'] ?? '' }}</td></tr>
        @endforeach
      </tbody></table>
    @endif

    @include('admin.students._section_heading', ['key' => 'J', 'label' => "Section J - Other Information"])
    <table class="data-table">
      <tr><th style="width:55%;">Previously applied for a scholarship/sponsorship elsewhere?</th><td>{{ $yn($student->prev_scholarship_flag) }}</td></tr>
      @if ($student->prev_scholarship_details)<tr><td colspan="2" style="color:var(--ink-soft); white-space:pre-wrap;">{{ $student->prev_scholarship_details }}</td></tr>@endif
      <tr><th>Physical impairment or disease?</th><td>{{ $yn($student->health_condition_flag) }}</td></tr>
      @if ($student->health_condition_details)<tr><td colspan="2" style="color:var(--ink-soft); white-space:pre-wrap;">{{ $student->health_condition_details }}</td></tr>@endif
      <tr><th>Ever charged in a court of law?</th><td>{{ $yn($student->legal_charge_flag) }}</td></tr>
      @if ($student->legal_charge_details)<tr><td colspan="2" style="color:var(--ink-soft); white-space:pre-wrap;">{{ $student->legal_charge_details }}</td></tr>@endif
    </table>

    @include('admin.students._section_heading', ['key' => 'K', 'label' => "Section K - Character Referees"])
    @if (empty($referees))
      <p style="color:var(--ink-soft);">None listed.</p>
    @else
      <table class="data-table"><thead><tr><th>Name</th><th>Address</th><th>Occupation</th><th style="width:13%;">Telephone</th><th style="width:11%;">Years Known</th></tr></thead><tbody>
        @foreach ($referees as $row)
          <tr>
            <td>{{ $row['name'] ?? '' }}</td><td>{{ $row['address'] ?? '' }}</td><td>{{ $row['occupation'] ?? '' }}</td>
            <td>{{ $row['telephone'] ?? '' }}</td><td>{{ $row['years_known'] ?? '' }}</td>
          </tr>
        @endforeach
      </tbody></table>
    @endif

    @include('admin.students._section_heading', ['key' => 'L', 'label' => "Section L - Declaration"])
    <table class="data-table">
      <tr><th style="width:25%;">Agreed to Declaration</th><td>{{ $student->declaration_agreed ? 'Yes' : 'No' }}</td></tr>
      <tr><th>Signature</th><td>{{ $student->declaration_signature ?: '—' }}</td></tr>
      <tr><th>Date</th><td>{{ $student->declaration_date ? format_date($student->declaration_date) : '—' }}</td></tr>
    </table>

    <button type="submit" class="btn btn--sm" style="margin-top:16px;">Save Section Ranks</button>
  </form>
  </div>
</div>

@if ($student->accepted_at)
<div class="panel">
  <div class="panel-head"><h3>Final Information</h3></div>
  <div class="panel-body">
    @if (!$student->final_info_updated_at)
      <p style="color:var(--ink-soft); margin:0;">The candidate has not yet submitted their Final Information (bank details &amp; emergency contact).</p>
    @else
      <div class="grid grid--2">
        <table class="data-table">
          <tr><th style="width:45%;">Bank Account Name</th><td>{{ $student->bank_account_name ?: '—' }}</td></tr>
          <tr><th>Bank</th><td>{{ $student->bank_name ?: '—' }}</td></tr>
          <tr><th>Branch</th><td>{{ $student->bank_branch ?: '—' }}</td></tr>
          <tr><th>Account Number</th><td>{{ $student->bank_account_number ?: '—' }}</td></tr>
        </table>
        <table class="data-table">
          <tr><th style="width:45%;">Emergency Contact</th><td>{{ $student->emergency_contact_name ?: '—' }}</td></tr>
          <tr><th>Phone</th><td>{{ $student->emergency_contact_phone ?: '—' }}</td></tr>
          <tr><th>Relationship</th><td>{{ $student->emergency_contact_relationship ?: '—' }}</td></tr>
        </table>
      </div>
      <p class="hint" style="margin-top:10px;">Last updated {{ format_date($student->final_info_updated_at, 'j M Y, g:i a') }}. The candidate can update this at any time from their portal.</p>
    @endif
  </div>
</div>

<div class="panel">
  <div class="panel-head"><h3>Academic Results (every 6 months)</h3></div>
  <div class="panel-body no-pad">
    <div class="table-wrap">
      <table class="data-table">
        <thead>
          <tr><th>Period</th><th>Due Date</th><th>Status</th><th>Submitted</th><th>Reminders Sent</th><th></th></tr>
        </thead>
        <tbody>
          @foreach ($periods as $p)
            @php($overdue = !$p->submitted_at && $p->due_date->toDateString() < $today)
            <tr>
              <td>Period {{ $p->period_number }}</td>
              <td>{{ format_date($p->due_date) }}</td>
              <td>
                @if ($p->submitted_at)
                  <span class="badge badge--approved">Submitted</span>
                @elseif ($overdue)
                  <span class="badge badge--rejected">Overdue</span>
                @else
                  <span class="badge badge--pending">Pending</span>
                @endif
              </td>
              <td>
                @if ($p->submitted_at)
                  {{ format_date($p->submitted_at, 'j M Y') }}
                  @if ($p->result_file)
                    &nbsp;<a href="{{ asset('storage/'.$p->result_file) }}" target="_blank" rel="noopener">&#128196; file</a>
                  @endif
                @else
                  —
                @endif
              </td>
              <td>{{ $p->reminder_count }}{{ $p->reminder_sent_at ? ' (last '.format_date($p->reminder_sent_at, 'j M Y').')' : '' }}</td>
              <td>
                @unless ($p->submitted_at)
                  <form method="post" action="{{ route('admin.students.send_reminder', $student->id) }}" style="margin:0;">
                    @csrf
                    <input type="hidden" name="period_id" value="{{ $p->id }}">
                    <button type="submit" class="btn btn--sm btn--outline">Send Reminder</button>
                  </form>
                @endunless
              </td>
            </tr>
            @if ($p->result_summary)
              <tr><td colspan="6" style="color:var(--ink-soft); white-space:pre-wrap; border-top:none; padding-top:0;">{{ $p->result_summary }}</td></tr>
            @endif
          @endforeach
          @if ($periods->isEmpty())
            <tr><td colspan="6">No academic result periods yet - these open automatically once a candidate is accepted.</td></tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>
@endif
@endsection