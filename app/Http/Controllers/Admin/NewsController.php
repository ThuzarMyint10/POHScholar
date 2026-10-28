<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        $items = NewsEvent::query()->orderByDesc('created_at')->get();

        return view('admin.news.index', [
            'pageTitle' => 'News & Events',
            'active' => 'news',
            'items' => $items,
        ]);
    }

    public function create(): View
    {
        return view('admin.news.form', [
            'pageTitle' => 'Add News / Event',
            'active' => 'news',
            'item' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('news', 'public');
        }

        NewsEvent::query()->create($data);

        return redirect()->route('admin.news.index')->with('success', 'Item added.');
    }

    public function edit(NewsEvent $newsEvent): View
    {
        return view('admin.news.form', [
            'pageTitle' => 'Edit Item',
            'active' => 'news',
            'item' => $newsEvent,
        ]);
    }

    public function update(Request $request, NewsEvent $newsEvent): RedirectResponse
    {
        $data = $this->validateData($request);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('news', 'public');
            if ($newsEvent->image) {
                Storage::disk('public')->delete($newsEvent->image);
            }
        } else {
            $data['image'] = $newsEvent->image;
        }

        $newsEvent->forceFill($data)->save();

        return redirect()->route('admin.news.index')->with('success', 'Item updated.');
    }

    public function destroy(NewsEvent $newsEvent): RedirectResponse
    {
        if ($newsEvent->image) {
            Storage::disk('public')->delete($newsEvent->image);
        }
        $newsEvent->delete();

        return redirect()->route('admin.news.index')->with('success', 'Item deleted.');
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:250'],
            'category' => ['required', 'in:News,Event'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'event_date' => ['nullable', 'date'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
        ], [
            'title.required' => 'Please enter a title.',
            'category.in' => 'Invalid category.',
        ]);

        return [
            'title' => trim((string) $data['title']),
            'category' => $data['category'],
            'excerpt' => trim((string) ($data['excerpt'] ?? '')),
            'content' => trim((string) ($data['content'] ?? '')),
            'event_date' => $data['event_date'] ?: null,
            'is_published' => $request->boolean('is_published') ? 1 : 0,
        ];
    }
}
