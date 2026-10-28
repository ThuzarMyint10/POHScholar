<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // A tiny append-only table whose sole purpose is to hand out a safe,
    // unique, sequential number (via auto-increment) when an application
    // is submitted - used to build numbers like POH-2026-000123. See
    // App\Support\ApplicationNumberGenerator.
    public function up(): void
    {
        Schema::create('application_number_seq', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->dateTime('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_number_seq');
    }
};
