<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuickApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    private const STATUSES = ['Pending', 'Under Review', 'Approved', 'Rejected'];

    public function index(Request $request): View
    {
        $statusFilter = $request->query('status', '');
        $search = trim((string) $request->query('q', ''));

        $query = QuickApplication::query()->with('program');

        if (in_array($statusFilter, self::STATUSES, true)) {
            $query->where('status', $statusFilter);
        }
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $like = '%'.$search.'%';
                $q->where('full_name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone', 'like', $like)
                    ->orWhere('district', 'like', $like)
                    ->orWhere('country', 'like', $like);
            });
        }

        $applications = $query->orderByDesc('created_at')->get();

        return view('admin.applications.index', [
            'pageTitle' => 'Applications',
            'active' => 'applications',
            'applications' => $applications,
            'statusFilter' => $statusFilter,
            'search' => $search,
            'statuses' => self::STATUSES,
        ]);
    }

    public function show(QuickApplication $application): View
    {
        return view('admin.applications.show', [
            'pageTitle' => 'Application: '.$application->full_name,
            'active' => 'applications',
            'app' => $application,
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(Request $request, QuickApplication $application): RedirectResponse
    {
        $status = $request->input('status', 'Pending');
        $notes = trim((string) $request->input('admin_notes', ''));

        if (in_array($status, self::STATUSES, true)) {
            $application->forceFill(['status' => $status, 'admin_notes' => $notes])->save();
            return redirect()->route('admin.applications.show', $application->id)->with('success', 'Application updated.');
        }

        return redirect()->route('admin.applications.show', $application->id);
    }
}
