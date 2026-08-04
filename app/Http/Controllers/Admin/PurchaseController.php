<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\DtmTest;
use App\Models\Purchase;
use App\Models\Section;
use App\Models\SertifikatTest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\View\View;

class PurchaseController extends Controller implements HasMiddleware
{
    /**
     * Purchasable types assignable from the admin filter dropdown, keyed by
     * their FQCN and labeled with the Uzbek name shown in the UI.
     *
     * @var array<class-string, string>
     */
    public const TYPE_LABELS = [
        Book::class => 'Kitob',
        Section::class => "Bo'lim",
        DtmTest::class => 'DTM testi',
        SertifikatTest::class => 'Sertifikat testi',
    ];

    public static function middleware(): array
    {
        return ['auth', 'admin'];
    }

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $type = $request->query('type');

        $purchases = Purchase::with(['user', 'purchasable'])
            ->when($q !== '', fn ($query) => $query->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%")))
            ->when($type, fn ($query) => $query->where('purchasable_type', $type))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.purchases.index', ['purchases' => $purchases, 'typeLabels' => self::TYPE_LABELS]);
    }

    public function destroy(Purchase $purchase): RedirectResponse
    {
        $purchase->delete();

        return redirect()->route('admin.purchases.index')->with('success', "Xarid yozuvi o'chirildi");
    }
}
