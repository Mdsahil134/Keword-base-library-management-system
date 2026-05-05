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

    {{-- ─── Recommended Books (Horizontal Carousel) ─── --}}
    <section class="mb-10">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-slate-900">Recommended books</h2>
            <span class="text-sm text-slate-500">New releases</span>
        </div>
        <div class="scrollbar-hide flex gap-5 overflow-x-auto pb-4">
            @forelse($featuredBooks as $book)
                <div class="w-44 shrink-0">
                    <x-book-card :book="$book" :showActions="false" />
                </div>
            @empty
                <div class="glass w-full p-8 text-center text-sm text-slate-500">No books found.</div>
            @endforelse
        </div>
    </section>

    {{-- ─── Explore Books (Main Grid) ─── --}}
    <section class="mb-8">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-slate-900">Explore books</h2>
            <a href="{{ route('search') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition">Browse all →</a>
        </div>
        <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
            @forelse($exploreBooks as $book)
                <x-book-card :book="$book" />
            @empty
                <div class="col-span-full rounded-2xl border border-slate-200 bg-white p-16 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100">
                        <svg class="h-8 w-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900">No books available</h3>
                    <p class="mt-2 text-sm text-slate-500">Check back later for new additions to the library.</p>
                </div>
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
