<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function index(): View
    {
        $programs = Program::query()->orderBy('display_order')->orderBy('id')->get();

        return view('admin.programs.index', [
            'pageTitle' => 'Scholarship Programs',
            'active' => 'programs',
            'programs' => $programs,
        ]);
    }

    public function create(): View
    {
        return view('admin.programs.form', [
            'pageTitle' => 'Add Program',
            'active' => 'programs',
            'program' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['slug'] = $this->uniqueSlug($data['title'], null);

        Program::query()->create($data);

        return redirect()->route('admin.programs.index')->with('success', 'Program added.');
    }

    public function edit(Program $program): View
    {
        return view('admin.programs.form', [
            'pageTitle' => 'Edit Program',
            'active' => 'programs',
            'program' => $program,
        ]);
    }

    public function update(Request $request, Program $program): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['slug'] = $this->uniqueSlug($data['title'], $program->id);

        $program->forceFill($data)->save();

        return redirect()->route('admin.programs.index')->with('success', 'Program updated.');
    }

    public function destroy(Program $program): RedirectResponse
    {
        $program->delete();

        return redirect()->route('admin.programs.index')->with('success', 'Program deleted.');
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'summary' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'eligibility' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'display_order' => ['nullable'],
        ], [
            'title.required' => 'Please enter a program title.',
        ]);

        return [
            'title' => trim((string) $data['title']),
            'summary' => trim((string) ($data['summary'] ?? '')),
            'description' => trim((string) ($data['description'] ?? '')),
            'eligibility' => trim((string) ($data['eligibility'] ?? '')),
            'benefits' => trim((string) ($data['benefits'] ?? '')),
            'is_active' => $request->boolean('is_active') ? 1 : 0,
            'display_order' => (int) ($data['display_order'] ?? 0),
        ];
    }

    private function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);

        return trim($text, '-') ?: 'program';
    }

    private function uniqueSlug(string $title, ?int $excludeId): string
    {
        $base = $this->slugify($title);
        $slug = $base;
        $i = 2;

        while (
            Program::query()
                ->where('slug', $slug)
                ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
