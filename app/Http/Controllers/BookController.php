<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookRequest;
use Illuminate\View\View;

class BookController extends Controller
{
    public function show(Book $book): View
    {
        $hasActiveRequest = false;

        if (auth()->check()) {
            $hasActiveRequest = BookRequest::query()
                ->where('user_id', auth()->id())
                ->where('book_id', $book->id)
                ->whereIn('status', ['pending', 'approved'])
                ->exists();
        }

        return view('library.book-show', [
            'book' => $book,
            'hasActiveRequest' => $hasActiveRequest,
        ]);
    }
}
