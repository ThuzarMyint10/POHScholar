<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // "applications" holds the short public quick-apply form submissions -
    // distinct from the full student portal application in `students`.
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->nullable()
                ->constrained('programs')->nullOnDelete();
            $table->string('full_name', 200);
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->string('nic_or_birth_cert', 60)->nullable();
            $table->string('address', 400)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('school_or_institute', 200)->nullable();
            $table->string('grade_or_year', 60)->nullable();
            $table->string('guardian_name', 200)->nullable();
            $table->string('guardian_occupation', 150)->nullable();
            $table->enum('household_income_band', [
                'Below Rs.25,000', 'Rs.25,000 - Rs.50,000', 'Rs.50,000 - Rs.75,000', 'Above Rs.75,000', 'Prefer not to say',
            ])->nullable();
            $table->text('reason_for_applying')->nullable();
            $table->enum('status', ['Pending', 'Under Review', 'Approved', 'Rejected'])->default('Pending');
            $table->text('admin_notes')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
