<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\User;
use App\Services\BookService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\View\View;

class BookController extends Controller implements HasMiddleware
{
    public function __construct(protected BookService $bookServ) {}

    public static function middleware(): array
    {
        return ['auth', 'admin'];
    }

    public function index(Request $request): View
    {
        $books = $this->bookServ->all([
            'q' => trim((string) $request->query('q', '')),
            'teacher_id' => $request->query('teacher'),
        ]);

        $teachers = User::whereHas('books')->orderBy('name')->get();

        return view('admin.books.index', compact('books', 'teachers'));
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'integer', 'min:0'],
        ]);

        $this->bookServ->updateBook($book, $data);

        return redirect()->route('admin.books.index')->with('success', 'Kitob yangilandi');
    }

    public function destroy(Book $book): RedirectResponse
    {
        $this->bookServ->deleteBook($book);

        return redirect()->route('admin.books.index')->with('success', "Kitob o'chirildi");
    }
}
