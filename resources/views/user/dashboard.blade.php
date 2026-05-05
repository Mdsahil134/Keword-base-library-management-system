<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-slate-900">
            {{ __('Your dashboard') }}
        </h2>
    </x-slot>

    <div class="space-y-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Welcome back, {{ auth()->user()->name }}</h1>
            <p class="mt-1 text-slate-600">Your search activity and personalized picks.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="stat-card glass p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total searches</p>
                <p class="mt-2 text-3xl font-bold text-indigo-600">{{ auth()->user()->total_searches }}</p>
            </div>
            <div class="stat-card glass p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Time on platform</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ gmdate('H:i:s', auth()->user()->time_spent) }}</p>
                <p class="mt-1 text-xs text-slate-500">hh:mm:ss (accumulated)</p>
            </div>
            <div class="stat-card glass p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Issued books</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ $issuedBooks->count() }}</p>
            </div>
            <div class="stat-card glass p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pending requests</p>
                <p class="mt-2 text-3xl font-bold text-amber-600">{{ $pendingRequests->count() }}</p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="glass p-6">
                <h3 class="mb-4 text-lg font-semibold text-slate-900">Recent searches</h3>
                @if($recentSearches->isEmpty())
                    <p class="text-sm text-slate-500">No searches yet. Try the catalogue search bar.</p>
                @else
                    <ul class="space-y-3">
                        @foreach($recentSearches as $h)
                            <li class="flex items-center justify-between gap-2 border-b border-slate-100 pb-3 text-sm last:border-0">
                                <a href="{{ route('search', ['q' => $h->query]) }}" class="font-medium text-indigo-600 hover:text-indigo-700">{{ Str::limit($h->query, 48) }}</a>
                                <span class="shrink-0 text-xs text-slate-500">{{ $h->created_at->diffForHumans() }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="glass p-6">
                <h3 class="mb-4 text-lg font-semibold text-slate-900">Most searched topics</h3>
                @if(count($topicKeywords) === 0)
                    <p class="text-sm text-slate-500">Search more to see keyword trends.</p>
                @else
                    <div class="flex flex-wrap gap-2">
                        @foreach($topicKeywords as $word => $count)
                            <a href="{{ route('search', ['q' => $word]) }}" class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-sm text-slate-700 hover:border-indigo-300 hover:text-indigo-700">
                                {{ $word }} <span class="text-slate-500">({{ $count }})</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="glass p-6">
            <h3 class="mb-4 text-lg font-semibold text-slate-900">Searches per day (last 7 days)</h3>
            <div class="relative h-56 w-full max-w-2xl">
                <canvas id="user-search-chart"></canvas>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="glass p-6">
                <h3 class="mb-4 text-lg font-semibold text-slate-900">My Books</h3>
                <p class="text-sm text-slate-500">Track your active requests and due books.</p>
                <div class="mt-4 space-y-2 text-sm">
                    <p class="text-slate-700">Pending: <span class="rounded-full border border-amber-200 bg-amber-50 px-2 py-0.5 text-amber-700">{{ $pendingRequests->count() }}</span></p>
                    <p class="text-slate-700">Issued: <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-emerald-700">{{ $issuedBooks->count() }}</span></p>
                </div>
            </div>
            <div class="glass p-6 lg:col-span-2">
                <h3 class="mb-4 text-lg font-semibold text-slate-900">Pending Requests</h3>
                @if($pendingRequests->isEmpty())
                    <p class="text-sm text-slate-500">No pending requests right now.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="table-clean w-full min-w-[520px] text-left text-sm">
                            <thead class="text-xs uppercase tracking-wider">
                                <tr>
                                    <th class="pb-2">Book</th>
                                    <th class="pb-2">Requested</th>
                                    <th class="pb-2">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($pendingRequests as $requestItem)
                                    <tr>
                                        <td class="py-3 text-slate-700">{{ $requestItem->book?->title }}</td>
                                        <td class="py-3 text-slate-500">{{ $requestItem->created_at->diffForHumans() }}</td>
                                        <td class="py-3"><span class="rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs text-amber-700">Pending</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="glass p-6">
            <h3 class="mb-4 text-lg font-semibold text-slate-900">Issued Books</h3>
            @if($issuedBooks->isEmpty())
                <p class="text-sm text-slate-500">No issued books yet.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="table-clean w-full min-w-[680px] text-left text-sm">
                        <thead class="text-xs uppercase tracking-wider">
                            <tr>
                                <th class="pb-2">Book</th>
                                <th class="pb-2">Issue Date</th>
                                <th class="pb-2">Due Date</th>
                                <th class="pb-2">Status</th>
                                <th class="pb-2 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($issuedBooks as $requestItem)
                                <tr>
                                    <td class="py-3 text-slate-700">{{ $requestItem->book?->title }}</td>
                                    <td class="py-3 text-slate-500">{{ optional($requestItem->issue_date)->format('d M Y') ?? '-' }}</td>
                                    <td class="py-3 text-slate-500">{{ optional($requestItem->return_date)->format('d M Y') ?? '-' }}</td>
                                    <td class="py-3"><span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs text-emerald-700">Approved</span></td>
                                    <td class="py-3 text-end">
                                        <form action="{{ route('book-requests.return', $requestItem) }}" method="post">
                                            @csrf
                                            <button type="submit" class="rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs text-indigo-700 hover:bg-indigo-100">Return</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div>
            <h3 class="mb-4 text-lg font-semibold text-slate-900">Recommended for you</h3>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($recommendedBooks as $book)
                    <a href="{{ route('books.show', $book) }}" class="result-card glass book-grid-card block p-3">
                        @if($book->cover_image)
                            <img src="{{ asset('storage/'.$book->cover_image) }}" alt="{{ $book->title }} cover" class="h-44 w-full rounded-lg object-cover">
                        @else
                            <div class="h-44 w-full rounded-lg bg-slate-100"></div>
                        @endif
                        <div>
                            <p class="mt-3 font-semibold text-slate-900">{{ Str::limit($book->title, 56) }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $book->author }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $book->category }}</p>
                        </div>
                    </a>
                @empty
                    <p class="text-sm text-slate-500">No recommendations yet — start searching.</p>
                @endforelse
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    window.addEventListener('load', function () {
        if (typeof Chart === 'undefined') return;
        var canvas = document.getElementById('user-search-chart');
        if (!canvas) return;
        var labels = @json($chartLabels);
        var data = @json($chartData);
        new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Searches',
                    data: data,
                    backgroundColor: 'rgba(99, 102, 241, 0.35)',
                    borderColor: 'rgba(99, 102, 241, 0.9)',
                    borderWidth: 1,
                    borderRadius: 8,
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: '#64748b' }, grid: { color: 'rgba(148,163,184,0.25)' } },
                    y: { beginAtZero: true, ticks: { color: '#64748b', stepSize: 1 }, grid: { color: 'rgba(148,163,184,0.25)' } },
                },
            },
        });
    });
    </script>
    @endpush
</x-app-layout>
