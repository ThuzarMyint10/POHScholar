<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Not wired up to any route yet (the original app had no
    // forgot-password flow for either guard), but Laravel's auth config
    // expects this table to exist for both the admin_users and students
    // password-broker providers, so it's ready if you add "Forgot
    // Password" later using Laravel's built-in Password facade.
    public function up(): void
    {
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};
