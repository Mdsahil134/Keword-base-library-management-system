<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class BookRequest extends Model
{
    protected $collection = 'book_requests';

    protected $fillable = [
        'user_id',
        'book_id',
        'status',
        'issue_date',
        'return_date',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'string',
            'book_id' => 'string',
            'issue_date' => 'datetime',
            'return_date' => 'datetime',
        ];
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
