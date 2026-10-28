@php
    $academicAwards = $student->academic_awards ?: [];
    $financialAwards = $student->financial_awards ?: [];
    $familyMembers = $student->family_members ?: [];
    $employmentRecords = $student->employment_records ?: [];
    $allCocurricular = $student->cocurricular_activities ?: [];
    $schoolActivities = array_values(array_filter($allCocurricular, fn ($r) => ($r['category'] ?? '') === 'school_activity'));
    $sports = array_values(array_filter($allCocurricular, fn ($r) => ($r['category'] ?? '') === 'sport'));
    $leadership = array_values(array_filter($allCocurricular, fn ($r) => ($r['category'] ?? '') === 'leadership'));
    $workExperience = $student->work_experience ?: [];
    $referees = $student->referees ?: [];
@endphp

{{-- Section A - Additional Personal Particulars --}}
<div class="ps-section-title">Section A &mdash; Additional Personal Particulars</div>
<table class="ps-table">
    <tr><th>Citizenship</th><td>{{ $student->citizenship ?: '-' }}</td></tr>
    <tr><th>Ethnic Group</th><td>{{ $student->ethnic_group ?: '-' }}</td></tr>
    <tr><th>Religion</th><td>{{ $student->religion ?: '-' }}</td></tr>
    <tr><th>Undergraduate Course</th><td>{{ $student->undergraduate_course ?: '-' }}</td></tr>
</table>

{{-- Section B - Schools Attended --}}
<div class="ps-section-title">Section B &mdash; Schools Attended</div>
<table class="ps-table">
    <tr><th>Secondary School ('O' Level)</th><td>{{ $student->secondary_school ?: '-' }}</td></tr>
    <tr><th>College ('A' Level)</th><td>{{ $student->al_college ?: '-' }}</td></tr>
</table>
<table class="ps-table">
    <tr>
        <th></th><th>A</th><th>B</th><th>C</th><th>S</th><th>W/F</th><th>National Ranking / Z-Score</th>
    </tr>
    <tr>
        <th>GCE 'O' Level</th>
        <td>{{ $student->ol_grade_a ?? '-' }}</td>
        <td>{{ $student->ol_grade_b ?? '-' }}</td>
        <td>{{ $student->ol_grade_c ?? '-' }}</td>
        <td>{{ $student->ol_grade_s ?? '-' }}</td>
        <td>{{ $student->ol_grade_wf ?? '-' }}</td>
        <td>{{ $student->ol_national_ranking ?: '-' }}</td>
    </tr>
    <tr>
        <th>GCE 'A' Level</th>
        <td>{{ $student->al_grade_a ?? '-' }}</td>
        <td>{{ $student->al_grade_b ?? '-' }}</td>
        <td>{{ $student->al_grade_c ?? '-' }}</td>
        <td>{{ $student->al_grade_s ?? '-' }}</td>
        <td>{{ $student->al_grade_wf ?? '-' }}</td>
        <td>{{ $student->al_zscore ?: '-' }}</td>
    </tr>
</table>
<table class="ps-table">
    <tr><th>A' Level National Ranking by Stream</th><td>{{ $student->al_national_ranking ?: '-' }}</td></tr>
</table>

{{-- Section C - Academic Awards --}}
<div class="ps-section-title">Section C &mdash; Prizes, Academic Awards &amp; Special Achievements</div>
@if (empty($academicAwards))
    <p>None recorded.</p>
@else
    <table class="ps-table">
        <tr><th>Year of Award</th><th>Name of Award</th></tr>
        @foreach ($academicAwards as $row)
            <tr><td>{{ $row['year_of_award'] ?? '' }}</td><td>{{ $row['name_of_award'] ?? '' }}</td></tr>
        @endforeach
    </table>
@endif

{{-- Section D - Family Particulars --}}
<div class="ps-section-title">Section D &mdash; Family Particulars</div>
@if (empty($familyMembers))
    <p>None recorded.</p>
