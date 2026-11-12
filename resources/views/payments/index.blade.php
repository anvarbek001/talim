<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>To'lovlar va Balans</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Sora:wght@700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <div class="page-container">
        <!-- Sahifa sarlavhasi -->
        <div class="page-head fade-up">
            <div class="head-icon-wrapper">
                <i class="bi bi-wallet2"></i>
            </div>
            <div>
                <h1>To'lovlarim</h1>
                <p class="page-sub">Sotib olgan bo'lim, kitob va testlaringiz uchun to'lov tarixi hamda balansni boshqarish.</p>
            </div>
        </div>

        <!-- Xabarnomalar -->
        @if (session('success'))
            <div class="alert alert-success fade-up">
                <i class="bi bi-check-circle-fill"></i> 
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-error fade-up">
                <i class="bi bi-exclamation-triangle-fill"></i> 
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Balans kartasi -->
        <div class="balance-card fade-up" style="animation-delay:.05s;">
            <div class="balance-content">
                <div class="balance-info">
                    <span class="balance-lbl">Joriy balansingiz</span>
                    <div class="balance-num">{{ number_format($balance, 0, '.', ' ') }} <span>so'm</span></div>
                </div>
                <div class="wallet-badge">
                    <i class="bi bi-shield-check-fill"></span> Xavfsiz to'lov
                </div>
            </div>

            <form action="{{ route('click.topup') }}" method="POST" class="topup-form">
                @csrf
                <div class="input-wrapper">
                    <span class="currency-prefix">UZS</span>
                    <input type="number" name="amount" class="topup-input" placeholder="Summani kiriting"
                        min="{{ $minTopUp }}" step="1000" value="{{ old('amount') }}" required>
                </div>
                <button type="submit" class="topup-btn">
                    <i class="bi bi-plus-circle-fill"></i> Hisobni to'ldirish
                </button>
            </form>

            @error('amount')
                <div class="field-error"><i class="bi bi-info-circle"></i> {{ $message }}</div>
            @enderror

            <div class="topup-hint">
                <i class="bi bi-lightning-charge-fill"></i> Click tizimi orqali onlayn to'lov — Eng kam miqdor: <strong>{{ number_format($minTopUp, 0, '.', ' ') }} so'm</strong>
            </div>
        </div>

        <!-- Statistika grid -->
        <div class="stats-grid fade-up" style="animation-delay:.1s;">
            <div class="stat-card">
                <div class="stat-icon-wrap" style="background: rgba(99, 102, 241, 0.12); color: #6366f1;">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div>
                    <div class="stat-num">{{ number_format($totalSpent, 0, '.', ' ') }}</div>
                    <div class="stat-lbl">Jami sarflangan (so'm)</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-wrap" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                    <i class="bi bi-bag-check-fill"></i>
                </div>
                <div>
                    <div class="stat-num">{{ $purchasesCount }}</div>
                    <div class="stat-lbl">Amalga oshirilgan xaridlar</div>
                </div>
            </div>
        </div>

        <!-- Asosiy jadval kartasi -->
        <div class="card fade-up" style="animation-delay:.15s;">
            <div class="card-head">
                <div class="card-title"><i class="bi bi-clock-history"></i> To'lovlar tarixi</div>
            </div>

            @if ($payments->isEmpty())
                <div class="empty-hint">
                    <i class="bi bi-info-circle-fill"></i> 
                    <div>
                        <strong>Hali xaridlar mavjud emas</strong>
                        <p>Siz hali hech qanday kurs, kitob yoki test sotib olmagansiz.</p>
                    </div>
                </div>
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
                                    <td class="cell-strong" data-label="Nomi">
                                        <div class="item-title-flex">
                                            <i class="bi bi-box-seam text-muted"></i>
                                            <span>{{ $payment->purchasable->title ?? '—' }}</span>
                                        </div>
                                    </td>
                                    <td class="cell-muted" data-label="Turi">
                                        <span class="type-pill">
                                            {{ $typeLabels[$payment->purchasable_type] ?? class_basename($payment->purchasable_type) }}
                                        </span>
                                    </td>
                                    <td data-label="Narxi" class="cell-price">
                                        {{ number_format($payment->price, 0, '.', ' ') }} so'm
                                    </td>
                                    <td class="cell-muted" data-label="Sana">
                                        <i class="bi bi-calendar3"></i> {{ $payment->created_at->format('d.m.Y H:i') }}
                                    </td>
                                    <td data-label="Muddati">
                                        @if ($payment->expires_at === null)
                                            <span class="expiry-badge expiry-badge-unlimited">Muddatsiz</span>
                                        @elseif ($payment->isActive())
                                            <span class="expiry-badge expiry-badge-active">
                                                <i class="bi bi-check2"></i> {{ $payment->expires_at->format('d.m.Y') }} gacha
                                            </span>
                                        @else
                                            <span class="expiry-badge expiry-badge-expired">
                                                <i class="bi bi-x-circle"></i> Muddati tugagan
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="pagination-wrapper">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    </div>

    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --primary-light: rgba(79, 70, 229, 0.08);
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --success-bg: #d1fae5;
            --success-color: #065f46;
            --danger-bg: #fee2e2;
            --danger-color: #991b1b;
            --radius-lg: 20px;
            --radius-md: 12px;
            --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
            --shadow-md: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            margin: 0;
            padding: 20px;
            -webkit-font-smoothing: antialiased;
        }

        .page-container {
            max-width: 900px;
            margin: 0 auto;
        }

        /* Animatsiyalar */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-up {
            animation: fadeUp 0.5s ease forwards;
        }

        /* Sarlavha qismi */
        .page-head {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }

        .head-icon-wrapper {
            width: 50px;
            height: 50px;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .page-head h1 {
            font-size: 1.65rem;
            font-weight: 800;
            margin: 0 0 4px;
            color: var(--text-main);
        }

        .page-sub {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin: 0;
        }

        /* Xabarnomalar (Alerts) */
        .alert {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 20px;
            box-shadow: var(--shadow-sm);
        }

        .alert-success {
            background: var(--success-bg);
            color: var(--success-color);
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background: var(--danger-bg);
            color: var(--danger-color);
            border: 1px solid #fecaca;
        }

        /* Balans kartasi */
        .balance-card {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: var(--radius-lg);
            padding: 28px;
            color: #fff;
            margin-bottom: 24px;
            box-shadow: var(--shadow-md);
            position: relative;
            overflow: hidden;
        }

        .balance-card::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 180px;
            height: 180px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            pointer-events: none;
        }

        .balance-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .balance-lbl {
            font-size: 0.85rem;
            opacity: 0.85;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
        }

        .balance-num {
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: 2.4rem;
            margin-top: 4px;
            letter-spacing: -0.02em;
        }

        .balance-num span {
            font-size: 1.1rem;
            font-weight: 600;
            opacity: 0.9;
        }

        .wallet-badge {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .topup-form {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .input-wrapper {
            flex: 1;
            min-width: 200px;
            position: relative;
            display: flex;
            align-items: center;
        }

        .currency-prefix {
            position: absolute;
            left: 14px;
            color: var(--text-muted);
            font-weight: 700;
            font-size: 0.85rem;
            pointer-events: none;
        }

        .topup-input {
            width: 100%;
            padding: 14px 14px 14px 50px;
            border-radius: var(--radius-md);
            border: 2px solid transparent;
            font-size: 1rem;
            font-weight: 700;
            background: #fff;
            color: #0f172a;
            transition: all 0.2s ease;
            box-sizing: border-box;
        }

        .topup-input:focus {
            outline: none;
            border-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.2);
        }

        .topup-btn {
            background: #ffffff;
            color: var(--primary);
            border: none;
            padding: 14px 24px;
            border-radius: var(--radius-md);
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            flex-shrink: 0;
        }

        .topup-btn:hover {
            background: #f8fafc;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
        }

        .topup-hint {
            font-size: 0.8rem;
            opacity: 0.9;
            margin-top: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .field-error {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #fff;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Statistika Bloki */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: var(--shadow-sm);
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .stat-icon-wrap {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .stat-num {
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: 1.4rem;
            color: var(--text-main);
        }

        .stat-lbl {
            color: var(--text-muted);
            font-size: 0.8rem;
            margin-top: 2px;
            font-weight: 500;
        }

        /* Asosiy card va Jadval */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .card-head {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title i {
            color: var(--primary);
        }

        .empty-hint {
            padding: 40px 24px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            color: var(--text-muted);
        }

        .empty-hint i {
            font-size: 2.5rem;
            color: #cbd5e1;
        }

        .empty-hint strong {
            color: var(--text-main);
            font-size: 1rem;
        }

        .empty-hint p {
            margin: 0;
            font-size: 0.85rem;
        }

        .table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        .pay-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .pay-table th {
            background: #f8fafc;
            color: var(--text-muted);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 14px 20px;
            border-bottom: 1px solid var(--border-color);
            font-weight: 700;
        }

        .pay-table td {
            padding: 16px 20px;
            font-size: 0.9rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-main);
        }

        .pay-table tr:last-child td {
            border-bottom: none;
        }

        .pay-table tr:hover td {
            background-color: #f8fafc;
        }

        .cell-strong {
            font-weight: 700;
        }

        .item-title-flex {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cell-muted {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .cell-price {
            font-weight: 700;
            color: var(--primary);
        }

        .type-pill {
            background: #f1f5f9;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.78rem;
            font-weight: 600;
            color: #475569;
        }

        /* Status Badge-lar */
        .expiry-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .expiry-badge-active {
            background: var(--success-bg);
            color: var(--success-color);
        }

        .expiry-badge-expired {
            background: var(--danger-bg);
            color: var(--danger-color);
        }

        .expiry-badge-unlimited {
            background: #e2e8f0;
            color: #475569;
        }

        .pagination-wrapper {
            padding: 16px 24px;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
        }

        /* Responsive dizayn (Mobil qurilmalar uchun) */
        @media (max-width: 768px) {
            body {
                padding: 12px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .balance-card {
                padding: 20px;
            }

            .balance-num {
                font-size: 2rem;
            }

            .topup-form {
                flex-direction: column;
            }

            .topup-btn {
                width: 100%;
                justify-content: center;
            }

            /* Jadvalni mobilda moslashuvchan card ko'rinishiga o'tkazish */
            .pay-table thead {
                display: none;
            }

            .pay-table, .pay-table tbody, .pay-table tr, .pay-table td {
                display: block;
                width: 100%;
            }

            .pay-table tr {
                margin-bottom: 12px;
                border: 1px solid var(--border-color);
                border-radius: var(--radius-md);
                background: #fff;
                padding: 8px 0;
            }

            .pay-table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px 16px;
                border-bottom: 1px solid #f1f5f9;
                text-align: right;
            }

            .pay-table td::before {
                content: attr(data-label);
                font-weight: 700;
                color: var(--text-muted);
                font-size: 0.78rem;
                text-transform: uppercase;
            }

            .pay-table td:last-child {
                border-bottom: none;
            }
        }
    </style>
</body>
</html>
