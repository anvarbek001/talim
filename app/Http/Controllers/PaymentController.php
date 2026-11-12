<?php

namespace App\Http\Controllers;

use App\Services\ClickPaymentService;
use App\Services\StudentPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct(protected StudentPaymentService $paymentServ) {}

    public static function middleware(): array
    {
        return ['auth'];
    }

    public function index()
    {
        $payments = $this->paymentServ->history(Auth::id());
        $totalSpent = $this->paymentServ->totalSpent(Auth::id());
        $purchasesCount = $this->paymentServ->purchasesCount(Auth::id());
        $typeLabels = StudentPaymentService::TYPE_LABELS;
        $balance = Auth::user()->balance;   
        $minTopUp = ClickPaymentService::MIN_TOPUP_AMOUNT;

        return view('payments.index', compact('payments', 'totalSpent', 'purchasesCount', 'typeLabels', 'balance', 'minTopUp'));
    }
}
