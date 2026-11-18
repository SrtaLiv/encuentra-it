<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('icon')->default('compass');
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('careers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('summary');
            $table->text('description');
            $table->string('coding_level');
            $table->json('daily_tasks');
            $table->json('skills');
            $table->json('job_titles');
            $table->text('job_outlook')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('roadmap_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('career_id')->constrained()->cascadeOnDelete();
            $table->string('level');
            $table->unsignedSmallInteger('position')->default(0);
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('roadmap_step_user', function (Blueprint $table) {
            $table->foreignId('roadmap_step_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['roadmap_step_id', 'user_id']);
        });

        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('url', 500);
            $table->text('description')->nullable();
            $table->string('type');
            $table->string('level');
            $table->string('language', 2);
            $table->boolean('is_free')->default(true);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('career_resource', function (Blueprint $table) {
            $table->foreignId('career_id')->constrained()->cascadeOnDelete();
            $table->foreignId('resource_id')->constrained()->cascadeOnDelete();
            $table->primary(['career_id', 'resource_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_resource');
        Schema::dropIfExists('resources');
        Schema::dropIfExists('roadmap_step_user');
        Schema::dropIfExists('roadmap_steps');
        Schema::dropIfExists('careers');
        Schema::dropIfExists('areas');
    }
};
