<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Models\Book;
use App\Models\BookFile;
use App\Services\BookService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller implements HasMiddleware
{
    public function __construct(protected BookService $bookServ) {}

    public static function middleware(): array
    {
        return ['auth'];
    }

    public function index()
    {
        return view('books.index');
    }

    public function store(BookRequest $request)
    {
        $this->bookServ->createBook($request->validated(), Auth::id());

        return redirect()->route('books.mine')->with('success', 'Kitob muvaffaqiyatli joylandi');
    }

    public function myBooks(Request $request)
    {
        $books = $this->bookServ->myBooks(Auth::id(), [
            'q' => trim((string) $request->query('q', '')),
        ]);

        return view('books.mine', compact('books'));
    }

    /**
     * Shared protected viewer page — the owning teacher, or a student with
     * purchase access, land here; everyone else sees the paywall.
     */
    public function view(Book $book)
    {
        if (! $this->bookServ->canView($book, Auth::user())) {
            return view('student.partials.locked', [
                'purchasable' => $book,
                'type' => 'book',
                'id' => $book->id,
                'itemTitle' => $book->title,
                'contentLabel' => 'Kitob',
                'lockDesc' => 'Bu kitobni ochish uchun sotib olish kerak.',
                'backUrl' => route('student-books.index'),
            ]);
        }

        $book->load('files');

        return view('books.view', compact('book'));
    }

    /**
     * Raw inline PDF bytes — only ever used as an <iframe>/<embed> src, never
     * linked to directly, so it stays the sole way to get file bytes out.
     */
    public function stream(Book $book, BookFile $bookFile)
    {
        abort_unless($bookFile->book_id === $book->id, 404);
        abort_unless($this->bookServ->canView($book, Auth::user()), 403);

        $path = Storage::disk('local')->path($bookFile->file_path);

        abort_unless(is_file($path), 404);

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.addslashes($bookFile->original_name).'"',
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
