@extends('layouts.admin')

@php
    $statusMeta = [
        'pending' => ['Kutilmoqda', 'var(--muted)', 'var(--bg-soft)'],
        'prepared' => ['Tayyorlandi', 'var(--primary)', 'var(--primary-soft)'],
        'paid' => ["To'landi", 'var(--mint)', 'var(--mint-soft)'],
        'cancelled' => ['Bekor qilindi', 'var(--coral)', 'var(--coral-soft)'],
        'failed' => ['Xatolik', 'var(--coral)', 'var(--coral-soft)'],
    ];
    $typeLabels = ['purchase' => 'Xarid', 'topup' => "To'ldirish"];
@endphp

@section('content')
    <div class="page">

        <div class="page-head fade-up">
            <div>
                <h1>Tranzaksiyalar</h1>
                <p class="page-sub">Click orqali amalga oshirilgan barcha to'lov urinishlari — muvaffaqiyatli, kutilayotgan
                    va bekor qilinganlari ham shu yerda ko'rinadi. Bu yozuv faqat ko'rish uchun — moliyaviy jurnal
                    tahrirlanmaydi yoki o'chirilmaydi.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.transactions.index') }}" class="filter-bar fade-up">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Foydalanuvchi ismi bo'yicha qidirish..." class="filter-input">
            <select name="type" class="filter-select" onchange="this.form.submit()">
                <option value="">Barcha turlar</option>
                @foreach ($typeLabels as $value => $label)
                    <option value="{{ $value }}" {{ request('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="">Barcha holatlar</option>
                @foreach ($statusMeta as $value => $meta)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $meta[0] }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-outline"><i class="bi bi-search"></i> Qidirish</button>
            @if (request('q') || request('type') || request('status'))
                <a href="{{ route('admin.transactions.index') }}" class="filter-reset">Tozalash</a>
            @endif
        </form>

        <div class="card fade-up" style="animation-delay:.05s;">
            @if ($transactions->isEmpty())
                <div class="empty-hint"><i class="bi bi-info-circle"></i> Hech qanday tranzaksiya topilmadi.</div>
            @else
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Foydalanuvchi</th>
                                <th>Turi</th>
                                <th>Nomi</th>
                                <th>Summa</th>
                                <th>Holati</th>
                                <th>Click ID</th>
                                <th>Sana</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $transaction)
                                <tr>
                                    <td class="cell-strong" data-label="Foydalanuvchi">{{ $transaction->user->name ?? '—' }}</td>
                                    <td class="cell-muted" data-label="Turi">{{ $typeLabels[$transaction->type] ?? $transaction->type }}</td>
                                    <td data-label="Nomi">
                                        {{ $transaction->type === 'topup' ? "Hisobni to'ldirish" : ($transaction->purchasable->title ?? '—') }}
                                    </td>
                                    <td data-label="Summa">{{ number_format($transaction->amount, 0, '.', ' ') }} so'm</td>
                                    <td data-label="Holati">
                                        @php $meta = $statusMeta[$transaction->status] ?? ['—', 'var(--muted)', 'var(--bg-soft)']; @endphp
                                        <span class="status-pill" style="color:{{ $meta[1] }};background:{{ $meta[2] }};">{{ $meta[0] }}</span>
                                    </td>
                                    <td class="cell-muted" data-label="Click ID">{{ $transaction->click_trans_id ?? '—' }}</td>
                                    <td class="cell-muted" data-label="Sana">{{ $transaction->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $transactions->links() }}
            @endif
        </div>
    </div>

    <style>
        .status-pill {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: .72rem;
            font-weight: 700;
        }
    </style>
@endsection
