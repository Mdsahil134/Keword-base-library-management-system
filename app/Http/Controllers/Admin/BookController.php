<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(): View
    {
        $books = Book::query()->orderByDesc('updated_at')->paginate(15);

        return view('admin.books.index', compact('books'));
    }

    public function create(): View
    {
        return view('admin.books.create', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedBook($request);
        $data = $this->storeFiles($request, $data);
        Book::query()->create($data);

        return redirect()->route('admin.books.index')->with('status', 'Book created.');
    }

    public function edit(Book $book): View
    {
        return view('admin.books.edit', [
            'book' => $book,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $data = $this->validatedBook($request);
        $data = $this->storeFiles($request, $data, $book);
        $book->update($data);

        return redirect()->route('admin.books.index')->with('status', 'Book updated.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }
        if ($book->pdf_file) {
            Storage::disk('public')->delete($book->pdf_file);
        }
        $book->delete();

        return redirect()->route('admin.books.index')->with('status', 'Book deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedBook(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'min:1000', 'max:2100'],
            'keywords' => ['nullable', 'string', 'max:2000'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
            'pdf_file' => ['nullable', 'file', 'mimes:pdf', 'max:15360'],
            'total_copies' => ['required', 'integer', 'min:0'],
            'available_copies' => ['required', 'integer', 'min:0'],
        ]);

        if ((int) $validated['available_copies'] > (int) $validated['total_copies']) {
            $validated['available_copies'] = (int) $validated['total_copies'];
        }

        if (isset($validated['keywords'])) {
            $validated['keywords'] = trim(preg_replace('/\s*,\s*/', ', ', $validated['keywords']) ?? '');
        }

        return $validated;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function storeFiles(Request $request, array $data, ?Book $book = null): array
    {
        unset($data['cover_image'], $data['pdf_file']);

        if ($request->hasFile('cover_image')) {
            if ($book && $book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        } elseif ($book) {
            $data['cover_image'] = $book->cover_image;
        }

        if ($request->hasFile('pdf_file')) {
            if ($book && $book->pdf_file) {
                Storage::disk('public')->delete($book->pdf_file);
            }
            $data['pdf_file'] = $request->file('pdf_file')->store('pdfs', 'public');
        } elseif ($book) {
            $data['pdf_file'] = $book->pdf_file;
        }

        return $data;
    }
}
