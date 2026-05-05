@extends('layouts.app')

@section('title', 'Add book')

@section('content')
    <h1 class="mb-6 text-2xl font-bold text-white">Add book</h1>
    <div class="glass max-w-2xl p-6 md:p-8">
        <form method="post" action="{{ route('admin.books.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @include('admin.books._form', ['book' => null, 'categories' => $categories])
            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" class="btn-glow rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 px-6 py-2.5 text-sm font-semibold text-white">Save</button>
                <a href="{{ route('admin.books.index') }}" class="rounded-xl border border-white/10 px-6 py-2.5 text-sm text-slate-300 hover:bg-white/5">Cancel</a>
            </div>
        </form>
    </div>
@endsection
