@extends('layouts.app')

@section('title', 'Dashboard')

@php
    use App\Models\Book;
    $featuredBooks = Book::query()->orderByDesc('updated_at')->limit(10)->get();
    $exploreBooks = Book::query()->orderByDesc('updated_at')->paginate(12);
@endphp

@section('content')
    <div class="mb-6">
        <h1 class="mb-2 text-3xl font-bold tracking-tight text-slate-900 md:text-4xl">Library intelligence dashboard</h1>
        <p class="max-w-2xl text-slate-600">Explore books in a clean grid-based interface with quick access to search and details.</p>
    </div>

    <div class="mx-auto mb-10 max-w-3xl">
        <form action="{{ route('search') }}" method="get" id="search-form" class="relative">
            <label for="q" class="sr-only">Search</label>
            <div class="search-glow flex items-center gap-2 rounded-full border border-slate-200 bg-white py-2 pl-6 pr-2">
                <svg class="h-6 w-6 shrink-0 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <input type="search" name="q" id="q" autocomplete="off" autofocus
                       class="min-w-0 flex-1 border-0 bg-transparent py-3 text-base text-slate-800 placeholder-slate-400 outline-none focus:ring-0"
                       placeholder="Search by keywords — e.g. machine learning, fiction, algorithms…" value="{{ request('q') }}"/>
                <span id="home-search-spinner" class="hidden shrink-0"><span class="spinner"></span></span>
                <button type="submit" class="btn-glow shrink-0 rounded-full bg-slate-900 px-8 py-3 text-sm font-semibold text-white">
                    Search
                </button>
            </div>
            <div id="suggestions" class="suggest-box absolute left-0 right-0 top-full mt-2 hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg"></div>
        </form>
    </div>

    <section class="mb-10">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-slate-900">Recommended books</h2>
            <span class="text-sm text-slate-500">New releases</span>
        </div>
        <div class="flex gap-4 overflow-x-auto pb-3">
            @forelse($featuredBooks as $book)
                <a href="{{ route('books.show', $book) }}" class="glass result-card book-grid-card flex min-w-[220px] items-center gap-3 p-4">
                    @if($book->cover_image)
                        <img src="{{ asset('storage/'.$book->cover_image) }}" alt="{{ $book->title }} cover" class="h-24 w-16 rounded-lg object-cover">
                    @else
                        <div class="flex h-24 w-16 items-center justify-center rounded-lg border border-dashed border-slate-300 bg-slate-50 text-[10px] text-slate-400">No cover</div>
                    @endif
                    <div class="min-w-0">
                        <p class="truncate font-semibold text-slate-900">{{ $book->title }}</p>
                        <p class="truncate text-sm text-slate-500">{{ $book->author }}</p>
                        <p class="mt-2 text-xs text-indigo-600">Rating: {{ number_format(min(5, max(3.5, 3.5 + (($book->available_copies ?? 0) / max(1, ($book->total_copies ?? 1))) * 1.5)), 1) }}/5</p>
                    </div>
                </a>
            @empty
                <div class="glass w-full p-8 text-center text-sm text-slate-500">No books found.</div>
            @endforelse
        </div>
    </section>

    <section class="mb-8">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-slate-900">Explore books</h2>
            <a href="{{ route('search') }}" class="text-sm font-medium text-slate-700 hover:text-slate-900">Open search</a>
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5">
            @forelse($exploreBooks as $book)
                <a href="{{ route('books.show', $book) }}" class="glass result-card book-grid-card block p-3">
                    @if($book->cover_image)
                        <img src="{{ asset('storage/'.$book->cover_image) }}" alt="{{ $book->title }} cover" class="h-52 w-full rounded-lg object-cover">
                    @else
                        <div class="flex h-52 w-full items-center justify-center rounded-lg border border-dashed border-slate-300 bg-slate-50 text-xs text-slate-400">No cover</div>
                    @endif
                    <p class="mt-3 line-clamp-2 text-sm font-semibold text-slate-900">{{ $book->title }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ Str::limit($book->author, 26) }}</p>
                </a>
            @empty
                <div class="glass col-span-full p-10 text-center text-sm text-slate-500">No books available.</div>
            @endforelse
        </div>
        <div class="mt-6">{{ $exploreBooks->links('vendor.pagination.dark-glass') }}</div>
    </section>
@endsection

@push('scripts')
<script>
(function () {
    const suggestUrl = @json(route('suggest'));
    const input = document.getElementById('q');
    const box = document.getElementById('suggestions');
    const form = document.getElementById('search-form');
    const spin = document.getElementById('home-search-spinner');
    let t;
    if (form && spin) {
        form.addEventListener('submit', function () {
            const q = input && input.value.trim();
            if (q) spin.classList.remove('hidden');
        });
    }
    if (!input || !box) return;
    input.addEventListener('input', function () {
        clearTimeout(t);
        const q = input.value.trim();
        if (q.length < 2) {
            box.classList.add('hidden');
            box.innerHTML = '';
            return;
        }
        t = setTimeout(function () {
            fetch(suggestUrl + '?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' } })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    box.innerHTML = '';
                    if (!data.suggestions || !data.suggestions.length) {
                        box.classList.add('hidden');
                        return;
                    }
                    data.suggestions.forEach(function (title) {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'suggest-item block w-full border-b border-slate-100 px-4 py-3 text-left text-sm text-slate-700 last:border-0 hover:bg-slate-50';
                        btn.textContent = title;
                        btn.addEventListener('click', function () {
                            input.value = title;
                            box.classList.add('hidden');
                            form.submit();
                        });
                        box.appendChild(btn);
                    });
                    box.classList.remove('hidden');
                });
        }, 250);
    });
    document.addEventListener('click', function (e) {
        if (!box.contains(e.target) && e.target !== input) box.classList.add('hidden');
    });
})();
window.addEventListener('load', function () {});
</script>
@endpush
