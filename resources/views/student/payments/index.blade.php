@extends('layouts.student')

@section('content')
    <div class="page">
        <div class="page-head fade-up">
            <div>
                <h1>To'lovlarim</h1>
                <p class="page-sub">Sotib olgan bo'lim, kitob va testlaringiz uchun to'lov tarixi shu yerda ko'rinadi.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert-success fade-up"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert-error fade-up"><i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}</div>
        @endif

        <div class="balance-card fade-up" style="animation-delay:.02s;">
            <div class="balance-info">
                <div class="balance-lbl">Balansingiz</div>
                <div class="balance-num">{{ number_format($balance, 0, '.', ' ') }} <span>so'm</span></div>
            </div>
            <form action="{{ route('click.topup') }}" method="POST" class="topup-form">
                @csrf
                <input type="number" name="amount" class="topup-input" placeholder="Summa (so'm)"
                    min="{{ $minTopUp }}" step="1000" value="{{ old('amount') }}" required>
                <button type="submit" class="topup-btn">
                    <i class="bi bi-plus-circle"></i> Hisobni to'ldirish
                </button>
            </form>
            @error('amount')
                <div class="field-error">{{ $message }}</div>
            @enderror
            <div class="topup-hint">Click orqali to'lanadi — eng kamida {{ number_format($minTopUp, 0, '.', ' ') }} so'm.</div>
        </div>

        <div class="stats-grid fade-up" style="animation-delay:.04s;">
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--primary-soft);color:var(--primary);"><i class="bi bi-cash-stack"></i></div>
                <div class="stat-num">{{ number_format($totalSpent, 0, '.', ' ') }}</div>
                <div class="stat-lbl">Jami sarflangan (so'm)</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--mint-soft);color:var(--mint);"><i class="bi bi-bag-check"></i></div>
                <div class="stat-num">{{ $purchasesCount }}</div>
                <div class="stat-lbl">Amalga oshirilgan xaridlar</div>
            </div>
        </div>

        <div class="card fade-up" style="animation-delay:.08s;">
            <div class="card-head">
                <div class="card-title">To'lovlar tarixi</div>
            </div>

            @if ($payments->isEmpty())
                <div class="empty-hint"><i class="bi bi-info-circle"></i> Hali hech narsa sotib olinmagan.</div>
            @else
                <div class="table-wrap">
                    <table class="pay-table">
                        <thead>
                            <tr>
                                <th>Nomi</th>
                                <th>Turi</th>
                                <th>Narxi</th>
                                <th>Sana</th>
                                <th>Amal qilish muddati</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr>
                                    <td class="cell-strong" data-label="Nomi">{{ $payment->purchasable->title ?? '—' }}</td>
                                    <td class="cell-muted" data-label="Turi">{{ $typeLabels[$payment->purchasable_type] ?? class_basename($payment->purchasable_type) }}</td>
                                    <td data-label="Narxi">{{ number_format($payment->price, 0, '.', ' ') }} so'm</td>
                                    <td class="cell-muted" data-label="Sana">{{ $payment->created_at->format('d.m.Y H:i') }}</td>
                                    <td data-label="Muddati">
                                        @if ($payment->expires_at === null)
                                            <span class="cell-muted">Muddatsiz</span>
                                        @elseif ($payment->isActive())
                                            <span class="expiry-badge expiry-badge-active">{{ $payment->expires_at->format('d.m.Y') }} gacha</span>
                                        @else
                                            <span class="expiry-badge expiry-badge-expired">Muddati tugagan</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $payments->links() }}
            @endif
        </div>
    </div>

    <style>
        .page-head h1 {
            font-size: 1.5rem;
            margin: 4px 0 6px;
        }

        .page-sub {
            color: var(--muted);
            font-size: .88rem;
            margin: 0;
        }

        .alert-success,
        .alert-error {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            border-radius: 12px;
            font-weight: 600;
            font-size: .87rem;
            margin-bottom: 16px;
        }

        .alert-success {
            background: var(--mint-soft);
            color: var(--mint);
        }

        .alert-error {
            background: var(--coral-soft);
            color: var(--coral);
        }

        .balance-card {
            background: linear-gradient(135deg, var(--primary), #9C8CFF);
            border-radius: 20px;
            padding: 24px 26px;
            color: #fff;
            margin-bottom: 22px;
            max-width: 520px;
        }

        .balance-lbl {
            font-size: .82rem;
            opacity: .85;
            margin-bottom: 4px;
        }

        .balance-num {
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: 2rem;
            margin-bottom: 16px;
        }

        .balance-num span {
            font-size: 1rem;
            font-weight: 600;
            opacity: .85;
        }

        .topup-form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .topup-input {
            flex: 1;
            min-width: 160px;
            padding: 12px 14px;
            border-radius: 10px;
            border: 1.5px solid transparent;
            font-size: .92rem;
            font-weight: 700;
            background: #fff;
            color: #17171D;
            color-scheme: light;
        }

        .topup-input:focus {
            outline: none;
            border-color: rgba(255, 255, 255, .7);
        }

        .topup-input::placeholder {
            color: #8A88A3;
            font-weight: 500;
        }

        .topup-btn {
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 22px;
            border-radius: 10px;
            border: none;
            background: #fff;
            color: var(--primary);
            font-weight: 700;
            font-size: .88rem;
            cursor: pointer;
            transition: .2s;
        }

        .topup-btn:hover {
            background: #f2f1ff;
            transform: translateY(-1px);
        }

        .topup-hint {
            font-size: .76rem;
            opacity: .85;
            margin-top: 10px;
        }

        .field-error {
            color: #fff;
            background: rgba(0, 0, 0, .18);
            border-radius: 8px;
            padding: 6px 10px;
            font-size: .78rem;
            font-weight: 600;
            margin-top: 8px;
            display: inline-block;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-bottom: 22px;
            max-width: 520px;
        }

        .stat-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 20px;
            box-shadow: var(--shadow-sm);
        }

        .stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            margin-bottom: 12px;
        }

        .stat-num {
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: 1.7rem;
        }

        .stat-lbl {
            color: var(--muted);
            font-size: .82rem;
            margin-top: 2px;
        }

        .empty-hint {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 16px;
            border-radius: 12px;
            background: var(--bg-soft);
            color: var(--muted);
            font-size: .88rem;
        }

        .table-wrap {
            overflow-x: auto;
        }

        .pay-table {
            width: 100%;
            border-collapse: collapse;
        }

        .pay-table th {
            text-align: left;
            font-size: .74rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--muted);
            padding: 10px 12px;
            border-bottom: 1px solid var(--line);
        }

        .pay-table td {
            padding: 12px;
            font-size: .87rem;
            border-bottom: 1px solid var(--line);
        }

        .pay-table .cell-strong {
            font-weight: 700;
        }

        .pay-table .cell-muted {
            color: var(--muted);
        }

        .pay-table tr:last-child td {
            border-bottom: none;
        }

        .expiry-badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: .74rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .expiry-badge-active {
            background: var(--mint-soft);
            color: var(--mint);
        }

        .expiry-badge-expired {
            background: var(--coral-soft);
            color: var(--coral);
        }

        @media (max-width:640px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
                max-width: none;
            }
        }
    </style>
@endsection
