<?php
// database/migrations/xxxx_create_jobs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->text('benefits')->nullable();
            
            // Job details
            $table->enum('type', ['internship', 'apprenticeship', 'cdd', 'cdi', 'freelance'])->default('internship');
            $table->enum('location_type', ['onsite', 'remote', 'hybrid'])->default('onsite');
            $table->string('location_city')->nullable();
            $table->string('location_country')->default('France');
            
            // Salary
            $table->integer('salary_min')->nullable();
            $table->integer('salary_max')->nullable();
            $table->enum('salary_period', ['hourly', 'monthly', 'yearly'])->default('yearly');
            $table->string('salary_currency')->default('EUR');
            
            // Duration (for internships/CDD)
            $table->integer('duration_months')->nullable();
            $table->date('start_date')->nullable();
            
            // Application
            $table->string('application_url')->nullable(); // For external jobs
            $table->string('application_email')->nullable();
            $table->date('application_deadline')->nullable();
            
            // Source (internal vs external)
            $table->enum('source_type', ['internal', 'external'])->default('internal');
            $table->string('company_name')->nullable(); // For external jobs
            $table->string('external_url')->nullable(); // Link to external job posting
            
            // Relations
            $table->foreignUuid('company_id')->nullable()->constrained('companies')->onDelete('cascade'); // Only for internal
            $table->foreignUuid('posted_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Stats
            $table->integer('views_count')->default(0);
            $table->integer('applications_count')->default(0);
            
            // Status
            $table->enum('status', ['draft', 'published', 'closed', 'filled'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('status');
            $table->index('source_type');
            $table->index('type');
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};