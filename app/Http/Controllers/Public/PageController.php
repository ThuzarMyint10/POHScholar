<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\ContactMessage;
use App\Models\NewsEvent;
use App\Models\Program;
use App\Models\QuickApplication;
use App\Models\Scholar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return view('public.home', [
            'pageTitle' => config('app.name').' | Home',
            'activeNav' => 'home',
            'programs' => Program::query()->where('is_active', true)
                ->orderBy('display_order')->orderBy('id')->limit(3)->get(),
            'news' => NewsEvent::query()->where('is_published', true)
                ->orderByDesc('event_date')->orderByDesc('id')->limit(3)->get(),
            'featuredScholars' => Scholar::query()->with('program')
                ->where('status', 'Active')
                ->where('is_featured', true)
                ->orderByDesc('created_at')->orderByDesc('id')->limit(3)->get(),
        ]);
    }

    public function about(): View
    {
        return view('public.about', [
            'pageTitle' => config('app.name').' | About Us',
            'activeNav' => 'about',
        ]);
    }

    public function programs(): View
    {
        return view('public.programs', [
            'pageTitle' => config('app.name').' | Scholarship Programs',
            'activeNav' => 'programs',
            'programs' => Program::query()->where('is_active', true)
                ->orderBy('display_order')->orderBy('id')->get(),
        ]);
    }

    public function showApply(Request $request): View
    {
        return view('public.apply', [
            'pageTitle' => config('app.name').' | Apply for a Scholarship',
            'activeNav' => 'apply',
            'programs' => Program::query()->where('is_active', true)
                ->orderBy('display_order')->orderBy('id')->get(),
            'addressOptions' => $this->addressOptions(),
            'incomeBands' => config('poh.household_income_bands'),
            'old' => array_merge(
                ['program_id' => $request->query('program', ''), 'country' => 'Sri Lanka'],
                old()
            ),
        ]);
    }

    public function storeApply(Request $request): RedirectResponse
    {
        // Field-for-field the same required/optional rules as the original
        // apply.php (email is the one optional-but-validated-if-present field).
        $data = $request->validate([
            'program_id' => ['nullable', 'exists:programs,id'],
            'full_name' => ['required', 'string', 'max:200'],
            'date_of_birth' => ['required', 'date'],
            'gender' => ['required', 'in:Male,Female,Other'],
            'nic_or_birth_cert' => ['nullable', 'string', 'max:60'],
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
            'email' => ['nullable', 'email', 'max:150'],
            'school_or_institute' => ['required', 'string', 'max:200'],
            'grade_or_year' => ['nullable', 'string', 'max:60'],
            'guardian_name' => ['required', 'string', 'max:200'],
            'guardian_occupation' => ['nullable', 'string', 'max:150'],
            'household_income_band' => ['nullable', 'string'],
            'reason_for_applying' => ['required', 'string'],
        ]);

        // No email is sent here - same as the original quick-apply form,
        // which simply records the submission for admin review under
        // "Quick Applications".
        QuickApplication::query()->create($data);

        return redirect()->route('apply')
            ->with('success', 'Thank you - your application has been received. Our team will be in touch soon.');
    }

    private function addressOptions(): array
    {
        return Country::query()->with('districts')->orderBy('name')->get()->mapWithKeys(
            fn (Country $country) => [$country->name => $country->districts->pluck('name')->values()->all()]
        )->all();
    }

    public function showContact(): View
    {
        return view('public.contact', [
            'pageTitle' => config('app.name').' | Contact Us',
            'activeNav' => 'contact',
            'old' => old(),
        ]);
    }

    public function storeContact(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'digits_between:1,15'],
            'subject' => ['nullable', 'string', 'max:220'],
            'message' => ['required', 'string'],
        ]);

        // No email is sent here either - matches the original contact.php,
        // which records the message for admin review under "Messages".
        ContactMessage::query()->create($data);

        return redirect()->route('contact')
            ->with('success', 'Thank you for reaching out! We will get back to you as soon as possible.');
    }
}
