<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedTinyInteger('ai_rank_family_score')->nullable()->after('mrs_poh_updated_at');
            $table->unsignedTinyInteger('ai_rank_academic_score')->nullable()->after('ai_rank_family_score');
            $table->unsignedTinyInteger('ai_rank_economic_score')->nullable()->after('ai_rank_academic_score');
            $table->unsignedTinyInteger('ai_rank_essay_score')->nullable()->after('ai_rank_economic_score');
            $table->unsignedTinyInteger('ai_rank_composite_score')->nullable()->after('ai_rank_essay_score');
            $table->text('ai_rank_summary')->nullable()->after('ai_rank_composite_score');
            $table->dateTime('ai_ranked_at')->nullable()->after('ai_rank_summary');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'ai_rank_family_score',
                'ai_rank_academic_score',
                'ai_rank_economic_score',
                'ai_rank_essay_score',
                'ai_rank_composite_score',
                'ai_rank_summary',
                'ai_ranked_at',
            ]);
        });
    }
};