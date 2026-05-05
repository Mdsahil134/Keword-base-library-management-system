@extends('layouts.app')

@section('title', 'Search results')

@section('stat_four_label', 'Matching results')
@section('stat_four_value', $books->total())
@section('stat_four_hint', 'After filters · ranked')

@section('content')
    <div id="search-loading" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/30 backdrop-blur-sm">
        <div class="glass flex flex-col items-center gap-4 px-10 py-8">
            <span class="spinner h-10 w-10 border-2"></span>
            <p class="text-sm font-medium text-slate-600">Searching catalogue…</p>
        </div>
    </div>

    <div class="min-w-0 space-y-6">
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <form method="get" action="{{ route('search') }}" class="js-trigger-search-loading grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-6" id="results-search-form">
                <div class="search-glow flex w-full items-center gap-2 rounded-full border border-slate-200 bg-white py-1 pl-4 pr-1 md:col-span-2 lg:col-span-3">
                    <input type="search" name="q" value="{{ $query }}" placeholder="Refine keywords…"
                           class="min-w-0 flex-1 border-0 bg-transparent py-2.5 text-sm text-slate-800 outline-none focus:ring-0"/>
                    <button type="submit" class="btn-glow rounded-full bg-slate-900 px-5 py-2 text-sm font-semibold text-white">Search</button>
                </div>
                <select name="author" id="author" class="select-dark">
                    <option value="">Any author</option>
                    @foreach($authors as $a)
                        <option value="{{ $a }}" @selected(($filters['author'] ?? '') === $a)>{{ $a }}</option>
                    @endforeach
                </select>
                <select name="category" id="category" class="select-dark">
                    <option value="">Any category</option>
                    @foreach($categories as $c)
                        <option value="{{ $c }}" @selected(($filters['category'] ?? '') === $c)>{{ $c }}</option>
                    @endforeach
                </select>
                <select name="year" id="year" class="select-dark">
                    <option value="">Any year</option>
                    @foreach($years as $y)
                        <option value="{{ $y }}" @selected((string)($filters['year'] ?? '') === (string)$y)>{{ $y }}</option>
                    @endforeach
                </select>
                <a href="{{ route('search', ['q' => $query]) }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-4 py-2 text-sm text-black hover:bg-slate-50">Clear</a>
            </form>
        </div>

        @if(trim($query) === '')
            <p class="rounded-2xl border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-700">Showing the full catalogue sorted A–Z. Enter keywords to activate relevance ranking.</p>
        @else
            <p class="text-slate-600">{{ $books->total() }} result(s) for <span class="font-semibold text-slate-900">{{ $query }}</span></p>
        @endif

        <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
            @forelse($books as $book)
                <x-book-card :book="$book" />
            @empty
                <div class="col-span-full rounded-2xl border border-slate-200 bg-white p-16 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100">
                        <svg class="h-8 w-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900">No results found</h3>
                    <p class="mt-2 max-w-md mx-auto text-sm text-slate-500">Try broader keywords, clear filters, or check spelling. All words in your query must match somewhere in the record.</p>
                    <a href="{{ route('search', ['q' => $query]) }}" class="btn-glow mt-6 inline-block rounded-full border border-slate-300 bg-white px-6 py-2 text-sm font-medium text-black hover:bg-slate-50">Reset filters</a>
                </div>
            @endforelse
        </div>

        {{ $books->withQueryString()->links('vendor.pagination.dark-glass') }}
    </div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.js-trigger-search-loading').forEach(function (form) {
    form.addEventListener('submit', function () {
        var el = document.getElementById('search-loading');
        if (el) {
            el.classList.remove('hidden');
            el.classList.add('flex');
        }
    });
});
</script>
@endpush
