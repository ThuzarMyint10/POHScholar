<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $table = 'students';

    // updated_at is maintained by the DB (ON UPDATE CURRENT_TIMESTAMP);
    // created_at only needs to be set once on insert.
    public $timestamps = false;

    protected $hidden = ['password_hash'];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'academic_awards' => 'array',
            'financial_awards' => 'array',
            'family_members' => 'array',
            'employment_records' => 'array',
            'cocurricular_activities' => 'array',
            'work_experience' => 'array',
            'referees' => 'array',
            'section_rank_admin' => 'array',
            'section_rank_mrs_poh' => 'array',
            'prev_scholarship_flag' => 'boolean',
            'health_condition_flag' => 'boolean',
            'legal_charge_flag' => 'boolean',
            'declaration_agreed' => 'boolean',
            'declaration_date' => 'date',
            'is_active' => 'boolean',
            'evaluation_score' => 'decimal:1',
            'submitted_at' => 'datetime',
            'evaluated_at' => 'datetime',
            'mrs_poh_updated_at' => 'datetime',
            'ai_rank_family_score' => 'integer',
            'ai_rank_academic_score' => 'integer',
            'ai_rank_economic_score' => 'integer',
            'ai_rank_essay_score' => 'integer',
            'ai_rank_composite_score' => 'integer',
            'ai_ranked_at' => 'datetime',
            'accepted_at' => 'datetime',
            'final_info_updated_at' => 'datetime',
            'essay_updated_at' => 'datetime',
            'last_login_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(StudentDocument::class);
    }

    public function academicResults(): HasMany
    {
        return $this->hasMany(AcademicResult::class)->orderBy('period_number');
    }

    public function evaluatedBy(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'evaluated_by');
    }

    /** Locked once the application has left Draft status. */
    public function isLocked(): bool
    {
        return $this->status !== 'Draft';
    }

    public function profileComplete(): bool
    {
        return filled($this->full_name)
            && filled($this->date_of_birth)
            && filled($this->gender)
            && filled($this->nic_or_birth_cert)
            && filled($this->citizenship)
            && filled($this->address)
            && filled($this->district)
            && filled($this->country)
            && filled($this->phone)
            && filled($this->school_or_institute)
            && filled($this->guardian_name)
            && filled($this->guardian_occupation)
            && filled($this->household_income_band)
            && filled($this->undergraduate_course)
            && filled($this->photo);
    }

    public function academicRecordComplete(): bool
    {
        $olFields = ['ol_grade_a', 'ol_grade_b', 'ol_grade_c', 'ol_grade_s', 'ol_grade_wf'];
        $alFields = ['al_grade_a', 'al_grade_b', 'al_grade_c', 'al_grade_s', 'al_grade_wf'];

        foreach (array_merge($olFields, $alFields) as $field) {
            if (! filled($this->{$field})) {
                return false;
            }
        }

        return filled($this->secondary_school)
            && filled($this->al_college)
            && filled($this->ol_national_ranking)
            && filled($this->al_zscore)
            && filled($this->al_national_ranking);
    }

    public function familyBackgroundComplete(): bool
    {
        $members = $this->family_members;
        if (! is_array($members) || count($members) < 2) {
            return false;
        }

        foreach (array_slice($members, 0, 2) as $member) {
            foreach (['name', 'relationship', 'date_of_birth', 'citizenship', 'marital_status', 'staying_with_applicant', 'occupation', 'employer_or_school', 'monthly_income'] as $field) {
                if (! is_array($member) || ! filled($member[$field] ?? null)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function activitiesComplete(): bool
    {
        $activities = $this->cocurricular_activities;
        return is_array($activities)
            && count(array_filter($activities, function ($row) {
                return is_array($row)
                    && filled($row['period'] ?? null)
                    && filled($row['detail'] ?? null)
                    && filled($row['level_role'] ?? null);
            })) > 0;
    }

    public function declarationComplete(): bool
    {
        if (! $this->declaration_agreed || ! filled($this->declaration_signature) || ! filled($this->declaration_date)) {
            return false;
        }

        foreach (['prev_scholarship_flag', 'health_condition_flag', 'legal_charge_flag'] as $field) {
            if ($this->{$field} === null) {
                return false;
            }
        }

        $referees = $this->referees;
        if (! is_array($referees) || count($referees) < 2) {
            return false;
        }

        foreach (array_slice($referees, 0, 2) as $referee) {
            foreach (['name', 'address', 'occupation', 'telephone', 'years_known'] as $field) {
                if (! is_array($referee) || ! filled($referee[$field] ?? null)) {
                    return false;
                }
            }
        }

        if ($this->prev_scholarship_flag && ! filled($this->prev_scholarship_details)) {
            return false;
        }
        if ($this->health_condition_flag && ! filled($this->health_condition_details)) {
            return false;
        }
        if ($this->legal_charge_flag && ! filled($this->legal_charge_details)) {
            return false;
        }

        return true;
    }

    public function missingSubmissionSections(): array
    {
        $missing = [];
        if (! $this->profileComplete()) {
            $missing[] = 'Profile';
        }
        if (! $this->academicRecordComplete()) {
            $missing[] = 'Academic Record';
        }
        if (! $this->familyBackgroundComplete()) {
            $missing[] = 'Family & Background';
        }
        if (! $this->activitiesComplete()) {
            $missing[] = 'Activities & Experience';
        }
        $essayWordCount = trim((string) $this->essay) === '' ? 0 : str_word_count(trim((string) $this->essay));
        if ($essayWordCount < 100) {
            $missing[] = 'Essay (at least 100 words)';
        }
        if (! $this->documents()->exists()) {
            $missing[] = 'Certificates / Documents';
        }
        if (! $this->declarationComplete()) {
            $missing[] = 'Other Information & Declaration';
        }

        return $missing;
    }
}
