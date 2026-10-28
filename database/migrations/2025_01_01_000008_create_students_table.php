<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // The "students" table doubles as the self-service candidate account
    // AND their full official application record (POH's paper "Application
    // Form for full time undergraduate studies at designated universities",
    // Sections A-L). Repeatable rows (family members, awards, referees,
    // etc.) are stored as JSON arrays rather than separate join tables, to
    // mirror the original schema.sql 1:1.
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('email', 190)->unique();
            $table->string('password_hash', 255);

            // Profile (mirrors the public quick-apply form)
            $table->string('full_name', 200)->nullable();
            $table->string('photo', 255)->nullable();
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->string('nic_or_birth_cert', 60)->nullable();
            $table->string('address', 400)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('school_or_institute', 200)->nullable();
            $table->string('grade_or_year', 60)->nullable();
            $table->string('guardian_name', 200)->nullable();
            $table->string('guardian_occupation', 150)->nullable();
            $table->enum('household_income_band', [
                'Below Rs.25,000', 'Rs.25,000 - Rs.50,000', 'Rs.50,000 - Rs.75,000', 'Above Rs.75,000', 'Prefer not to say',
            ])->nullable();

            // Section A - additional personal particulars
            $table->string('citizenship', 100)->nullable();
            $table->string('ethnic_group', 100)->nullable();
            $table->string('religion', 100)->nullable();
            $table->string('undergraduate_course', 200)->nullable();

            // Section B - schools attended
            $table->string('secondary_school', 200)->nullable();
            $table->string('al_college', 200)->nullable();
            $table->unsignedTinyInteger('ol_grade_a')->nullable();
            $table->unsignedTinyInteger('ol_grade_b')->nullable();
            $table->unsignedTinyInteger('ol_grade_c')->nullable();
            $table->unsignedTinyInteger('ol_grade_s')->nullable();
            $table->unsignedTinyInteger('ol_grade_wf')->nullable();
            $table->string('ol_national_ranking', 60)->nullable();
            $table->unsignedTinyInteger('al_grade_a')->nullable();
            $table->unsignedTinyInteger('al_grade_b')->nullable();
            $table->unsignedTinyInteger('al_grade_c')->nullable();
            $table->unsignedTinyInteger('al_grade_s')->nullable();
            $table->unsignedTinyInteger('al_grade_wf')->nullable();
            $table->string('al_zscore', 30)->nullable();
            $table->string('al_national_ranking', 100)->nullable();

            // Section C (academic_awards) and Section F (financial_awards) -
            // each a JSON array of rows.
            $table->json('academic_awards')->nullable();
            $table->json('financial_awards')->nullable();

            // Section D - family particulars (JSON array of rows)
            $table->json('family_members')->nullable();

            // Section E - past employment record, applicant's own (JSON array)
            $table->json('employment_records')->nullable();

            // Section G - co-curricular activities, tagged by "category"
            // (school_activity / sport / leadership) within the JSON array
            $table->json('cocurricular_activities')->nullable();

            // Section H - work experience (JSON array)
            $table->json('work_experience')->nullable();

            // Section J - other information
            $table->boolean('prev_scholarship_flag')->nullable();
            $table->text('prev_scholarship_details')->nullable();
            $table->boolean('health_condition_flag')->nullable();
            $table->text('health_condition_details')->nullable();
            $table->boolean('legal_charge_flag')->nullable();
            $table->text('legal_charge_details')->nullable();

            // Section K - character referees (JSON array, max 2 enforced in code)
            $table->json('referees')->nullable();

            // Section L - declaration
            $table->string('declaration_signature', 200)->nullable();
            $table->date('declaration_date')->nullable();
            $table->boolean('declaration_agreed')->default(false);

            // Essay (its own portal step; covers Section I)
            $table->text('essay')->nullable();
            $table->dateTime('essay_updated_at')->nullable();

            // Submission
            $table->string('application_number', 30)->nullable()->unique();
            $table->enum('status', ['Draft', 'Submitted', 'Under Review', 'Approved', 'Rejected'])->default('Draft');
            $table->dateTime('submitted_at')->nullable();

            // Admin-only evaluation
            $table->decimal('evaluation_score', 4, 1)->nullable();
            $table->text('evaluation_notes')->nullable();
            $table->foreignId('evaluated_by')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->dateTime('evaluated_at')->nullable();

            // Admin-only: per-section rank, one JSON object per evaluator
            // (Admin and Mrs. Poh evaluate independently), keyed by section
            // letter e.g. {"A":"4.5","B":"4",...,"I":"5"} (I = Essay). Every
            // rank is on a 0-5 scale, same as evaluation_score above.
            $table->json('section_rank_admin')->nullable();
            $table->json('section_rank_mrs_poh')->nullable();

            // Admin-only: Mrs. Poh's dedicated remarks, separate from the
            // general evaluation above
            $table->text('mrs_poh_remarks')->nullable();
            $table->dateTime('mrs_poh_updated_at')->nullable();

            // Acceptance workflow: set the moment status transitions to
            // 'Approved' via the admin "Accept" quick action
            $table->dateTime('accepted_at')->nullable();

            // Final information: collected only after acceptance
            $table->string('bank_account_name', 200)->nullable();
            $table->string('bank_name', 150)->nullable();
            $table->string('bank_branch', 150)->nullable();
            $table->string('bank_account_number', 60)->nullable();
            $table->string('emergency_contact_name', 200)->nullable();
            $table->string('emergency_contact_phone', 30)->nullable();
            $table->string('emergency_contact_relationship', 100)->nullable();
            $table->dateTime('final_info_updated_at')->nullable();

            // Account control: admin can disable a student's login access
            $table->boolean('is_active')->default(true);

            $table->dateTime('last_login_at')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
