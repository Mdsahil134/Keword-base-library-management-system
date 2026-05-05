@extends('layouts.app')

@section('title', 'Books')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <h1 class="text-2xl font-bold text-slate-900">Book Management</h1>
        <a href="{{ route('admin.books.create') }}" class="btn-glow inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-800 hover:bg-slate-50">Add book</a>
    </div>
    <div class="glass overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-clean w-full min-w-[760px] text-left text-sm">
                <thead class="border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-4 py-3">Cover</th>
                    <th class="px-4 py-3">Title</th>
                    <th class="px-4 py-3">Author</th>
                    <th class="px-4 py-3">Availability</th>
                    <th class="px-4 py-3">Category</th>
                    <th class="px-4 py-3">Year</th>
                    <th class="px-4 py-3 text-end">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @foreach($books as $book)
                    <tr class="transition hover:bg-slate-50">
                        <td class="px-4 py-3">
                            @if($book->cover_image)
                                <img src="{{ asset('storage/'.$book->cover_image) }}" alt="{{ $book->title }} cover" class="h-16 w-12 rounded object-cover">
                            @else
                                <div class="h-16 w-12 rounded bg-slate-100"></div>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-medium text-slate-900">{{ Str::limit($book->title, 48) }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $book->author }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $book->available_copies }}/{{ $book->total_copies }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $book->category }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $book->year }}</td>
                        <td class="px-4 py-3 text-end">
                            <a href="{{ route('books.show', $book) }}" class="mr-2 rounded-full border border-slate-200 px-3 py-1 text-xs text-slate-600 hover:bg-slate-100">View</a>
                            <a href="{{ route('admin.books.edit', $book) }}" class="mr-2 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs text-indigo-700 hover:bg-indigo-100">Edit</a>
                            <form action="{{ route('admin.books.destroy', $book) }}" method="post" class="inline" onsubmit="return confirm('Delete this book?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-xs text-rose-700 hover:bg-rose-100">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-6">{{ $books->links('vendor.pagination.dark-glass') }}</div>
@endsection
