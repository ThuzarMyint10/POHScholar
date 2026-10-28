@extends('layouts.student')

@section('content')
<span class="eyebrow">Section D &amp; E</span>
<h1>Family &amp; Background</h1>
<p style="color:var(--ink-soft); margin-bottom:26px;">Particulars of your immediate family (parents and siblings), and your own past employment record, if applicable.</p>

@if ($locked)
  <div class="locked-note">Your application has been submitted, so this section is now locked for editing.</div>
@endif

<div class="form-card">
  <form method="post" action="{{ route('student.family_background.update') }}" novalidate>
    @csrf
    @method('PUT')
    <fieldset {{ $locked ? 'disabled' : '' }}>

    <fieldset>
      <legend>Family Particulars</legend>
      <p class="hint" style="margin-top:0;">Enter particulars of your immediate family members (parents and siblings).</p>
      <div class="table-wrap">
        <table class="data-table" id="familyTable">
          <thead>
            <tr>
              <th style="width:220px;">Name</th><th>Relationship</th><th>Date of Birth</th><th>Citizenship</th>
              <th>Marital Status</th><th>Staying with Applicant?</th><th>Occupation</th><th>Employer / School Attending</th>
              <th style="width:12%;">Gross Monthly Income (LKR)</th>@unless ($locked)<th></th>@endunless
            </tr>
          </thead>
          <tbody>
            @foreach ($familyMembers as $row)
              <tr>
                <td><input type="text" name="fam_name[]" value="{{ $row['name'] ?? '' }}" style="min-width:200px;" {{ $locked ? 'readonly' : '' }}></td>
                <td><input type="text" name="fam_relationship[]" value="{{ $row['relationship'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
                <td><input type="date" name="fam_dob[]" value="{{ $row['date_of_birth'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
                <td><input type="text" name="fam_citizenship[]" value="{{ $row['citizenship'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
                <td><input type="text" name="fam_marital_status[]" value="{{ $row['marital_status'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
                <td>
                  <select name="fam_staying_with[]" {{ $locked ? 'disabled' : '' }}>
                    <option value="">&ndash;</option>
                    <option value="Yes" {{ ($row['staying_with_applicant'] ?? '') === 'Yes' ? 'selected' : '' }}>Yes</option>
                    <option value="No" {{ ($row['staying_with_applicant'] ?? '') === 'No' ? 'selected' : '' }}>No</option>
                  </select>
                </td>
                <td><input type="text" name="fam_occupation[]" value="{{ $row['occupation'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
                <td><input type="text" name="fam_employer_or_school[]" value="{{ $row['employer_or_school'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
                <td><input type="text" name="fam_income[]" value="{{ $row['monthly_income'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
                @unless ($locked)<td><button type="button" class="btn btn--sm btn--outline row-remove">&times;</button></td>@endunless
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @unless ($locked)<button type="button" class="btn btn--sm btn--outline" id="addFamilyRow" style="margin-top:8px;">+ Add Family Member</button>@endunless
    </fieldset>

    <fieldset>
      <legend>Past Employment Record (If Applicable)</legend>
      <p class="hint" style="margin-top:0;">Your own past employment, in chronological order, starting with your most recent position.</p>
      <div class="table-wrap">
        <table class="data-table" id="employmentTable">
          <thead>
            <tr>
              <th>Name of Company</th><th>Designation</th><th style="width:10%;">Gross Monthly Income (LKR)</th>
              <th style="width:9%;">From (mth/yr)</th><th style="width:9%;">To (mth/yr)</th><th style="width:10%;">Years / Months</th>
              <th>Job Description</th><th style="width:10%;">No. of Staff Reporting to You</th>@unless ($locked)<th></th>@endunless
            </tr>
          </thead>
          <tbody>
            @foreach ($employmentRecords as $row)
              <tr>
                <td><input type="text" name="emp_company[]" value="{{ $row['company_name'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
                <td><input type="text" name="emp_designation[]" value="{{ $row['designation'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
                <td><input type="text" name="emp_income[]" value="{{ $row['gross_monthly_income'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
                <td><input type="text" name="emp_from[]" value="{{ $row['period_from'] ?? '' }}" placeholder="mm/yyyy" {{ $locked ? 'readonly' : '' }}></td>
                <td><input type="text" name="emp_to[]" value="{{ $row['period_to'] ?? '' }}" placeholder="mm/yyyy" {{ $locked ? 'readonly' : '' }}></td>
                <td><input type="text" name="emp_years_months[]" value="{{ $row['years_months'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
                <td><input type="text" name="emp_job_description[]" value="{{ $row['job_description'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
                <td><input type="text" name="emp_staff_reporting[]" value="{{ $row['staff_reporting'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
                @unless ($locked)<td><button type="button" class="btn btn--sm btn--outline row-remove">&times;</button></td>@endunless
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @unless ($locked)<button type="button" class="btn btn--sm btn--outline" id="addEmploymentRow" style="margin-top:8px;">+ Add Row</button>@endunless
    </fieldset>

    </fieldset>

    @unless ($locked)
      <button type="submit" class="btn btn--block">Save Family &amp; Background</button>
    @endunless
  </form>
</div>

@unless ($locked)
<script>
  function addRow(tableId, rowHtml) {
    var tbody = document.querySelector('#' + tableId + ' tbody');
    var tr = document.createElement('tr');
    tr.innerHTML = rowHtml;
    tbody.appendChild(tr);
  }
  document.addEventListener('click', function (e) {
    if (e.target.classList.contains('row-remove')) {
      var tr = e.target.closest('tr');
      if (tr) tr.parentNode.removeChild(tr);
    }
  });
  var addFamily = document.getElementById('addFamilyRow');
  if (addFamily) {
    addFamily.addEventListener('click', function () {
      addRow('familyTable',
        '<td><input type="text" name="fam_name[]" style="min-width:200px;"></td>' +
        '<td><input type="text" name="fam_relationship[]"></td>' +
        '<td><input type="date" name="fam_dob[]"></td>' +
        '<td><input type="text" name="fam_citizenship[]"></td>' +
        '<td><input type="text" name="fam_marital_status[]"></td>' +
        '<td><select name="fam_staying_with[]"><option value="">&ndash;</option><option value="Yes">Yes</option><option value="No">No</option></select></td>' +
        '<td><input type="text" name="fam_occupation[]"></td>' +
        '<td><input type="text" name="fam_employer_or_school[]"></td>' +
        '<td><input type="text" name="fam_income[]"></td>' +
        '<td><button type="button" class="btn btn--sm btn--outline row-remove">&times;</button></td>');
    });
  }
  var addEmployment = document.getElementById('addEmploymentRow');
  if (addEmployment) {
    addEmployment.addEventListener('click', function () {
      addRow('employmentTable',
        '<td><input type="text" name="emp_company[]"></td>' +
        '<td><input type="text" name="emp_designation[]"></td>' +
        '<td><input type="text" name="emp_income[]"></td>' +
        '<td><input type="text" name="emp_from[]" placeholder="mm/yyyy"></td>' +
        '<td><input type="text" name="emp_to[]" placeholder="mm/yyyy"></td>' +
        '<td><input type="text" name="emp_years_months[]"></td>' +
        '<td><input type="text" name="emp_job_description[]"></td>' +
        '<td><input type="text" name="emp_staff_reporting[]"></td>' +
        '<td><button type="button" class="btn btn--sm btn--outline row-remove">&times;</button></td>');
    });
  }
</script>
@endunless
@endsection
