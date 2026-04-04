<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->integer('votes_count')->default(0)->after('replies_count');
            $table->boolean('is_accepted_answer')->default(false)->after('votes_count');
        });

        Schema::create('comment_votes', function (Blueprint $table) {
            $table->uuid('comment_id');
            $table->uuid('user_id');
            $table->tinyInteger('value'); // 1 = upvote, -1 = downvote
            $table->timestamp('created_at')->useCurrent();

            $table->primary(['comment_id', 'user_id']);
            $table->foreign('comment_id')->references('id')->on('comments')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index('value');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_votes');

        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn(['votes_count', 'is_accepted_answer']);
        });
    }
};
