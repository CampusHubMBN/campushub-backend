<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL enums must be fully redeclared to add a value
        DB::statement("ALTER TABLE jobs MODIFY type ENUM('internship','apprenticeship','cdd','cdi','freelance','student_job') NOT NULL DEFAULT 'internship'");

        Schema::table('jobs', function (Blueprint $table) {
            $table->unsignedTinyInteger('hours_per_week')->nullable()->after('duration_months');
        });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('hours_per_week');
        });

        DB::statement("ALTER TABLE jobs MODIFY type ENUM('internship','apprenticeship','cdd','cdi','freelance') NOT NULL DEFAULT 'internship'");
    }
};
