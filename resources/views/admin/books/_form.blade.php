@php
    $b = $book ?? null;
@endphp
<div>
    <label class="mb-1 block text-xs font-medium uppercase tracking-wider text-slate-400" for="title">Title</label>
    <input type="text" name="title" id="title" required
           class="input-dark @error('title') border-rose-500/50 @enderror"
           value="{{ old('title', $b->title ?? '') }}">
    @error('title')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-xs font-medium uppercase tracking-wider text-slate-400" for="author">Author</label>
    <input type="text" name="author" id="author" required
           class="input-dark @error('author') border-rose-500/50 @enderror"
           value="{{ old('author', $b->author ?? '') }}">
    @error('author')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-xs font-medium uppercase tracking-wider text-slate-400" for="category">Category</label>
    <select name="category" id="category" required class="select-dark @error('category') border-rose-500/50 @enderror">
        <option value="">— Select —</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->name }}" @selected(old('category', $b->category ?? '') === $cat->name)>{{ $cat->name }}</option>
        @endforeach
    </select>
    @error('category')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
    @if($categories->isEmpty())
        <p class="mt-1 text-sm text-amber-400/90">Add at least one category first.</p>
    @endif
</div>
<div>
    <label class="mb-1 block text-xs font-medium uppercase tracking-wider text-slate-400" for="year">Year</label>
    <input type="number" name="year" id="year" required min="1000" max="2100"
           class="input-dark @error('year') border-rose-500/50 @enderror"
           value="{{ old('year', $b->year ?? date('Y')) }}">
    @error('year')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-xs font-medium uppercase tracking-wider text-slate-400" for="keywords">Keywords <span class="font-normal lowercase text-slate-500">(comma-separated)</span></label>
    <input type="text" name="keywords" id="keywords"
           class="input-dark @error('keywords') border-rose-500/50 @enderror"
           value="{{ old('keywords', $b->keywords ?? '') }}" placeholder="e.g. fiction, award, bestseller">
    @error('keywords')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-xs font-medium uppercase tracking-wider text-slate-400" for="total_copies">Total copies</label>
    <input type="number" name="total_copies" id="total_copies" required min="0"
           class="input-dark @error('total_copies') border-rose-500/50 @enderror"
           value="{{ old('total_copies', $b->total_copies ?? 1) }}">
    @error('total_copies')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-xs font-medium uppercase tracking-wider text-slate-400" for="available_copies">Available copies</label>
    <input type="number" name="available_copies" id="available_copies" required min="0"
           class="input-dark @error('available_copies') border-rose-500/50 @enderror"
           value="{{ old('available_copies', $b->available_copies ?? 1) }}">
    @error('available_copies')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-xs font-medium uppercase tracking-wider text-slate-400" for="cover_image">Cover image</label>
    <input type="file" name="cover_image" id="cover_image" accept="image/*"
           class="input-dark @error('cover_image') border-rose-500/50 @enderror">
    @if(!empty($b?->cover_image))
        <p class="mt-1 text-xs text-slate-500">Current: {{ $b->cover_image }}</p>
    @endif
    @error('cover_image')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-xs font-medium uppercase tracking-wider text-slate-400" for="pdf_file">PDF file</label>
    <input type="file" name="pdf_file" id="pdf_file" accept="application/pdf"
           class="input-dark @error('pdf_file') border-rose-500/50 @enderror">
    @if(!empty($b?->pdf_file))
        <p class="mt-1 text-xs text-slate-500">Current: {{ $b->pdf_file }}</p>
    @endif
    @error('pdf_file')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-xs font-medium uppercase tracking-wider text-slate-400" for="description">Description</label>
    <textarea name="description" id="description" rows="5" class="input-dark @error('description') border-rose-500/50 @enderror">{{ old('description', $b->description ?? '') }}</textarea>
    @error('description')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
</div>
