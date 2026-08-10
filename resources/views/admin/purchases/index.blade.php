@extends('layouts.admin')

@section('content')
    <div class="page">

        <div class="page-head fade-up">
            <div>
                <h1>Xaridlar</h1>
                <p class="page-sub">Platformada amalga oshirilgan barcha xaridlar tarixi.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="empty-hint" style="background:var(--mint-soft);color:var(--mint);border-color:var(--mint);margin-bottom:16px;">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <form method="GET" action="{{ route('admin.purchases.index') }}" class="filter-bar fade-up">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Xaridor ismi bo'yicha qidirish..." class="filter-input">
            <select name="type" class="filter-select" onchange="this.form.submit()">
                <option value="">Barcha turlar</option>
                @foreach ($typeLabels as $class => $label)
                    <option value="{{ $class }}" {{ request('type') === $class ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-outline"><i class="bi bi-search"></i> Qidirish</button>
            @if (request('q') || request('type'))
                <a href="{{ route('admin.purchases.index') }}" class="filter-reset">Tozalash</a>
            @endif
        </form>

        <div class="card fade-up" style="animation-delay:.05s;">
            @if ($purchases->isEmpty())
                <div class="empty-hint"><i class="bi bi-info-circle"></i> Hech qanday xarid topilmadi.</div>
            @else
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Xaridor</th>
                                <th>Material turi</th>
                                <th>Nomi</th>
                                <th>Narxi</th>
                                <th>Sana</th>
                                <th>Muddati</th>
                                <th>Amallar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchases as $purchase)
                                <tr>
                                    <td class="cell-strong" data-label="Xaridor">{{ $purchase->user->name }}</td>
                                    <td class="cell-muted" data-label="Turi">{{ $typeLabels[$purchase->purchasable_type] ?? class_basename($purchase->purchasable_type) }}</td>
                                    <td data-label="Nomi">{{ $purchase->purchasable->title ?? '—' }}</td>
                                    <td data-label="Narxi">{{ number_format($purchase->price, 0, '.', ' ') }} so'm</td>
                                    <td class="cell-muted" data-label="Sana">{{ $purchase->created_at->format('d.m.Y H:i') }}</td>
                                    <td data-label="Muddati">
                                        @if ($purchase->expires_at === null)
                                            <span class="cell-muted">Muddatsiz</span>
                                        @elseif ($purchase->isActive())
                                            {{ $purchase->expires_at->format('d.m.Y') }}
                                        @else
                                            <span style="color:var(--coral);font-weight:600;">Tugagan</span>
                                        @endif
                                    </td>
                                    <td class="actions-cell">
                                        <form action="{{ route('admin.purchases.destroy', $purchase) }}" method="POST"
                                            onsubmit="return confirm('Rostdan ham o\'chirmoqchimisiz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $purchases->links() }}
            @endif
        </div>
    </div>
@endsection
