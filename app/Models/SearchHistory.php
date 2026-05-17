<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class SearchHistory extends Model
{
    protected $collection = 'search_histories';

    protected $fillable = [
        'user_id',
        'query',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'string',
        ];
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
