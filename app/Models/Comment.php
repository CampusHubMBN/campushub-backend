<?php

// =====================================================================
// app/Models/Comment.php
// =====================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = ['post_id', 'author_id', 'parent_id', 'content', 'is_accepted_answer'];

    protected $casts = [
        'replies_count'      => 'integer',
        'votes_count'        => 'integer', // signed — can be negative (downvotes)
        'is_accepted_answer' => 'boolean',
    ];

    // ── Boot ─────────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::created(function (Comment $comment) {
            \App\Models\Post::where('id', $comment->post_id)
                            ->increment('comments_count');

            if ($comment->parent_id) {
                Comment::where('id', $comment->parent_id)
                    ->increment('replies_count');
            }
        });

        static::deleted(function (Comment $comment) {
            // MAX(0, count-1) — empêche le underflow UNSIGNED
            \App\Models\Post::where('id', $comment->post_id)
                            ->where('comments_count', '>', 0)
                            ->decrement('comments_count');

            if ($comment->parent_id) {
                Comment::where('id', $comment->parent_id)
                    ->where('replies_count', '>', 0)
                    ->decrement('replies_count');
            }
        });
    }

    // ── Relations ─────────────────────────────────────────────────────────────

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->latest();
    }

    public function votes()
    {
        return $this->hasMany(CommentVote::class);
    }

    public function getUserVote(?string $userId): ?int
    {
        if (!$userId) return null;
        return $this->votes()->where('user_id', $userId)->value('value');
    }
}