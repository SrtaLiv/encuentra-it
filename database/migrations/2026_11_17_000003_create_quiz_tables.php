<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->string('text');
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('quiz_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_question_id')->constrained()->cascadeOnDelete();
            $table->string('text');
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('career_quiz_option', function (Blueprint $table) {
            $table->foreignId('career_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quiz_option_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('weight')->default(1);
            $table->primary(['career_id', 'quiz_option_id']);
        });

        Schema::create('quiz_results', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->json('answers');
            $table->json('scores');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_results');
        Schema::dropIfExists('career_quiz_option');
        Schema::dropIfExists('quiz_options');
        Schema::dropIfExists('quiz_questions');
    }
};
