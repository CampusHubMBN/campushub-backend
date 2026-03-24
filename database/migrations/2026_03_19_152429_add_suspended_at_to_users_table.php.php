<?php
// =====================================================================
// 1. MIGRATION — ajouter suspended_at sur la table users
// database/migrations/xxxx_add_suspended_at_to_users_table.php
// =====================================================================
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('suspended_at')->nullable()->after('last_login_at');
        });
    }
 
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('suspended_at');
        });
    }
};