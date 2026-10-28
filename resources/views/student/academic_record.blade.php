@extends('layouts.student')

@section('content')
<span class="eyebrow">Section B, C &amp; F</span>
<h1>Academic Record</h1>
<p style="color:var(--ink-soft); margin-bottom:26px;">Schools attended, examination results, academic awards, and any financial awards or scholarships you have held.</p>

@if ($locked)
  <div class="locked-note">Your application has been submitted, so this section is now locked for editing.</div>
@endif

<div class="form-card">
  <form method="post" action="{{ route('student.academic_record.update') }}" novalidate>
    @csrf
    @method('PUT')
    <fieldset {{ $locked ? 'disabled' : '' }}>

    <fieldset>
      <legend>Schools Attended</legend>
      <div class="form-row">
        <div class="form-group">
          <label for="secondary_school">Secondary School for GCE 'O' Level</label>
          <input type="text" id="secondary_school" name="secondary_school" value="{{ old('secondary_school', $student->secondary_school) }}" {{ $locked ? 'readonly' : '' }}>
        </div>
        <div class="form-group">
          <label for="al_college">College for GCE 'A' Level</label>
          <input type="text" id="al_college" name="al_college" value="{{ old('al_college', $student->al_college) }}" {{ $locked ? 'readonly' : '' }}>
        </div>
      </div>

      <p style="margin:16px 0 6px; font-weight:600; color:var(--maroon);">Total Number of Grades Obtained</p>
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr><th></th><th>A</th><th>B</th><th>C</th><th>S</th><th>W/F</th><th>National Ranking / Z-Score</th></tr>
          </thead>
          <tbody>
            <tr>
              <th>GCE 'O' Level</th>
              <td><input type="number" min="0" name="ol_grade_a" value="{{ old('ol_grade_a', $student->ol_grade_a) }}" style="width:60px;" {{ $locked ? 'readonly' : '' }}></td>
              <td><input type="number" min="0" name="ol_grade_b" value="{{ old('ol_grade_b', $student->ol_grade_b) }}" style="width:60px;" {{ $locked ? 'readonly' : '' }}></td>
              <td><input type="number" min="0" name="ol_grade_c" value="{{ old('ol_grade_c', $student->ol_grade_c) }}" style="width:60px;" {{ $locked ? 'readonly' : '' }}></td>
              <td><input type="number" min="0" name="ol_grade_s" value="{{ old('ol_grade_s', $student->ol_grade_s) }}" style="width:60px;" {{ $locked ? 'readonly' : '' }}></td>
              <td><input type="number" min="0" name="ol_grade_wf" value="{{ old('ol_grade_wf', $student->ol_grade_wf) }}" style="width:60px;" {{ $locked ? 'readonly' : '' }}></td>
              <td><input type="text" name="ol_national_ranking" value="{{ old('ol_national_ranking', $student->ol_national_ranking) }}" placeholder="O' Level National Ranking" {{ $locked ? 'readonly' : '' }}></td>
            </tr>
            <tr>
              <th>GCE 'A' Level</th>
              <td><input type="number" min="0" name="al_grade_a" value="{{ old('al_grade_a', $student->al_grade_a) }}" style="width:60px;" {{ $locked ? 'readonly' : '' }}></td>
              <td><input type="number" min="0" name="al_grade_b" value="{{ old('al_grade_b', $student->al_grade_b) }}" style="width:60px;" {{ $locked ? 'readonly' : '' }}></td>
              <td><input type="number" min="0" name="al_grade_c" value="{{ old('al_grade_c', $student->al_grade_c) }}" style="width:60px;" {{ $locked ? 'readonly' : '' }}></td>
              <td><input type="number" min="0" name="al_grade_s" value="{{ old('al_grade_s', $student->al_grade_s) }}" style="width:60px;" {{ $locked ? 'readonly' : '' }}></td>
              <td><input type="number" min="0" name="al_grade_wf" value="{{ old('al_grade_wf', $student->al_grade_wf) }}" style="width:60px;" {{ $locked ? 'readonly' : '' }}></td>
              <td><input type="text" name="al_zscore" value="{{ old('al_zscore', $student->al_zscore) }}" placeholder="A' Level Z-Score" {{ $locked ? 'readonly' : '' }}></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="form-group" style="margin-top:12px;">
        <label for="al_national_ranking">A' Level National Ranking by Stream</label>
        <input type="text" id="al_national_ranking" name="al_national_ranking" value="{{ old('al_national_ranking', $student->al_national_ranking) }}" {{ $locked ? 'readonly' : '' }}>
      </div>
    </fieldset>

    <fieldset>
      <legend>Prizes, Academic Awards &amp; Special Achievements in Secondary School / College</legend>
      <div class="table-wrap">
        <table class="data-table" id="academicAwardsTable">
          <thead><tr><th style="width:30%;">Year of Award</th><th>Name of Award</th>@unless ($locked)<th style="width:60px;"></th>@endunless</tr></thead>
          <tbody>
            @foreach ($academicAwards as $row)
              <tr>
                <td><input type="text" name="award_year[]" value="{{ $row['year_of_award'] ?? '' }}" placeholder="e.g. 2023" {{ $locked ? 'readonly' : '' }}></td>
                <td><input type="text" name="award_name[]" value="{{ $row['name_of_award'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
                @unless ($locked)<td><button type="button" class="btn btn--sm btn--outline row-remove">&times;</button></td>@endunless
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @unless ($locked)<button type="button" class="btn btn--sm btn--outline" id="addAcademicAward" style="margin-top:8px;">+ Add Row</button>@endunless
    </fieldset>

    <fieldset>
      <legend>Financial Awards / Scholarships / Loans Held Previously or Currently</legend>
      <div class="table-wrap">
        <table class="data-table" id="financialAwardsTable">
          <thead><tr><th>Name of Award</th><th style="width:25%;">Tenure of Award</th><th style="width:20%;">Value of Award (LKR)</th>@unless ($locked)<th style="width:60px;"></th>@endunless</tr></thead>
          <tbody>
            @foreach ($financialAwards as $row)
              <tr>
                <td><input type="text" name="fin_award_name[]" value="{{ $row['name_of_award'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
                <td><input type="text" name="fin_award_tenure[]" value="{{ $row['tenure_of_award'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
                <td><input type="text" name="fin_award_value[]" value="{{ $row['value_of_award'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
                @unless ($locked)<td><button type="button" class="btn btn--sm btn--outline row-remove">&times;</button></td>@endunless
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @unless ($locked)<button type="button" class="btn btn--sm btn--outline" id="addFinancialAward" style="margin-top:8px;">+ Add Row</button>@endunless
    </fieldset>

    </fieldset>

    @unless ($locked)
      <button type="submit" class="btn btn--block">Save Academic Record</button>
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
  var addAcademic = document.getElementById('addAcademicAward');
  if (addAcademic) {
    addAcademic.addEventListener('click', function () {
      addRow('academicAwardsTable',
        '<td><input type="text" name="award_year[]" placeholder="e.g. 2023"></td>' +
        '<td><input type="text" name="award_name[]"></td>' +
        '<td><button type="button" class="btn btn--sm btn--outline row-remove">&times;</button></td>');
    });
  }
  var addFinancial = document.getElementById('addFinancialAward');
  if (addFinancial) {
    addFinancial.addEventListener('click', function () {
      addRow('financialAwardsTable',
        '<td><input type="text" name="fin_award_name[]"></td>' +
        '<td><input type="text" name="fin_award_tenure[]"></td>' +
        '<td><input type="text" name="fin_award_value[]"></td>' +
        '<td><button type="button" class="btn btn--sm btn--outline row-remove">&times;</button></td>');
    });
  }
</script>
@endunless
@endsection
