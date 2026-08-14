@extends('layouts.teacher')

@section('content')
    <div class="page">
        <div class="page-head fade-up">
            <div>
                <h1>Guruh tariflari</h1>
                <p class="page-sub">Bir vaqtning o'zida nechta guruh ochib turishingiz mumkinligini belgilaydi.
                    Guruh (Telegram guruhi kabi) o'zingiz o'chirmaguningizcha doimiy turadi — o'chirilgan guruh
                    joyi bo'shab, yangi guruh ochish imkoni tug'iladi.</p>
            </div>
        </div>

        @if (session('error'))
            <div class="mine-alert mine-alert-error fade-up">
                <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="mine-alert fade-up">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        @if ($activePlan)
            <div class="plan-status fade-up">
                <i class="bi bi-stars"></i>
                Joriy tarifingiz: <strong>{{ $activePlan->name }}</strong> —
                {{ $slotsUsed }} / {{ $activePlan->max_groups }} guruh ishlatilgan.
            </div>
        @else
            <div class="plan-status fade-up">
                <i class="bi bi-gift-fill"></i>
                Hozircha <strong>bepul tarifdasiz</strong> — {{ $slotsUsed }} /
                {{ \App\Models\User::FREE_GROUP_SLOTS }} guruh ishlatilgan. Ko'proq guruh kerak bo'lsa,
                pastdagi tariflardan birini tanlang.
            </div>
        @endif

        <div class="plan-grid">
            @foreach ($plans as $plan)
                @php $isActive = $activePlan && $activePlan->id === $plan->id; @endphp
                <div class="plan-card fade-up @if ($isActive) is-active @endif">
                    @if ($isActive)
                        <div class="plan-badge">Joriy tarif</div>
                    @endif
                    <div class="plan-name">{{ $plan->name }}</div>
                    <div class="plan-price">{{ number_format($plan->price, 0, '.', ' ') }} <span>so'm / oy</span></div>
                    <div class="plan-desc">Bir vaqtning o'zida <strong>{{ $plan->max_groups }} tagacha</strong>
                        guruh ochib turish imkoni.</div>

                    <div class="plan-buy-row">
                        <form action="{{ route('student-purchases.store', ['group_plan', $plan->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="plan-buy-btn plan-buy-btn-outline">
                                <i class="bi bi-wallet2"></i> Balansdan
                            </button>
                        </form>
                        <form action="{{ route('click.pay', ['group_plan', $plan->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="plan-buy-btn">
                                <i class="bi bi-bag-check"></i> Click orqali
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <style>
        .mine-alert {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--mint-soft);
            color: var(--mint);
            border-radius: 12px;
            padding: 13px 16px;
            font-weight: 600;
            font-size: .88rem;
            margin-bottom: 20px;
        }

        .mine-alert-error {
            background: var(--coral-soft);
            color: var(--coral);
        }

        .plan-status {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--primary-soft);
            color: var(--primary);
            border-radius: 12px;
            padding: 13px 16px;
            font-weight: 600;
            font-size: .88rem;
            margin-bottom: 22px;
        }

        .plan-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 18px;
        }

        .plan-card {
            position: relative;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 24px 20px;
            box-shadow: var(--shadow-sm);
            text-align: center;
        }

        .plan-card.is-active {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-soft);
        }

        .plan-badge {
            position: absolute;
            top: -11px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--primary);
            color: #fff;
            font-size: .68rem;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 20px;
            letter-spacing: .03em;
        }

        .plan-name {
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: 1.1rem;
            margin-bottom: 8px;
        }

        .plan-price {
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .plan-price span {
            font-size: .74rem;
            font-weight: 600;
            color: var(--muted);
        }

        .plan-desc {
            color: var(--muted);
            font-size: .84rem;
            margin-bottom: 20px;
        }

        .plan-buy-row {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .plan-buy-btn {
            width: 100%;
            padding: 11px 18px;
            border-radius: 10px;
            border: none;
            background: var(--primary);
            color: #fff;
            font-weight: 700;
            font-size: .86rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .plan-buy-btn:hover {
            background: #5A4BD6;
        }

        .plan-buy-btn-outline {
            background: transparent;
            border: 1.5px solid var(--line);
            color: var(--muted);
        }

        .plan-buy-btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
        }
    </style>
@endsection
