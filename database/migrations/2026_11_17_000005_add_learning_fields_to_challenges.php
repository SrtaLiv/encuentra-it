<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('challenges', function (Blueprint $table) {
            $table->string('ai_mode')->default('tutor')->after('level');
            $table->text('solution')->nullable()->after('deliverable');
        });

        Schema::table('challenge_user', function (Blueprint $table) {
            $table->json('reflection')->nullable()->after('evidence_url');
        });
    }

    public function down(): void
    {
        Schema::table('challenge_user', function (Blueprint $table) {
            $table->dropColumn('reflection');
        });

        Schema::table('challenges', function (Blueprint $table) {
            $table->dropColumn(['ai_mode', 'solution']);
        });
    }
};
