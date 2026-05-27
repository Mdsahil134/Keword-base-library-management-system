<?php

namespace App\Services;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class BookRecommendationService
{
    public function __construct(
        protected BookSearchService $searchService
    ) {}

    /**
     * @return Collection<int, Book>
     */
    public function forUser(User $user, int $limit = 8): Collection
    {
        $queries = $user->searchHistories()
            ->latest()
            ->limit(40)
            ->pluck('query');

        $termWeights = [];
        foreach ($queries as $q) {
            $keywords = $this->searchService->keywordsFromQuery((string) $q);
            foreach ($keywords as $kw) {
                $k = mb_strtolower($kw);
                if (mb_strlen($k) < 2) {
                    continue;
                }
                $termWeights[$k] = ($termWeights[$k] ?? 0) + 1;
            }
        }

        if ($termWeights === []) {
            // MongoDB driver does not support inRandomOrder().
            return Book::query()
                ->limit(max($limit * 3, 30))
                ->get()
                ->shuffle()
                ->take($limit)
                ->values();
        }

        arsort($termWeights);
        $topTerms = array_slice(array_keys($termWeights), 0, 8);

        $books = Book::query()
            ->where(function ($q) use ($topTerms) {
                foreach ($topTerms as $term) {
                    $pattern = $this->searchService->likePattern($term);
                    $q->orWhere(function ($sub) use ($pattern) {
                        $sub->where('title', 'like', $pattern)
                            ->orWhere('description', 'like', $pattern)
                            ->orWhere('keywords', 'like', $pattern);
                    });
                }
            })
            ->limit($limit * 2)
            ->get();

        return $books->unique('id')->take($limit)->values();
    }

    /**
     * @return array<string, int>
     */
    public function topKeywordsFromHistory(User $user, int $maxTerms = 8): array
    {
        $queries = $user->searchHistories()
            ->latest()
            ->limit(50)
            ->pluck('query');

        $counts = [];
        foreach ($queries as $q) {
            foreach ($this->searchService->keywordsFromQuery((string) $q) as $kw) {
                $k = mb_strtolower(trim($kw));
                if (mb_strlen($k) < 2) {
                    continue;
                }
                $counts[$k] = ($counts[$k] ?? 0) + 1;
            }
        }

        arsort($counts);

        return array_slice($counts, 0, $maxTerms, true);
    }
}
