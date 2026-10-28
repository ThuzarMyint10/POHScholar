<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Support\RowParser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ActivitiesController extends Controller
{
    public function edit(): View
    {
        /** @var Student $student */
        $student = Auth::guard('student')->user();
        $locked = $student->status !== 'Draft';

        $all = $student->cocurricular_activities ?: [];
        $schoolActivities = array_values(array_filter($all, fn ($r) => ($r['category'] ?? '') === 'school_activity'));
        $sports = array_values(array_filter($all, fn ($r) => ($r['category'] ?? '') === 'sport'));
        $leadership = array_values(array_filter($all, fn ($r) => ($r['category'] ?? '') === 'leadership'));
        $workExperience = $student->work_experience ?: [];

        return view('student.activities', [
            'pageTitle' => 'Activities & Experience',
            'active' => 'activities',
            'student' => $student,
            'locked' => $locked,
            'schoolActivities' => $schoolActivities ?: [['period' => '', 'detail' => '', 'level_role' => '']],
            'sports' => $sports ?: [['period' => '', 'detail' => '', 'level_role' => '']],
            'leadership' => $leadership ?: [['period' => '', 'detail' => '', 'level_role' => '']],
            'workExperience' => $workExperience ?: [['period' => '', 'description' => '', 'org_position' => '']],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var Student $student */
        $student = Auth::guard('student')->user();

        if ($student->status !== 'Draft') {
            return redirect()->route('student.activities.edit');
        }

        $schoolActivities = RowParser::fromRequest($request, ['sa_period' => 'period', 'sa_detail' => 'detail', 'sa_level' => 'level_role']);
        $sports = RowParser::fromRequest($request, ['sp_period' => 'period', 'sp_detail' => 'detail', 'sp_level' => 'level_role']);
        $leadership = RowParser::fromRequest($request, ['ld_period' => 'period', 'ld_detail' => 'detail', 'ld_level' => 'level_role']);
        $workExperience = RowParser::fromRequest($request, ['we_period' => 'period', 'we_description' => 'description', 'we_org_position' => 'org_position']);

        $tag = fn (array $rows, string $category) => array_map(function ($r) use ($category) {
            $r['category'] = $category;

            return $r;
        }, $rows);

        $allCocurricular = array_merge(
            $tag($schoolActivities, 'school_activity'),
            $tag($sports, 'sport'),
            $tag($leadership, 'leadership')
        );

        $student->forceFill([
            'cocurricular_activities' => $allCocurricular,
            'work_experience' => $workExperience,
        ])->save();

        return redirect()->route('student.activities.edit')->with('success', 'Activities & experience saved.');
    }
}
