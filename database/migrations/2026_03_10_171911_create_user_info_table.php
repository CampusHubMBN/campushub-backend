<?php
// database/migrations/xxxx_create_user_info_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_info', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique();
            
            // Profile
            $table->string('avatar_url')->nullable();
            $table->text('bio')->nullable();
            $table->string('phone')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('github_url')->nullable();
            $table->string('website_url')->nullable();
            
            // Professional
            $table->string('cv_url')->nullable();
            $table->json('skills')->nullable();
            $table->json('languages')->nullable();
            
            // Academic (student/alumni)
            $table->string('program')->nullable();
            $table->integer('year')->nullable();
            $table->integer('graduation_year')->nullable();
            $table->string('specialization')->nullable();
            $table->string('campus')->nullable();
            
            // Company reference
            $table->uuid('company_id')->nullable();
            
            // Gamification
            $table->integer('reputation_points')->default(0);
            $table->enum('level', ['beginner', 'active_member', 'contributor', 'expert', 'vip'])
                  ->default('beginner');
            $table->integer('profile_completion')->default(0);
            
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->foreign('company_id')
                  ->references('id')
                  ->on('companies')
                  ->onDelete('set null');
            
            // Indexes
            $table->index('user_id');
            $table->index('company_id');
            $table->index('program');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_info');
    }
};