@extends('layouts.student')

@section('content')
    <div class="page">
        <div class="page-head fade-up">
            <div>
                <h1>Obunam</h1>
                <p class="page-sub">Bir nechta dars har doim tekin — qolgan barcha video darslarni ko'rish uchun obuna tanlang.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="sub-alert fade-up">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        <div class="card fade-up status-card" style="animation-delay:.04s;">
            @if ($activeSubscription)
                <div class="status-active">
                    <div class="status-icon"><i class="bi bi-patch-check-fill"></i></div>
                    <div>
                        <div class="status-title">{{ $activeSubscription->planLabel() }} obuna faol</div>
                        <div class="status-sub">Amal qilish muddati: {{ $activeSubscription->ends_at->format('d.m.Y') }} gacha</div>
                    </div>
                </div>
            @else
                <div class="status-inactive">
                    <div class="status-icon"><i class="bi bi-unlock"></i></div>
                    <div>
                        <div class="status-title">Faol obuna yo'q</div>
                        <div class="status-sub">Faqat bir nechta boshlang'ich dars ochiq — to'liq kirish uchun obuna tanlang.</div>
                    </div>
                </div>
            @endif
        </div>

        <div class="plans-grid fade-up" style="animation-delay:.1s;">
            @foreach ($plans as $key => $plan)
                <div class="plan-card {{ $key === 'monthly' ? 'is-popular' : '' }}">
                    @if ($key === 'monthly')
                        <div class="plan-badge">Mashhur</div>
                    @endif
                    <div class="plan-label">{{ $plan['label'] }}</div>
                    <div class="plan-price">{{ number_format($plan['price'], 0, '.', ' ') }} <span>so'm</span></div>
                    <div class="plan-days">{{ $plan['days'] }} kun davomida to'liq kirish</div>
                    <form action="{{ route('student-subscription.subscribe') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan" value="{{ $key }}">
                        <button type="submit" class="plan-btn">
                            {{ $activeSubscription ? 'Uzaytirish' : 'Faollashtirish' }}
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <div class="demo-note fade-up" style="animation-delay:.14s;">
            <i class="bi bi-info-circle"></i>
            Bu — namunaviy to'lov oqimi: haqiqiy to'lov tizimi (Payme/Click) hali ulanmagan, tugma bosilganda obuna darhol faollashadi.
        </div>

        @if ($history->isNotEmpty())
            <div class="card fade-up" style="animation-delay:.18s;">
                <div class="card-head">
                    <div class="card-title">Obunalar tarixi</div>
                </div>
                @foreach ($history as $item)
                    <div class="history-row">
                        <div class="history-plan">{{ $item->planLabel() }}</div>
                        <div class="history-dates">{{ $item->starts_at->format('d.m.Y') }} — {{ $item->ends_at->format('d.m.Y') }}</div>
                        <div class="history-price">{{ number_format($item->price, 0, '.', ' ') }} so'm</div>
                    </div>
                @endforeach
            </div>
        @endif
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
            max-width: 560px;
        }

        .sub-alert {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--mint-soft);
            color: var(--mint);
            border-radius: 12px;
            padding: 13px 16px;
            font-weight: 600;
            font-size: .88rem;
            margin-bottom: 18px;
        }

        .status-card {
            padding: 18px 20px;
        }

        .status-active,
        .status-inactive {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .status-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .status-active .status-icon {
            background: var(--mint-soft);
            color: var(--mint);
        }

        .status-inactive .status-icon {
            background: var(--amber-soft);
            color: #8A6100;
        }

        .status-title {
            font-weight: 700;
            font-size: .96rem;
        }

        .status-sub {
            font-size: .8rem;
            color: var(--muted);
            margin-top: 2px;
        }

        .plans-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin: 20px 0;
        }

        @media (max-width:900px) {
            .plans-grid {
                grid-template-columns: 1fr;
            }
        }

        .plan-card {
            position: relative;
            background: var(--card);
            border: 1.5px solid var(--line);
            border-radius: 18px;
            padding: 24px 20px;
            box-shadow: var(--shadow-sm);
            text-align: center;
        }

        .plan-card.is-popular {
            border-color: var(--primary);
            box-shadow: var(--shadow);
        }

        .plan-badge {
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--primary);
            color: #fff;
            font-size: .68rem;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .plan-label {
            font-weight: 700;
            font-size: 1rem;
            color: var(--muted);
            margin-bottom: 10px;
        }

        .plan-price {
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: 1.7rem;
            margin-bottom: 4px;
        }

        .plan-price span {
            font-size: .8rem;
            font-weight: 600;
            color: var(--muted);
        }

        .plan-days {
            font-size: .8rem;
            color: var(--muted);
            margin-bottom: 18px;
        }

        .plan-btn {
            width: 100%;
            padding: 11px;
            border-radius: 10px;
            border: none;
            background: var(--primary);
            color: #fff;
            font-weight: 700;
            font-size: .88rem;
            transition: .2s;
        }

        .plan-btn:hover {
            background: #5A4BD6;
        }

        .demo-note {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: var(--bg-soft);
            border: 1px dashed var(--line);
            border-radius: 12px;
            padding: 13px 16px;
            font-size: .78rem;
            color: var(--muted);
            margin-bottom: 18px;
        }

        .history-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid var(--line);
            font-size: .84rem;
        }

        .history-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .history-plan {
            font-weight: 700;
        }

        .history-dates {
            color: var(--muted);
            font-size: .78rem;
        }

        .history-price {
            font-weight: 700;
            color: var(--muted);
        }
    </style>
@endsection
