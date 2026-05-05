<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookRequest;
use App\Models\Category;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'bookCount' => Book::query()->count(),
            'categoryCount' => Category::query()->count(),
            'pendingRequestCount' => BookRequest::query()->where('status', 'pending')->count(),
        ]);
    }
}
