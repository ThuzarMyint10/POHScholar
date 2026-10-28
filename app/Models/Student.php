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
}
