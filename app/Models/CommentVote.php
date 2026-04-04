<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentVote extends Model
{
    public $incrementing = false;
    public $timestamps   = false;

    protected $primaryKey = ['comment_id', 'user_id'];

    protected $fillable = ['comment_id', 'user_id', 'value'];

    protected $casts = [
        'value'      => 'integer',
        'created_at' => 'datetime',
    ];

    const UPDATED_AT = null;

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
