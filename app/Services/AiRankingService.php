<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AiRankingService
{
    private const CATEGORY_KEYS = [
        'family_background_score',
        'academic_result_score',
        'economic_status_score',
        'essay_score',
    ];

    public function rank(Student $student): array
    {
        $apiKey = trim((string) config('services.anthropic.api_key'));
        if ($apiKey === '') {
            throw new RuntimeException('ANTHROPIC_API_KEY is not configured in .env.');
        }

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
            // 'anthropic-workspace-id' => config('services.anthropic.workspace_id'),
        ])->timeout(60)->post('https://api.anthropic.com/v1/messages', [
            'model' => (string) config('services.anthropic.model', 'claude-sonnet-4-5'),
            'max_tokens' => 1024,
            'system' => $this->systemPrompt(),
            'messages' => [[
                'role' => 'user',
                'content' => $this->buildPrompt($student),
            ]],
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Anthropic API request failed: '.$response->status().' '.$response->body());
        }

        $text = collect((array) $response->json('content', []))
            ->where('type', 'text')
            ->pluck('text')
            ->implode("\n");
        $parsed = $this->parseJson($text);

        $result = [];
        foreach (self::CATEGORY_KEYS as $key) {
            $result[$key] = $this->clampScore($parsed[$key] ?? null);
        }
        $result['composite_score'] = $this->clampScore($parsed['composite_score'] ?? null)
            ?? $this->averageOf($parsed);
        $result['summary'] = trim((string) ($parsed['summary'] ?? '')) ?: null;

        if ($result['composite_score'] === null) {
            throw new RuntimeException('Anthropic returned no usable ranking scores.');
        }

        return $result;
    }

    private function systemPrompt(): string
    {
        return 'You rank scholarship applications fairly and consistently. Return only a valid JSON object with exactly these keys: family_background_score, academic_result_score, economic_status_score, essay_score, composite_score, summary. Each score must be a WHOLE NUMBER from 0 to 5, integers only, no decimals. The summary must briefly explain the evidence used. Do not infer protected characteristics or make a decision based on race, religion, gender, disability, or other protected traits.';
    }

    private function buildPrompt(Student $student): string
    {
        $fields = [
            'Applicant' => [
                'name' => $student->full_name,
                'district' => $student->district,
                'school_or_institute' => $student->school_or_institute,
                'grade_or_year' => $student->grade_or_year,
                'undergraduate_course' => $student->undergraduate_course,
            ],
            'Family background' => [
                'guardian_name' => $student->guardian_name,
                'guardian_occupation' => $student->guardian_occupation,
                'family_members' => $student->family_members,
                'employment_records' => $student->employment_records,
            ],
            'Academic results and awards' => [
                'ol_grades' => [$student->ol_grade_a, $student->ol_grade_b, $student->ol_grade_c, $student->ol_grade_s, $student->ol_grade_wf],
                'al_grades' => [$student->al_grade_a, $student->al_grade_b, $student->al_grade_c, $student->al_grade_s, $student->al_grade_wf],
                'ol_national_ranking' => $student->ol_national_ranking,
                'al_zscore' => $student->al_zscore,
                'al_national_ranking' => $student->al_national_ranking,
                'academic_awards' => $student->academic_awards,
            ],
            'Economic status' => [
                'household_income_band' => $student->household_income_band,
                'financial_awards' => $student->financial_awards,
            ],
            'Essay and activities' => [
                'essay' => $student->essay,
                'cocurricular_activities' => $student->cocurricular_activities,
                'work_experience' => $student->work_experience,
            ],
        ];

        return "Evaluate this scholarship application using only the information below. Do not penalize missing information.\n\n".json_encode($fields, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    private function parseJson(string $text): array
    {
        $text = trim(preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $text) ?? $text);
        $parsed = json_decode($text, true);

        if (! is_array($parsed)) {
            throw new RuntimeException('Anthropic returned invalid JSON for the ranking.');
        }

        return $parsed;
    }

    private function clampScore(mixed $value): ?int
    {
        if ($value === null || ! is_numeric($value)) {
            return null;
        }

        return (int) round(max(0, min(5, (float) $value)));
    }

    private function averageOf(array $parsed): ?int
    {
        $values = [];
        foreach (self::CATEGORY_KEYS as $key) {
            $score = $this->clampScore($parsed[$key] ?? null);
            if ($score !== null) {
                $values[] = $score;
            }
        }

        return $values === [] ? null : (int) round(array_sum($values) / count($values));
    }
}