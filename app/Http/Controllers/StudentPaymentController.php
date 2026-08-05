<?php

namespace App\Http\Controllers;

use App\Services\ClickPaymentService;
use App\Services\StudentPaymentService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;

class StudentPaymentController extends Controller implements HasMiddleware
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

        return view('student.payments.index', compact('payments', 'totalSpent', 'purchasesCount', 'typeLabels', 'balance', 'minTopUp'));
    }
}
