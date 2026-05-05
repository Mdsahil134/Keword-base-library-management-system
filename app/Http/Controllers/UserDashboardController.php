<?php

namespace App\Http\Controllers;

use App\Models\BookRequest;
use App\Services\BookRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserDashboardController extends Controller
{
    public function __construct(
        protected BookRecommendationService $recommendations
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $recentSearches = $user->searchHistories()
            ->latest()
            ->limit(5)
            ->get();

        $recommendedBooks = $this->recommendations->forUser($user, 8);
        $topicKeywords = $this->recommendations->topKeywordsFromHistory($user, 10);
        $bookRequests = BookRequest::query()
            ->with('book')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $pendingRequests = $bookRequests->where('status', 'pending');
        $issuedBooks = $bookRequests->where('status', 'approved');

        $chartLabels = [];
        $chartData = [];
        $dayExpr = DB::getDriverName() === 'sqlite'
            ? "strftime('%Y-%m-%d', created_at) as day"
            : 'DATE(created_at) as day';

        $byDay = $user->searchHistories()
            ->selectRaw($dayExpr)
            ->selectRaw('COUNT(*) as c')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        foreach ($byDay as $row) {
            $chartLabels[] = $row->day;
            $chartData[] = (int) $row->c;
        }

        if ($chartLabels === []) {
            for ($i = 6; $i >= 0; $i--) {
                $chartLabels[] = now()->subDays($i)->format('Y-m-d');
                $chartData[] = 0;
            }
        }

        return view('user.dashboard', [
            'recentSearches' => $recentSearches,
            'recommendedBooks' => $recommendedBooks,
            'topicKeywords' => $topicKeywords,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'pendingRequests' => $pendingRequests,
            'issuedBooks' => $issuedBooks,
        ]);
    }
}
