<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use MongoDB\Laravel\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $collection = 'users';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'total_searches' => 'integer',
            'time_spent' => 'integer',
            'is_admin' => 'boolean',
        ];
    }

    public function searchHistories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SearchHistory::class);
    }

    public function bookRequests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BookRequest::class);
    }
}
