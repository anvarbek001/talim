<?php

namespace App\Http\Controllers;

use App\Services\BookService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;

class StudentBookController extends Controller implements HasMiddleware
{
    public function __construct(protected BookService $bookServ) {}

    public static function middleware(): array
    {
        return ['auth'];
    }

    public function index()
    {
        $catalog = $this->bookServ->catalog(Auth::id());

        return view('student.books.index', compact('catalog'));
    }
}
