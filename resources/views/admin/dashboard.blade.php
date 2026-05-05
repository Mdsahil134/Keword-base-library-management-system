@extends('layouts.app')

@section('title', 'Admin')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Admin dashboard</h1>
        <p class="mt-1 text-slate-600">Manage books, categories and issue workflows in one place.</p>
    </div>
    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="stat-card glass p-6">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Books</p>
            <p class="mt-2 text-4xl font-bold text-slate-900">{{ $bookCount }}</p>
        </div>
        <div class="stat-card glass p-6">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Categories</p>
            <p class="mt-2 text-4xl font-bold text-slate-900">{{ $categoryCount }}</p>
        </div>
        <div class="stat-card glass p-6">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pending requests</p>
            <p class="mt-2 text-4xl font-bold text-amber-600">{{ $pendingRequestCount }}</p>
        </div>
    </div>
    <div class="glass max-w-2xl divide-y divide-slate-100 overflow-hidden">
        <a href="{{ route('admin.books.index') }}" class="flex items-center justify-between px-6 py-4 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
            Manage books
            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        </a>
        <a href="{{ route('admin.categories.index') }}" class="flex items-center justify-between px-6 py-4 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
            Manage categories
            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        </a>
        <a href="{{ route('admin.book-requests.index') }}" class="flex items-center justify-between px-6 py-4 text-sm font-medium text-amber-700 transition hover:bg-amber-50">
            Manage issue requests
            <svg class="h-4 w-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        </a>
        <a href="{{ route('home') }}" class="flex items-center justify-between px-6 py-4 text-sm font-medium text-indigo-700 transition hover:bg-indigo-50">
            View public site
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5M14 4.5h4.5m0 0V9m0-4.5L10.5 15"/></svg>
        </a>
    </div>
@endsection
