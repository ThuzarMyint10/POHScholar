<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Once accepted, a scholar must submit an academic result update every
    // 6 months. Each row is one such period.
    public function up(): void
    {
        Schema::create('academic_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->unsignedInteger('period_number');
            $table->date('due_date');
            $table->dateTime('submitted_at')->nullable();
            $table->text('result_summary')->nullable();
            $table->string('result_file', 255)->nullable();
            $table->dateTime('reminder_sent_at')->nullable();
            $table->unsignedInteger('reminder_count')->default(0);
            $table->dateTime('created_at')->useCurrent();

            $table->unique(['student_id', 'period_number'], 'uq_academic_results_student_period');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_results');
    }
};
