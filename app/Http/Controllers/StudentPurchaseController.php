<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\DtmTest;
use App\Models\Section;
use App\Models\SertifikatTest;
use App\Services\PurchaseService;
use Exception;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;

class StudentPurchaseController extends Controller implements HasMiddleware
{
    protected const TYPE_MAP = [
        'section' => Section::class,
        'dtm' => DtmTest::class,
        'sertifikat' => SertifikatTest::class,
        'book' => Book::class,
    ];

    public function __construct(protected PurchaseService $purchaseServ) {}

    public static function middleware(): array
    {
        return ['auth'];
    }

    public function store(string $type, int $id)
    {
        $class = self::TYPE_MAP[$type] ?? null;

        abort_if($class === null, 404);

        $purchasable = $class::findOrFail($id);

        try {
            $this->purchaseServ->purchase(Auth::user(), $purchasable);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Xarid muvaffaqiyatli amalga oshirildi');
    }
}
