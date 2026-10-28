@extends('layouts.student')

@section('content')
<span class="eyebrow">Section G &amp; H</span>
<h1>Activities &amp; Experience</h1>
<p style="color:var(--ink-soft); margin-bottom:26px;">Co-curricular activities, sports, leadership / community service, and any work experience (excluding part-time work).</p>

@if ($locked)
  <div class="locked-note">Your application has been submitted, so this section is now locked for editing.</div>
@endif

<div class="form-card">
  <form method="post" action="{{ route('student.activities.update') }}" novalidate>
    @csrf
    @method('PUT')
    <fieldset {{ $locked ? 'disabled' : '' }}>

    <fieldset>
      <legend>Co-Curricular Activities</legend>

      <p style="font-weight:600; color:var(--maroon); margin:0 0 6px;">Activities Involved in Secondary School / College</p>
      <table class="data-table" id="schoolActivitiesTable">
        <thead><tr><th style="width:20%;">Period</th><th>Type of Activity</th><th>Level of Representation &amp; Position Held</th>@unless ($locked)<th style="width:50px;"></th>@endunless</tr></thead>
        <tbody>
          @foreach ($schoolActivities as $row)
            <tr>
              <td><input type="text" name="sa_period[]" value="{{ $row['period'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
              <td><input type="text" name="sa_detail[]" value="{{ $row['detail'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
              <td><input type="text" name="sa_level[]" value="{{ $row['level_role'] ?? '' }}" placeholder="e.g. National, President" {{ $locked ? 'readonly' : '' }}></td>
              @unless ($locked)<td><button type="button" class="btn btn--sm btn--outline row-remove">&times;</button></td>@endunless
            </tr>
          @endforeach
        </tbody>
      </table>
      @unless ($locked)<button type="button" class="btn btn--sm btn--outline" id="addSchoolActivity" style="margin:8px 0 18px;">+ Add Row</button>@else<div style="margin-bottom:18px;"></div>@endunless

      <p style="font-weight:600; color:var(--maroon); margin:0 0 6px;">Sports at School / College / Province / National Level</p>
      <table class="data-table" id="sportsTable">
        <thead><tr><th style="width:20%;">Period</th><th>Type of Sport</th><th>Level of Representation &amp; Position Held</th>@unless ($locked)<th style="width:50px;"></th>@endunless</tr></thead>
        <tbody>
          @foreach ($sports as $row)
            <tr>
              <td><input type="text" name="sp_period[]" value="{{ $row['period'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
              <td><input type="text" name="sp_detail[]" value="{{ $row['detail'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
              <td><input type="text" name="sp_level[]" value="{{ $row['level_role'] ?? '' }}" placeholder="e.g. National, President" {{ $locked ? 'readonly' : '' }}></td>
              @unless ($locked)<td><button type="button" class="btn btn--sm btn--outline row-remove">&times;</button></td>@endunless
            </tr>
          @endforeach
        </tbody>
      </table>
      @unless ($locked)<button type="button" class="btn btn--sm btn--outline" id="addSport" style="margin:8px 0 18px;">+ Add Row</button>@else<div style="margin-bottom:18px;"></div>@endunless

      <p style="font-weight:600; color:var(--maroon); margin:0 0 6px;">Other Activities at Leadership Level &amp; Community / Voluntary Services</p>
      <table class="data-table" id="leadershipTable">
        <thead><tr><th style="width:20%;">Period</th><th>Brief Description of Activities</th><th>Role Played / Position Held</th>@unless ($locked)<th style="width:50px;"></th>@endunless</tr></thead>
        <tbody>
          @foreach ($leadership as $row)
            <tr>
              <td><input type="text" name="ld_period[]" value="{{ $row['period'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
              <td><input type="text" name="ld_detail[]" value="{{ $row['detail'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
              <td><input type="text" name="ld_level[]" value="{{ $row['level_role'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
              @unless ($locked)<td><button type="button" class="btn btn--sm btn--outline row-remove">&times;</button></td>@endunless
            </tr>
          @endforeach
        </tbody>
      </table>
      @unless ($locked)<button type="button" class="btn btn--sm btn--outline" id="addLeadership" style="margin-top:8px;">+ Add Row</button>@endunless
    </fieldset>

    <fieldset>
      <legend>Work Experience (Excluding Part-Time Work)</legend>
      <table class="data-table" id="workExperienceTable">
        <thead><tr><th style="width:20%;">Period</th><th>Brief Description of Work Responsibility</th><th>Organisation &amp; Position Held</th>@unless ($locked)<th style="width:50px;"></th>@endunless</tr></thead>
        <tbody>
          @foreach ($workExperience as $row)
            <tr>
              <td><input type="text" name="we_period[]" value="{{ $row['period'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
              <td><input type="text" name="we_description[]" value="{{ $row['description'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
              <td><input type="text" name="we_org_position[]" value="{{ $row['org_position'] ?? '' }}" {{ $locked ? 'readonly' : '' }}></td>
              @unless ($locked)<td><button type="button" class="btn btn--sm btn--outline row-remove">&times;</button></td>@endunless
            </tr>
          @endforeach
        </tbody>
      </table>
      @unless ($locked)<button type="button" class="btn btn--sm btn--outline" id="addWorkExperience" style="margin-top:8px;">+ Add Row</button>@endunless
    </fieldset>

    </fieldset>

    @unless ($locked)
      <button type="submit" class="btn btn--block">Save Activities &amp; Experience</button>
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
  function threeColRow(prefix) {
    return '<td><input type="text" name="' + prefix + '_period[]"></td>' +
      '<td><input type="text" name="' + prefix + '_detail[]"></td>' +
      '<td><input type="text" name="' + prefix + '_level[]"></td>' +
      '<td><button type="button" class="btn btn--sm btn--outline row-remove">&times;</button></td>';
  }
  var addSA = document.getElementById('addSchoolActivity');
  if (addSA) addSA.addEventListener('click', function () { addRow('schoolActivitiesTable', threeColRow('sa')); });
  var addSP = document.getElementById('addSport');
  if (addSP) addSP.addEventListener('click', function () { addRow('sportsTable', threeColRow('sp')); });
  var addLD = document.getElementById('addLeadership');
  if (addLD) addLD.addEventListener('click', function () { addRow('leadershipTable', threeColRow('ld')); });
  var addWE = document.getElementById('addWorkExperience');
  if (addWE) {
    addWE.addEventListener('click', function () {
      addRow('workExperienceTable',
        '<td><input type="text" name="we_period[]"></td>' +
        '<td><input type="text" name="we_description[]"></td>' +
        '<td><input type="text" name="we_org_position[]"></td>' +
        '<td><button type="button" class="btn btn--sm btn--outline row-remove">&times;</button></td>');
    });
  }
</script>
@endunless
@endsection
