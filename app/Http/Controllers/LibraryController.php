<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\SearchHistory;
use App\Services\BookSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LibraryController extends Controller
{
    public function __construct(
        protected BookSearchService $searchService
    ) {}

    public function home(): View
    {
        return view('library.home');
    }

    public function search(Request $request): View
    {
        $query = (string) $request->input('q', '');
        $filters = [
            'author' => $request->input('author'),
            'category' => $request->input('category'),
            'year' => $request->input('year'),
        ];

        if (trim($query) !== '') {
            $history = collect(session('search_history', []))
                ->prepend($query)
                ->unique()
                ->take(8)
                ->values()
                ->all();
            session(['search_history' => $history]);

            if ($request->user()) {
                SearchHistory::query()->create([
                    'user_id' => $request->user()->id,
                    'query' => mb_substr($query, 0, 500),
                ]);
                $request->user()->increment('total_searches');
            }
        }

        $result = $this->searchService->search($query, $filters, 8);

        $authors = Book::query()
            ->distinct()
            ->orderBy('author')
            ->pluck('author');

        $years = Book::query()
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return view('library.search', [
            'query' => $query,
            'keywords' => $result['keywords'],
            'books' => $result['paginator'],
            'categories' => Category::orderBy('name')->pluck('name'),
            'authors' => $authors,
            'years' => $years,
            'filters' => $filters,
            'searchHistory' => session('search_history', []),
        ]);
    }

    public function suggest(Request $request): JsonResponse
    {
        $q = trim((string) $request->input('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json(['suggestions' => []]);
        }

        $pattern = $this->searchService->likePattern($q);

        $titles = Book::query()
            ->where('title', 'like', $pattern)
            ->orderBy('title')
            ->limit(10)
            ->pluck('title');

        return response()->json(['suggestions' => $titles]);
    }
}
