<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookRequestController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'book_id' => ['required', 'string'],
        ]);

        $book = Book::query()->findOrFail($validated['book_id']);

        if ($book->available_copies <= 0) {
            return back()->withErrors([
                'book' => 'This book is currently out of stock.',
            ]);
        }

        $alreadyRequested = BookRequest::query()
            ->where('user_id', $request->user()->id)
            ->where('book_id', $book->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($alreadyRequested) {
            return back()->withErrors([
                'book' => 'You already have a pending or approved request for this book.',
            ]);
        }

        BookRequest::query()->create([
            'user_id' => $request->user()->id,
            'book_id' => $book->id,
            'status' => 'pending',
        ]);

        return back()->with('status', 'Book issue request submitted.');
    }

    public function return(BookRequest $bookRequest, Request $request): RedirectResponse
    {
        if ((int) $bookRequest->user_id !== (int) $request->user()->id) {
            abort(403);
        }

        if ($bookRequest->status !== 'approved') {
            return back()->withErrors([
                'book' => 'Only approved issued books can be returned.',
            ]);
        }

        $bookRequest->update([
            'status' => 'returned',
            'return_date' => now(),
        ]);

        $bookRequest->book()->increment('available_copies');

        return back()->with('status', 'Book returned successfully.');
    }
}
