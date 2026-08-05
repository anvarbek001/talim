<?php

namespace App\Http\Controllers;

use App\Services\ClickPaymentService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class ClickPaymentController extends Controller implements HasMiddleware
{
    public function __construct(protected ClickPaymentService $clickServ) {}

    public static function middleware(): array
    {
        // prepare/complete are called directly by Click's servers — no session, no auth.
        return [new Middleware('auth', only: ['pay', 'topUp'])];
    }

    /**
     * Student clicks "Click orqali to'lash" — redirect to Click's checkout page.
     */
    public function pay(string $type, int $id)
    {
        $url = $this->clickServ->initiate(Auth::user(), $type, $id);

        return redirect()->away($url);
    }

    /**
     * Student clicks "Hisobni to'ldirish" and enters an amount.
     */
    public function topUp(Request $request)
    {
        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:'.ClickPaymentService::MIN_TOPUP_AMOUNT],
        ], [
            'amount.required' => "To'ldirish summasini kiriting.",
            'amount.integer' => "Summa butun son bo'lishi kerak.",
            'amount.min' => 'Eng kamida :min so\'m kiritish kerak.',
        ]);

        $url = $this->clickServ->initiateTopUp(Auth::user(), (int) $data['amount']);

        return redirect()->away($url);
    }

    /**
     * Click's Prepare (action=0) webhook.
     */
    public function prepare(Request $request)
    {
        return response()->json($this->clickServ->prepare($request->all()));
    }

    /**
     * Click's Complete (action=1) webhook.
     */
    public function complete(Request $request)
    {
        return response()->json($this->clickServ->complete($request->all()));
    }
}
