<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClickTransaction;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\View\View;

class TransactionController extends Controller implements HasMiddleware
{
    /**
     * Read-only by design — a payment/transaction log is an audit trail and
     * should never be edited or deleted from the admin UI.
     */
    public static function middleware(): array
    {
        return ['auth', 'admin'];
    }

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $status = $request->query('status');
        $type = $request->query('type');

        $transactions = ClickTransaction::with(['user', 'purchasable'])
            ->when($q !== '', fn ($query) => $query->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%")))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($type, fn ($query) => $query->where('type', $type))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.transactions.index', compact('transactions'));
    }
}
