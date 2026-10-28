<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicResult;
use App\Models\ContactMessage;
use App\Models\QuickApplication;
use App\Models\Scholar;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_students' => Student::query()->where('status', '!=', 'Draft')->count(),
            'pending_students' => Student::query()->whereIn('status', ['Submitted', 'Under Review'])->count(),
            'total_applications' => QuickApplication::query()->count(),
            'total_scholars' => Scholar::query()->where('status', 'Active')->count(),
            'graduated_scholars' => Scholar::query()->where('status', 'Graduated')->count(),
            'unread_messages' => ContactMessage::query()->where('is_read', false)->count(),
            'overdue_results' => AcademicResult::query()->whereNull('submitted_at')->where('due_date', '<', now()->toDateString())->distinct()->count('student_id'),
        ];

        $recentStudents = Student::query()->with('program')
            ->where('status', '!=', 'Draft')
            ->orderByDesc('submitted_at')
            ->limit(6)
            ->get();

        return view('admin.dashboard', [
            'pageTitle' => 'Dashboard',
            'active' => 'dashboard',
            'stats' => $stats,
            'recentStudents' => $recentStudents,
            'admin' => Auth::guard('admin')->user(),
        ]);
    }
}
