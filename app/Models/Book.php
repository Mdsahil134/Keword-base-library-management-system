<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title',
        'author',
        'description',
        'category',
        'year',
        'keywords',
        'cover_image',
        'pdf_file',
        'total_copies',
        'available_copies',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'total_copies' => 'integer',
            'available_copies' => 'integer',
        ];
    }

    /** @return list<string> */
    public function keywordList(): array
    {
        if ($this->keywords === null || trim($this->keywords) === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $this->keywords))));
    }

    public function bookRequests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BookRequest::class);
    }
}