@else
    <table class="ps-table">
        <tr>
            <th>Name</th><th>Relationship</th><th>Date of Birth</th><th>Citizenship</th>
            <th>Marital Status</th><th>Staying with Applicant?</th><th>Occupation</th>
            <th>Employer / School</th><th>Gross Monthly Income (LKR)</th>
        </tr>
        @foreach ($familyMembers as $row)
            <tr>
                <td>{{ $row['name'] ?? '' }}</td>
                <td>{{ $row['relationship'] ?? '' }}</td>
                <td>{{ $row['date_of_birth'] ?? '' }}</td>
                <td>{{ $row['citizenship'] ?? '' }}</td>
                <td>{{ $row['marital_status'] ?? '' }}</td>
                <td>{{ $row['staying_with_applicant'] ?? '' }}</td>
                <td>{{ $row['occupation'] ?? '' }}</td>
                <td>{{ $row['employer_or_school'] ?? '' }}</td>
                <td>{{ $row['monthly_income'] ?? '' }}</td>
            </tr>
        @endforeach
    </table>
@endif

{{-- Section E - Applicant's Past Employment --}}
<div class="ps-section-title">Section E &mdash; Applicant's Past Employment Record</div>
@if (empty($employmentRecords))
    <p>None recorded.</p>
@else
    <table class="ps-table">
        <tr>
            <th>Company</th><th>Designation</th><th>Gross Monthly Income (LKR)</th>
            <th>From</th><th>To</th><th>Years / Months</th><th>Job Description</th><th>Staff Reporting to You</th>
        </tr>
        @foreach ($employmentRecords as $row)
            <tr>
                <td>{{ $row['company_name'] ?? '' }}</td>
                <td>{{ $row['designation'] ?? '' }}</td>
                <td>{{ $row['gross_monthly_income'] ?? '' }}</td>
                <td>{{ $row['period_from'] ?? '' }}</td>
                <td>{{ $row['period_to'] ?? '' }}</td>
                <td>{{ $row['years_months'] ?? '' }}</td>
                <td>{{ $row['job_description'] ?? '' }}</td>
                <td>{{ $row['staff_reporting'] ?? '' }}</td>
            </tr>
        @endforeach
    </table>
@endif

{{-- Section F - Financial Awards --}}
<div class="ps-section-title">Section F &mdash; Financial Awards / Scholarships / Loans Held</div>
@if (empty($financialAwards))
    <p>None recorded.</p>
@else
    <table class="ps-table">
        <tr><th>Name of Award</th><th>Tenure of Award</th><th>Value of Award (LKR)</th></tr>
        @foreach ($financialAwards as $row)
            <tr>
                <td>{{ $row['name_of_award'] ?? '' }}</td>
                <td>{{ $row['tenure_of_award'] ?? '' }}</td>
                <td>{{ $row['value_of_award'] ?? '' }}</td>
            </tr>
        @endforeach
    </table>
@endif

{{-- Section G - Co-Curricular Activities --}}
<div class="ps-section-title">Section G &mdash; Co-Curricular Activities</div>
<p style="font-weight:600; margin:10px 0 4px;">Activities in Secondary School / College</p>
@if (empty($schoolActivities))
    <p>None recorded.</p>
@else
    <table class="ps-table">
        <tr><th>Period</th><th>Type of Activity</th><th>Level of Representation / Position</th></tr>
        @foreach ($schoolActivities as $row)
            <tr><td>{{ $row['period'] ?? '' }}</td><td>{{ $row['detail'] ?? '' }}</td><td>{{ $row['level_role'] ?? '' }}</td></tr>
        @endforeach
    </table>
@endif

<p style="font-weight:600; margin:10px 0 4px;">Sports</p>
@if (empty($sports))
    <p>None recorded.</p>
@else
    <table class="ps-table">
        <tr><th>Period</th><th>Type of Sport</th><th>Level of Representation / Position</th></tr>
        @foreach ($sports as $row)
            <tr><td>{{ $row['period'] ?? '' }}</td><td>{{ $row['detail'] ?? '' }}</td><td>{{ $row['level_role'] ?? '' }}</td></tr>
        @endforeach
    </table>
@endif

<p style="font-weight:600; margin:10px 0 4px;">Leadership &amp; Community / Voluntary Service</p>
@if (empty($leadership))
    <p>None recorded.</p>
@else
    <table class="ps-table">
        <tr><th>Period</th><th>Description</th><th>Role / Position</th></tr>
        @foreach ($leadership as $row)
            <tr><td>{{ $row['period'] ?? '' }}</td><td>{{ $row['detail'] ?? '' }}</td><td>{{ $row['level_role'] ?? '' }}</td></tr>
        @endforeach
    </table>
@endif

{{-- Section H - Work Experience --}}
<div class="ps-section-title">Section H &mdash; Work Experience (Excluding Part-Time)</div>
@if (empty($workExperience))
    <p>None recorded.</p>
@else
    <table class="ps-table">
        <tr><th>Period</th><th>Description of Responsibility</th><th>Organisation &amp; Position</th></tr>
        @foreach ($workExperience as $row)
            <tr><td>{{ $row['period'] ?? '' }}</td><td>{{ $row['description'] ?? '' }}</td><td>{{ $row['org_position'] ?? '' }}</td></tr>
        @endforeach
    </table>
@endif

{{-- Section J - Other Information --}}
<div class="ps-section-title">Section J &mdash; Other Information</div>
<table class="ps-table">
    <tr>
        <th>Previously applied for a scholarship / sponsorship?</th>
        <td>
            {{ $student->prev_scholarship_flag === null ? '-' : ($student->prev_scholarship_flag ? 'Yes' : 'No') }}
            @if ($student->prev_scholarship_flag && $student->prev_scholarship_details)
                <br><span style="color:#5a4f47;">{{ $student->prev_scholarship_details }}</span>
            @endif
        </td>
    </tr>
    <tr>
        <th>Physical impairment or disease?</th>
        <td>
            {{ $student->health_condition_flag === null ? '-' : ($student->health_condition_flag ? 'Yes' : 'No') }}
            @if ($student->health_condition_flag && $student->health_condition_details)
                <br><span style="color:#5a4f47;">{{ $student->health_condition_details }}</span>
            @endif
        </td>
    </tr>
    <tr>
        <th>Ever charged in a court of law?</th>
        <td>
            {{ $student->legal_charge_flag === null ? '-' : ($student->legal_charge_flag ? 'Yes' : 'No') }}
            @if ($student->legal_charge_flag && $student->legal_charge_details)
                <br><span style="color:#5a4f47;">{{ $student->legal_charge_details }}</span>
            @endif
        </td>
    </tr>
</table>

{{-- Section K - Character Referees --}}
<div class="ps-section-title">Section K &mdash; Character Referees</div>
@if (empty($referees))
    <p>None recorded.</p>
@else
    <table class="ps-table">
        <tr><th>Name</th><th>Address</th><th>Occupation</th><th>Telephone</th><th>Years Known</th></tr>
        @foreach ($referees as $row)
            <tr>
                <td>{{ $row['name'] ?? '' }}</td>
                <td>{{ $row['address'] ?? '' }}</td>
                <td>{{ $row['occupation'] ?? '' }}</td>
                <td>{{ $row['telephone'] ?? '' }}</td>
                <td>{{ $row['years_known'] ?? '' }}</td>
            </tr>
        @endforeach
    </table>
@endif

{{-- Section L - Declaration --}}
<div class="ps-section-title">Section L &mdash; Declaration</div>
<table class="ps-table">
    <tr><th>Signature</th><td>{{ $student->declaration_signature ?: '-' }}</td></tr>
    <tr><th>Date</th><td>{{ $student->declaration_date ? format_date($student->declaration_date) : '-' }}</td></tr>
    <tr><th>Declaration Agreed</th><td>{{ $student->declaration_agreed ? 'Yes' : 'No' }}</td></tr>
</table>