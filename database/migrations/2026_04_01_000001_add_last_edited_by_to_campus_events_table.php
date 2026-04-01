<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campus_events', function (Blueprint $table) {
            $table->uuid('last_edited_by_id')->nullable()->after('organizer_id');
            $table->foreign('last_edited_by_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('campus_events', function (Blueprint $table) {
            $table->dropForeign(['last_edited_by_id']);
            $table->dropColumn('last_edited_by_id');
        });
    }
};
