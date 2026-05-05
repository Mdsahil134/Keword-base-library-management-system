<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BookRequestController extends Controller
{
    public function index(): View
    {
        return view('admin.book-requests.index', [
            'pendingRequests' => BookRequest::query()
                ->with(['book', 'user'])
                ->where('status', 'pending')
                ->latest()
                ->paginate(15),
        ]);
    }

    public function approve(BookRequest $bookRequest): RedirectResponse
    {
        if ($bookRequest->status !== 'pending') {
            return back()->withErrors(['book' => 'Only pending requests can be approved.']);
        }

        $book = $bookRequest->book;

        if (! $book || $book->available_copies <= 0) {
            return back()->withErrors(['book' => 'Book is out of stock, cannot approve this request.']);
        }

        $bookRequest->update([
            'status' => 'approved',
            'issue_date' => now(),
            'return_date' => now()->addDays(7),
        ]);

        $book->decrement('available_copies');

        return back()->with('status', 'Request approved.');
    }

    public function reject(BookRequest $bookRequest): RedirectResponse
    {
        if ($bookRequest->status !== 'pending') {
            return back()->withErrors(['book' => 'Only pending requests can be rejected.']);
        }

        $bookRequest->update([
            'status' => 'rejected',
        ]);

        return back()->with('status', 'Request rejected.');
    }
}
