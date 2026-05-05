@extends('layouts.app')

@section('title', $book->title)

@section('content')
    <nav aria-label="Breadcrumb" class="mb-6 text-sm text-slate-500">
        <ol class="flex flex-wrap items-center gap-2">
            <li><a href="{{ route('search') }}" class="text-sky-600 hover:text-sky-700">Search</a></li>
            <li class="text-slate-600">/</li>
            <li class="font-medium text-slate-800">{{ Str::limit($book->title, 52) }}</li>
        </ol>
    </nav>
    <div class="glass overflow-hidden">
        <div class="border-b border-slate-200 bg-gradient-to-r from-sky-500/10 to-indigo-500/10 px-6 py-8 md:px-10">
            <h1 class="text-2xl font-extrabold md:text-3xl" style="color: #0f172a;">{{ $book->title }}</h1>
            <p class="mt-2 text-lg" style="color: #334155;">{{ $book->author }}</p>
        </div>
        <div class="grid gap-6 p-6 md:grid-cols-3 md:p-10">
            <div class="space-y-4 text-sm md:col-span-1">
                <div>
                    @if($book->cover_image)
                        <img src="{{ asset('storage/'.$book->cover_image) }}" alt="{{ $book->title }} cover" class="h-60 w-full rounded-xl border border-slate-200 object-cover">
                    @else
                        <div class="flex h-60 w-full items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 text-xs text-slate-600">No cover image</div>
                    @endif
                </div>
            </div>
            <div class="md:col-span-2">
                <div class="mb-4 flex flex-wrap gap-2">
                    @if($book->pdf_file)
                        <a href="{{ asset('storage/'.$book->pdf_file) }}" target="_blank" class="rounded-lg border px-4 py-2 text-sm font-semibold hover:bg-slate-100" style="border-color: #94a3b8; background-color: #ffffff; color: #0f172a;">Read PDF</a>
                        <a href="{{ asset('storage/'.$book->pdf_file) }}" download class="rounded-lg border px-4 py-2 text-sm font-semibold hover:bg-sky-700" style="border-color: #0369a1; background-color: #0284c7; color: #ffffff;">Download PDF</a>
                    @endif
                    @auth
                        @if($book->available_copies > 0 && ! $hasActiveRequest)
                            <form action="{{ route('book-requests.store') }}" method="post">
                                @csrf
                                <input type="hidden" name="book_id" value="{{ $book->id }}">
                                <button type="submit" class="rounded-lg px-4 py-2 text-sm font-semibold hover:bg-blue-700" style="background-color: #2563eb; color: #ffffff;">Issue Book</button>
                            </form>
                        @elseif($hasActiveRequest)
                            <span class="inline-flex items-center rounded-lg border border-amber-300 bg-amber-50 px-4 py-2 text-sm text-amber-700">You already requested this book</span>
                        @endif
                    @endauth
                </div>
                @if($book->description)
                    <h2 class="mb-3 text-xs font-semibold uppercase tracking-wider text-slate-600">Description</h2>
                    <div class="max-w-none text-sm leading-relaxed text-slate-700">{!! nl2br(e($book->description)) !!}</div>
                @endif

                <dl class="mt-6 space-y-4 text-sm">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-600">Category</dt>
                        <dd class="mt-1 text-slate-700">{{ $book->category }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-600">Year</dt>
                        <dd class="mt-1 text-slate-700">{{ $book->year }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-600">Availability</dt>
                        <dd class="mt-1">
                            @if($book->available_copies > 0)
                                <span class="rounded-full border border-emerald-300 bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">Available ({{ $book->available_copies }}/{{ $book->total_copies }})</span>
                            @else
                                <span class="rounded-full border border-rose-300 bg-rose-50 px-2.5 py-1 text-xs font-medium text-rose-700">Out of stock</span>
                            @endif
                        </dd>
                    </div>
                    @if($book->keywords)
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-slate-600">Keywords</dt>
                            <dd class="mt-1 text-slate-700">{{ $book->keywords }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>
@endsection
