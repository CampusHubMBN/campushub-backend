<?php
// database/migrations/xxxx_create_job_applications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Relations
            $table->foreignUuid('job_id')->constrained('jobs')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            
            // Application content
            $table->text('cover_letter')->nullable();
            $table->string('cv_url')->nullable(); // Can use user's CV or custom
            $table->json('additional_documents')->nullable(); // Array of file URLs
            
            // Status
            $table->enum('status', ['pending', 'reviewed', 'shortlisted', 'interview', 'rejected', 'accepted'])->default('pending');
            $table->text('notes')->nullable(); // For company/admin notes
            
            // Timeline
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('interview_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->unique(['job_id', 'user_id']); // One application per job per user
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};