<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class BookSearchService
{
    /** @return list<string> */
    public function keywordsFromQuery(string $query): array
    {
        $normalized = trim(preg_replace('/\s+/u', ' ', $query) ?? '');
        if ($normalized === '') {
            return [];
        }

        return array_values(array_filter(explode(' ', $normalized), fn (string $k) => $k !== ''));
    }

    public function likePattern(string $keyword): string
    {
        $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $keyword);

        return '%'.$escaped.'%';
    }

    /**
     * @param  array<string, mixed>  $filters  author, category, year
     * @return array{paginator: LengthAwarePaginator<int, Book>, keywords: list<string>}
     */
    public function search(string $query, array $filters, int $perPage = 10): array
    {
        $keywords = $this->keywordsFromQuery($query);

        $q = Book::query();

        $author = isset($filters['author']) ? trim((string) $filters['author']) : '';
        if ($author !== '') {
            $q->where('author', 'like', $this->likePattern($author));
        }

        $category = isset($filters['category']) ? trim((string) $filters['category']) : '';
        if ($category !== '') {
            $q->where('category', $category);
        }

        if (! empty($filters['year']) && is_numeric($filters['year'])) {
            $q->where('year', (int) $filters['year']);
        }

        foreach ($keywords as $kw) {
            $pattern = $this->likePattern($kw);
            $q->where(function ($sub) use ($pattern) {
                $sub->where('title', 'like', $pattern)
                    ->orWhere('description', 'like', $pattern)
                    ->orWhere('keywords', 'like', $pattern);
            });
        }

        /** @var Collection<int, Book> $books */
        $books = $q->get()->map(function (Book $book) use ($keywords) {
            $book->relevance_score = $this->scoreBook($book, $keywords);

            return $book;
        });

        if ($keywords === []) {
            $books = $books->sort(fn (Book $a, Book $b) => strnatcasecmp($a->title, $b->title))->values();
        } else {
            $books = $books->sort(function (Book $a, Book $b) {
                $s = ($b->relevance_score ?? 0) <=> ($a->relevance_score ?? 0);
                if ($s !== 0) {
                    return $s;
                }

                return strnatcasecmp($a->title, $b->title);
            })->values();
        }

        $page = max(1, (int) Paginator::resolveCurrentPage());
        $total = $books->count();
        $items = $books->forPage($page, $perPage)->values();

        $paginator = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'query' => request()->query(),
            ]
        );

        return [
            'paginator' => $paginator,
            'keywords' => $keywords,
        ];
    }

    /**
     * @param  list<string>  $keywords
     */
    public function scoreBook(Book $book, array $keywords): int
    {
        if ($keywords === []) {
            return 0;
        }

        $score = 0;
        $title = mb_strtolower($book->title ?? '');
        $desc = mb_strtolower($book->description ?? '');
        $keywordField = mb_strtolower($book->keywords ?? '');

        foreach ($keywords as $kw) {
            $k = mb_strtolower($kw);
            if ($k === '') {
                continue;
            }
            if (str_contains($title, $k)) {
                $score += 3;
            }
            if (str_contains($keywordField, $k)) {
                $score += 2;
            }
            if (str_contains($desc, $k)) {
                $score += 1;
            }
        }

        return $score;
    }
}
