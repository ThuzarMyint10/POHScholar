<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_events', function (Blueprint $table) {
            $table->id();
            $table->string('title', 220);
            $table->enum('category', ['News', 'Event'])->default('News');
            $table->string('excerpt', 500)->nullable();
            $table->text('content')->nullable();
            $table->string('image', 255)->nullable();
            $table->date('event_date')->nullable();
            $table->boolean('is_published')->default(true);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_events');
    }
};
