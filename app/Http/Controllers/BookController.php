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
        try {
            $this->bookServ->createBook($request->validated(), Auth::id());

            return redirect()->route('books.mine')->with('success', 'Kitob muvaffaqiyatli joylandi');
        } catch (\Throwable $e) {
            \Log::error('Book upload failed: '.$e->getMessage(), ['exception' => $e]);

            return redirect()->back()->withInput()->with('error', 'Kitob yuklashda xatolik yuz berdi: '.$e->getMessage());
        }
    }

    public function myBooks(Request $request)
    {
        $books = $this->bookServ->myBooks(Auth::id(), [
            'q' => trim((string) $request->query('q', '')),
        ]);

        return view('books.mine', compact('books'));
    }

    public function edit(Book $book)
    {
        $this->bookServ->assertOwnerOrAdmin($book);

        return view('books.edit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $this->bookServ->assertOwnerOrAdmin($book);

        $data = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'integer', 'min:0'],
            'book_files.*' => ['file', 'mimes:pdf', 'max:51200'],
            'delete_file_ids' => ['nullable', 'array'],
            'delete_file_ids.*' => ['integer', 'exists:book_files,id'],
        ]);

        $uploaded = $request->file('book_files') ?? [];
        $deleteIds = $request->input('delete_file_ids', []);

        $this->bookServ->updateBook($book, $data, $uploaded, $deleteIds);

        return redirect()->route('books.mine')->with('success', 'Kitob yangilandi');
    }

    public function destroy(Book $book)
    {
        $this->bookServ->assertOwnerOrAdmin($book);

        $this->bookServ->deleteBook($book);

        return redirect()->route('books.mine')->with('success', "Kitob o'chirildi");
    }

    /**
     * Shared protected viewer page — the owning teacher, or a student with
     * purchase access, land here; everyone else sees the paywall.
     */
    public function view(Book $book)
    {
        // Reload via service to ensure relations & repository behaviour are
        // consistent with other places that fetch books.
        $book = $this->bookServ->find($book->id);

        if (! $book) {
            abort(404);
        }

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

        $disk = Storage::disk('local');
        $filePath = ltrim($bookFile->file_path, '/');

        if ($disk->exists($filePath)) {
            $path = $disk->path($filePath);
        } else {
            $candidate = storage_path('app/'.$filePath);
            if (is_file($candidate)) {
                $path = $candidate;
            } else {
                abort(404);
            }
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.addslashes($bookFile->original_name).'"',
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Return the rendered JPEG for a single PDF page.
     */
    public function page(Book $book, BookFile $bookFile, int $page)
    {
        abort_unless($bookFile->book_id === $book->id, 404);
        abort_unless($this->bookServ->canView($book, Auth::user()), 403);

        $disk = Storage::disk('local');
        $filePath = ltrim($bookFile->file_path, '/');

        if ($disk->exists($filePath)) {
            $path = $disk->path($filePath);
        } else {
            $candidate = storage_path('app/'.$filePath);
            if (is_file($candidate)) {
                $path = $candidate;
            } else {
                abort(404);
            }
        }

        if (! class_exists(\Imagick::class)) {
            abort(501, 'Imagick not available on server');
        }

        try {
            $im = new \Imagick;
            // set a reasonable resolution for readable images
            $im->setResolution(150, 150);
            // zero-indexed page selection
            $idx = max(0, $page - 1);
            $im->readImage($path."[{$idx}]");
            $im->setImageFormat('jpeg');
            $im->setImageCompressionQuality(80);
            $blob = $im->getImageBlob();
            $im->clear();
            $im->destroy();

            return response($blob, 200, [
                'Content-Type' => 'image/jpeg',
                'Cache-Control' => 'private, no-store, max-age=0, must-revalidate',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        } catch (\Exception $e) {
            \Log::error('PDF page render failed: '.$e->getMessage(), ['file' => $filePath, 'page' => $page]);
            abort(404);
        }
    }

    /**
     * Return page count for the PDF.
     */
    public function pages(Book $book, BookFile $bookFile)
    {
        abort_unless($bookFile->book_id === $book->id, 404);
        abort_unless($this->bookServ->canView($book, Auth::user()), 403);

        $disk = Storage::disk('local');
        $filePath = ltrim($bookFile->file_path, '/');

        if ($disk->exists($filePath)) {
            $path = $disk->path($filePath);
        } else {
            $candidate = storage_path('app/'.$filePath);
            if (is_file($candidate)) {
                $path = $candidate;
            } else {
                abort(404);
            }
        }

        if (! class_exists(\Imagick::class)) {
            abort(501, 'Imagick not available on server');
        }

        try {
            $im = new \Imagick;
            $im->pingImage($path);
            $count = $im->getNumberImages();
            $im->clear();
            $im->destroy();

            return response()->json(['pages' => $count]);
        } catch (\Exception $e) {
            \Log::error('PDF ping failed: '.$e->getMessage(), ['file' => $filePath]);
            abort(404);
        }
    }
}
