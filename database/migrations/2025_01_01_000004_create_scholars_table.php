<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholars', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 200);
            $table->string('photo', 255)->nullable();
            $table->foreignId('program_id')->nullable()
                ->constrained('programs')->nullOnDelete();
            $table->string('school_or_institute', 200)->nullable();
            $table->string('district', 100)->nullable();
            $table->unsignedSmallInteger('year_enrolled')->nullable();
            $table->enum('status', ['Active', 'Graduated', 'Discontinued'])->default('Active');
            $table->text('story')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholars');
    }
};
