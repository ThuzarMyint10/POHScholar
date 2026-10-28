<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Scholar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ScholarController extends Controller
{
    private const STATUSES = ['Active', 'Graduated', 'Discontinued'];

    public function index(Request $request): View
    {
        $statusFilter = $request->query('status', 'Active');
        $statuses = ['Active', 'Graduated', 'Discontinued'];
        $query = Scholar::query()->with('program');

        if ($statusFilter !== 'All' && in_array($statusFilter, $statuses, true)) {
            $query->where('status', $statusFilter);
        } else {
            $statusFilter = 'All';
        }

        $scholars = $query->orderByDesc('created_at')->get();
        $statusCounts = Scholar::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.scholars.index', [
            'pageTitle' => 'Scholar Register',
            'active' => 'scholars',
            'scholars' => $scholars,
            'statusCounts' => $statusCounts,
            'statusFilter' => $statusFilter,
            'statuses' => $statuses,
        ]);
    }

    public function create(): View
    {
        return view('admin.scholars.form', [
            'pageTitle' => 'Add Scholar',
            'active' => 'scholars',
            'scholar' => null,
            'programs' => Program::query()->orderBy('display_order')->orderBy('id')->get(),
            'statuses' => self::STATUSES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('scholars', 'public');
        }

        Scholar::query()->create([
            'full_name' => $data['full_name'],
            'program_id' => $data['program_id'] ?: null,
            'school_or_institute' => $data['school_or_institute'],
            'district' => $data['district'],
            'year_enrolled' => $data['year_enrolled'] !== '' ? (int) $data['year_enrolled'] : null,
            'status' => $data['status'],
            'story' => $data['story'],
            'is_featured' => $data['is_featured'],
            'photo' => $photoPath,
        ]);

        return redirect()->route('admin.scholars.index')->with('success', 'Scholar record added.');
    }

    public function edit(Scholar $scholar): View
    {
        return view('admin.scholars.form', [
            'pageTitle' => 'Edit Scholar',
            'active' => 'scholars',
            'scholar' => $scholar,
            'programs' => Program::query()->orderBy('display_order')->orderBy('id')->get(),
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(Request $request, Scholar $scholar): RedirectResponse
    {
        $data = $this->validateData($request);

        $photoPath = $scholar->photo;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('scholars', 'public');
            if ($scholar->photo) {
                Storage::disk('public')->delete($scholar->photo);
            }
        }

        DB::transaction(function () use ($scholar, $data, $photoPath): void {
            $scholar->forceFill([
                'full_name' => $data['full_name'],
                'program_id' => $data['program_id'] ?: null,
                'school_or_institute' => $data['school_or_institute'],
                'district' => $data['district'],
                'year_enrolled' => $data['year_enrolled'] !== '' ? (int) $data['year_enrolled'] : null,
                'status' => $data['status'],
                'story' => $data['story'],
                'is_featured' => $data['is_featured'],
                'photo' => $photoPath,
            ])->save();

            if ($scholar->student_id && $scholar->student) {
                $studentStatus = match ($data['status']) {
                    'Active' => 'Approved',
                    'Graduated' => 'Graduated',
                    'Discontinued' => 'Discontinued',
                };

                $scholar->student->forceFill([
                    'status' => $studentStatus,
                    'is_active' => $studentStatus === 'Approved',
                ])->save();
            }
        });

        return redirect()->route('admin.scholars.index')->with('success', 'Scholar record updated.');
    }

    public function destroy(Scholar $scholar): RedirectResponse
    {
        if ($scholar->photo) {
            Storage::disk('public')->delete($scholar->photo);
        }
        $scholar->delete();

        return redirect()->route('admin.scholars.index')->with('success', 'Scholar record deleted.');
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:200'],
            'program_id' => ['nullable', 'exists:programs,id'],
            'school_or_institute' => ['nullable', 'string', 'max:200'],
            'district' => ['nullable', 'string', 'max:100'],
            'year_enrolled' => ['nullable', 'string', 'max:10'],
            'status' => ['required', 'in:Active,Graduated,Discontinued'],
            'story' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
        ], [
            'full_name.required' => 'Please enter the scholar name.',
            'status.in' => 'Invalid status.',
        ]);

        $data['program_id'] = $data['program_id'] ?? '';
        $data['school_or_institute'] = trim((string) ($data['school_or_institute'] ?? ''));
        $data['district'] = trim((string) ($data['district'] ?? ''));
        $data['year_enrolled'] = trim((string) ($data['year_enrolled'] ?? ''));
        $data['story'] = trim((string) ($data['story'] ?? ''));
        $data['is_featured'] = $request->boolean('is_featured') ? 1 : 0;

        return $data;
    }
}
