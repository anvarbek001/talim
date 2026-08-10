@extends('layouts.student')

@php
    $itemTitle = $itemTitle ?? $purchasable->title;
    $lockDesc = $lockDesc ?? "Bu materialni ochish uchun sotib olish kerak.";

    // Bo'lim/kitob narxidan tashqari — shu o'qituvchining barchasini
    // qamrab oluvchi oylik obuna ham mavjud bo'lsa, muqobil sifatida ko'rsatamiz.
    $subTeacher = $purchasable instanceof \App\Contracts\TeacherOwned ? $purchasable->user : null;
    $showTeacherSub = $subTeacher && $subTeacher->subscription_price > 0;
@endphp

@section('content')
    <div class="page">
        <a href="{{ $backUrl }}" class="back-link fade-up">
            <i class="bi bi-arrow-left"></i> Orqaga
        </a>

        <div class="card fade-up locked-card">
            <div class="locked-icon">
                <i class="bi bi-lock-fill"></i>
            </div>
            <div class="locked-body">
                <span class="locked-type-chip">{{ $contentLabel }}</span>
                <h1>{{ $itemTitle }}</h1>
                @if ($type === 'section')
                    <p class="locked-sub">Bo'lim: {{ $purchasable->title }}</p>
                @endif
                <p class="locked-desc">{{ $lockDesc }}</p>

                @if (session('error'))
                    <div class="alert-error">{{ session('error') }}</div>
                @endif

                <div class="locked-price">{{ number_format($purchasable->price, 0, '.', ' ') }} <span>so'm</span></div>

                <div class="locked-buy-row">
                    <form action="{{ route('student-purchases.store', [$type, $id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="locked-buy-btn locked-buy-btn-outline">
                            <i class="bi bi-wallet2"></i> Sotib olish (balans)
                        </button>
                    </form>
                    <form action="{{ route('click.pay', [$type, $id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="locked-buy-btn">
                            <i class="bi bi-bag-check"></i> Sotib olish (Click)
                        </button>
                    </form>
                </div>

                @if ($showTeacherSub)
                    <div class="locked-sub-alt">
                        <div class="locked-sub-alt-divider"><span>yoki</span></div>
                        <p class="locked-sub-alt-desc">
                            <strong>{{ $subTeacher->name }}</strong>ga obuna bo'lib, uning barcha bo'lim va
                            kitoblaridan 1 oy davomida to'liq foydalaning.
                        </p>
                        <form action="{{ route('student-purchases.store', ['teacher', $subTeacher->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="locked-buy-btn locked-buy-btn-alt">
                                <i class="bi bi-stars"></i>
                                Obuna bo'lish — {{ number_format($subTeacher->subscription_price, 0, '.', ' ') }} so'm/oy
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            font-weight: 600;
            font-size: .85rem;
            margin-bottom: 16px;
            text-decoration: none;
        }

        .back-link:hover {
            color: var(--primary);
        }

        .locked-card {
            padding: 0;
            overflow: hidden;
        }

        .locked-icon {
            width: 100%;
            aspect-ratio: 21 / 9;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary), #9C8CFF);
            color: #fff;
            font-size: 2.4rem;
        }

        .locked-body {
            padding: 26px 24px 30px;
            text-align: center;
        }

        .locked-type-chip {
            display: inline-flex;
            align-items: center;
            font-size: .7rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            background: var(--primary-soft);
            color: var(--primary);
            margin-bottom: 10px;
        }

        .locked-body h1 {
            font-size: 1.3rem;
            margin: 0 0 8px;
        }

        .locked-sub {
            color: var(--muted);
            font-size: .82rem;
            margin: 0 0 10px;
        }

        .locked-desc {
            color: var(--muted);
            font-size: .88rem;
            max-width: 480px;
            margin: 0 auto 20px;
        }

        .alert-error {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--coral-soft);
            color: var(--coral);
            border-radius: 12px;
            padding: 13px 16px;
            font-weight: 600;
            font-size: .88rem;
            margin: 0 auto 20px;
        }

        .locked-price {
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: 1.6rem;
            margin-bottom: 18px;
        }

        .locked-price span {
            font-size: .8rem;
            font-weight: 600;
            color: var(--muted);
        }

        .locked-buy-btn {
            padding: 12px 28px;
            border-radius: 10px;
            border: none;
            background: var(--primary);
            color: #fff;
            font-weight: 700;
            font-size: .9rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .locked-buy-btn:hover {
            background: #5A4BD6;
        }

        .locked-buy-row {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .locked-buy-btn-outline {
            background: transparent;
            border: 1.5px solid var(--line);
            color: var(--muted);
        }

        .locked-buy-btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .locked-sub-alt {
            margin-top: 22px;
            padding-top: 20px;
            border-top: 1px dashed var(--line);
        }

        .locked-sub-alt-divider {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }

        .locked-sub-alt-divider span {
            font-size: .74rem;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .locked-sub-alt-desc {
            font-size: .84rem;
            color: var(--muted);
            max-width: 420px;
            margin: 0 auto 14px;
        }

        .locked-buy-btn-alt {
            background: linear-gradient(135deg, var(--amber, #E0B24C), #F0C96A);
            color: #4A3300;
        }

        .locked-buy-btn-alt:hover {
            filter: brightness(1.05);
        }
    </style>
@endsection
