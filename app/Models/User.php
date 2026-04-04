<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'last_login_at',
        'suspended_at',  
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'suspended_at' => 'datetime',
        ];
    }

    /**
     * Relation one-to-one avec UserInfo
     */
    public function info()
    {
        return $this->hasOne(UserInfo::class);
    }

    /**
     * Créer automatiquement UserInfo à la création du User
     */
    protected static function booted()
    {
        static::created(function ($user) {
            $user->info()->create([
                'profile_completion' => 20, // Base: nom + email
            ]);
        });
    }
}