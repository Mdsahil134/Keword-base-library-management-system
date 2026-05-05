@extends('layouts.app')

@section('title', 'Categories')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <h1 class="text-2xl font-bold text-white">Categories</h1>
        <a href="{{ route('admin.categories.create') }}" class="btn-glow inline-flex rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 px-5 py-2.5 text-sm font-semibold text-white">Add category</a>
    </div>
    <div class="glass overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-white/10 bg-white/[0.03] text-xs font-semibold uppercase tracking-wider text-slate-500">
            <tr>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3 text-end">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
            @foreach($categories as $category)
                <tr class="hover:bg-white/[0.03]">
                    <td class="px-4 py-3 font-medium text-slate-200">{{ $category->name }}</td>
                    <td class="px-4 py-3 text-end">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="mr-3 text-xs text-sky-400 hover:text-sky-300">Edit</a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="post" class="inline" onsubmit="return confirm('Delete this category?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-rose-400 hover:text-rose-300">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $categories->links('vendor.pagination.dark-glass') }}</div>
@endsection
