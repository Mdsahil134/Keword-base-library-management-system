@props(['book', 'showActions' => true])

@php
    $rating = number_format(min(5, max(3.5, 3.5 + (($book->available_copies ?? 0) / max(1, ($book->total_copies ?? 1))) * 1.5)), 1);
    $fullStars = floor($rating);
    $hasHalf = ($rating - $fullStars) >= 0.3;
@endphp

<div class="book-card group relative flex flex-col overflow-hidden rounded-xl bg-[#0f172a] shadow-md transition-all duration-300">

    {{-- Cover --}}
    <a href="{{ route('books.show', $book) }}" class="relative block aspect-[2/3] w-full overflow-hidden bg-slate-800">
        @if($book->cover_image)
            <img src="{{ asset('storage/'.$book->cover_image) }}"
                 alt="{{ $book->title }} cover"
                 loading="lazy"
                 class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-110">
        @else
            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-slate-700 to-slate-900">
                <svg class="h-12 w-12 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1" stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
            </div>
        @endif

        {{-- Hover overlay with actions --}}
        @if($showActions)
        <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-end bg-gradient-to-t from-black/80 via-black/30 to-transparent opacity-0 transition-opacity duration-300 group-hover:pointer-events-auto group-hover:opacity-100">
            <div class="flex w-full flex-col gap-2 p-3">
                <a href="{{ route('books.show', $book) }}"
                   class="flex w-full items-center justify-center gap-1.5 rounded-lg bg-white/95 px-3 py-2 text-xs font-semibold text-slate-900 backdrop-blur transition hover:bg-white">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    View Details
                </a>
                @if($book->pdf_file)
                    <a href="{{ asset('storage/'.$book->pdf_file) }}" target="_blank"
                       class="flex w-full items-center justify-center gap-1.5 rounded-lg bg-indigo-500/90 px-3 py-2 text-xs font-semibold text-white backdrop-blur transition hover:bg-indigo-500">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                        Read PDF
                    </a>
                @endif
                @auth
                    @if($book->available_copies > 0)
                        <form action="{{ route('book-requests.store') }}" method="post" class="w-full">
                            @csrf
                            <input type="hidden" name="book_id" value="{{ $book->id }}">
                            <button type="submit"
                                    class="flex w-full items-center justify-center gap-1.5 rounded-lg bg-emerald-500/90 px-3 py-2 text-xs font-semibold text-white backdrop-blur transition hover:bg-emerald-500">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                Issue Book
                            </button>
                        </form>
                    @endif
                @endauth
            </div>
        </div>
        @endif

        {{-- Availability badge (top-right) --}}
        <div class="absolute right-2 top-2">
            @if($book->available_copies > 0)
                <span class="rounded-full bg-emerald-500/90 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white shadow backdrop-blur-sm">Available</span>
            @else
                <span class="rounded-full bg-rose-500/90 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white shadow backdrop-blur-sm">Out of stock</span>
            @endif
        </div>
    </a>

    {{-- Info --}}
    <div class="flex flex-1 flex-col px-3 pb-3 pt-3">
        <h3 class="truncate text-sm font-semibold text-white" title="{{ $book->title }}">
            {{ $book->title }}
        </h3>
        <p class="mt-0.5 truncate text-xs text-gray-400">
            {{ $book->author }}
        </p>
        <div class="mt-auto flex items-center justify-between pt-2">
            {{-- Star rating --}}
            <div class="flex items-center gap-1">
                <div class="flex text-amber-400">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $fullStars)
                            <svg class="h-3 w-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @elseif($hasHalf && $i == $fullStars + 1)
                            <svg class="h-3 w-3 fill-current opacity-60" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @else
                            <svg class="h-3 w-3 fill-current opacity-20" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endif
                    @endfor
                </div>
                <span class="text-[10px] font-medium text-gray-500">{{ $rating }}</span>
            </div>
            {{-- Copies indicator --}}
            <span class="text-[10px] text-gray-500">{{ $book->available_copies ?? 0 }}/{{ $book->total_copies ?? 0 }}</span>
        </div>
    </div>
</div>
