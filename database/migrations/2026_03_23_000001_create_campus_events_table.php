<?php
// database/migrations/2026_03_23_000001_create_campus_events_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campus_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description');
            $table->string('location');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->unsignedInteger('capacity')->nullable(); // null = unlimited
            $table->string('cover_image')->nullable();
            $table->string('event_type')->default('general'); // general, workshop, conference, networking, sports
            $table->json('target_roles')->nullable(); // null = all, or ["student","alumni"]
            $table->foreignUuid('organizer_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_events');
    }
};
