<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('career_id')->constrained()->cascadeOnDelete();
            $table->string('level');
            $table->unsignedSmallInteger('position')->default(0);
            $table->string('title');
            $table->text('description');
            $table->string('deliverable', 500);
            $table->timestamps();
        });

        Schema::create('challenge_user', function (Blueprint $table) {
            $table->foreignId('challenge_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('evidence_url', 500)->nullable();
            $table->timestamps();
            $table->primary(['challenge_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenge_user');
        Schema::dropIfExists('challenges');
    }
};
