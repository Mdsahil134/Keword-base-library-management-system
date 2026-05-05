@extends('layouts.app')

@section('title', 'Edit category')

@section('content')
    <h1 class="mb-6 text-2xl font-bold text-white">Edit category</h1>
    <div class="glass max-w-md p-6 md:p-8">
        <form method="post" action="{{ route('admin.categories.update', $category) }}" class="space-y-5">
            @csrf
            @method('PUT')
            <div>
                <label class="mb-1 block text-xs font-medium uppercase tracking-wider text-slate-400" for="name">Name</label>
                <input type="text" name="name" id="name" required
                       class="input-dark @error('name') border-rose-500/50 @enderror"
                       value="{{ old('name', $category->name) }}">
                @error('name')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
            </div>
            <p class="text-sm text-slate-500">Renaming updates the category on all linked books.</p>
            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" class="btn-glow rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 px-6 py-2.5 text-sm font-semibold text-white">Update</button>
                <a href="{{ route('admin.categories.index') }}" class="rounded-xl border border-white/10 px-6 py-2.5 text-sm text-slate-300 hover:bg-white/5">Cancel</a>
            </div>
        </form>
    </div>
@endsection
