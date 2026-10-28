<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Program;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    private const DESIGNATED_INSTITUTIONS = [
        'University of Colombo', 'University of Peradeniya', 'University of Moratuwa', 'University of Sri Jayewardenepura',
    ];

    private const ETHNIC_GROUPS = ['Sinhalese', 'Tamil', 'Muslim', 'Burgher', 'Other'];

    public function edit(): View
    {
        /** @var Student $student */
        $student = Auth::guard('student')->user();
        $locked = $student->status !== 'Draft';

        $institutionSelectValue = in_array($student->school_or_institute, self::DESIGNATED_INSTITUTIONS, true)
            ? $student->school_or_institute
            : ($student->school_or_institute !== null && $student->school_or_institute !== '' ? 'Other' : '');
        $institutionOtherValue = $institutionSelectValue === 'Other' ? $student->school_or_institute : '';

        $ethnicSelectValue = in_array($student->ethnic_group, self::ETHNIC_GROUPS, true)
            ? $student->ethnic_group
            : (filled($student->ethnic_group) ? 'Other' : '');
        $ethnicOtherValue = $ethnicSelectValue === 'Other' ? $student->ethnic_group : '';

        return view('student.profile', [
            'pageTitle' => 'My Profile',
            'active' => 'profile',
            'student' => $student,
            'locked' => $locked,
            'programs' => Program::query()->where('is_active', true)->orderBy('display_order')->orderBy('id')->get(),
            'addressOptions' => $this->addressOptions(),
            'ethnicGroups' => self::ETHNIC_GROUPS,
            'designatedInstitutions' => self::DESIGNATED_INSTITUTIONS,
            'incomeBands' => config('poh.household_income_bands'),
            'institutionSelectValue' => $institutionSelectValue,
            'institutionOtherValue' => $institutionOtherValue,
            'ethnicSelectValue' => $ethnicSelectValue,
            'ethnicOtherValue' => $ethnicOtherValue,
            'citizenship' => $student->citizenship ?: 'Sri Lanka',
            'country' => $student->country ?: 'Sri Lanka',
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var Student $student */
        $student = Auth::guard('student')->user();

        if ($student->status !== 'Draft') {
            return redirect()->route('student.profile.edit');
        }

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:200'],
            'date_of_birth' => ['required', 'date'],
            'gender' => ['required', 'in:Male,Female,Other'],
            'nic_or_birth_cert' => ['required', 'string', 'max:60'],
            'citizenship' => ['required', 'string', 'max:100'],
            'ethnic_group' => ['nullable', 'string', 'max:100'],
            'ethnic_group_other' => ['nullable', 'string', 'max:100'],
            'religion' => ['nullable', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:400'],
            'district' => [
                'required', 'string', 'max:100',
                Rule::exists('districts', 'name')->where(fn ($query) => $query->whereIn(
                    'country_id',
                    Country::query()->where('name', $request->input('country'))->pluck('id')
                )),
            ],
            'country' => ['required', 'string', 'max:100', Rule::exists('countries', 'name')],
            'phone' => ['required', 'digits_between:1,15'],
            'undergraduate_course' => ['nullable', 'string', 'max:200'],
            'institution_select' => ['required', 'string', 'max:200'],
            'institution_other' => ['nullable', 'string', 'max:200'],
            'grade_or_year' => ['nullable', 'string', 'max:60'],
            'guardian_name' => ['required', 'string', 'max:200'],
            'guardian_occupation' => ['nullable', 'string', 'max:150'],
            'household_income_band' => ['nullable', 'string'],
            'program_id' => ['nullable', 'exists:programs,id'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
        ]);

        $ethnicGroupValue = $data['ethnic_group'] === 'Other' && filled($data['ethnic_group_other'] ?? null)
            ? $data['ethnic_group_other'] : $data['ethnic_group'];
        $institutionValue = $data['institution_select'] === 'Other' && filled($data['institution_other'] ?? null)
            ? $data['institution_other'] : $data['institution_select'];

        if ($institutionValue === '' || $institutionValue === null) {
            return back()->withInput()->withErrors(['institution_select' => 'Please select the university / institution you are applying to attend.']);
        }

        $photoPath = $student->photo;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('students', 'public');
            if ($student->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($student->photo);
            }
        } elseif (empty($student->photo)) {
            return back()->withInput()->withErrors(['photo' => 'Please upload a photo.']);
        }

        $student->forceFill([
            'full_name' => $data['full_name'],
            'program_id' => $data['program_id'] ?: null,
            'undergraduate_course' => $data['undergraduate_course'] ?: null,
            'date_of_birth' => $data['date_of_birth'],
            'gender' => $data['gender'],
            'nic_or_birth_cert' => $data['nic_or_birth_cert'],
            'citizenship' => $data['citizenship'],
            'ethnic_group' => $ethnicGroupValue ?: null,
            'religion' => $data['religion'] ?: null,
            'address' => $data['address'],
            'district' => $data['district'],
            'country' => $data['country'],
            'phone' => $data['phone'],
            'school_or_institute' => $institutionValue,
            'grade_or_year' => $data['grade_or_year'] ?: null,
            'guardian_name' => $data['guardian_name'],
            'guardian_occupation' => $data['guardian_occupation'] ?: null,
            'household_income_band' => $data['household_income_band'] ?: null,
            'photo' => $photoPath,
        ])->save();

        return redirect()->route('student.profile.edit')->with('success', 'Profile saved.');
    }

    private function addressOptions(): array
    {
        return Country::query()->with('districts')->orderBy('name')->get()->mapWithKeys(
            fn (Country $country) => [$country->name => $country->districts->pluck('name')->values()->all()]
        )->all();
    }
}
