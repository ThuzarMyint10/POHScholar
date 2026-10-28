<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE students MODIFY status ENUM('Draft', 'Submitted', 'Under Review', 'Approved', 'Graduated', 'Rejected') NOT NULL DEFAULT 'Draft'");

        Schema::table('scholars', function (Blueprint $table): void {
            $table->foreignId('student_id')->nullable()->unique()->after('id')
                ->constrained('students')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('scholars', function (Blueprint $table): void {
            $table->dropForeign(['student_id']);
            $table->dropUnique(['student_id']);
            $table->dropColumn('student_id');
        });

        DB::statement("ALTER TABLE students MODIFY status ENUM('Draft', 'Submitted', 'Under Review', 'Approved', 'Rejected') NOT NULL DEFAULT 'Draft'");
    }
};