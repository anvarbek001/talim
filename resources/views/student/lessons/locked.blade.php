@extends('layouts.student')

@section('content')
    <div class="page">
        <a href="{{ url()->previous() }}" class="back-link fade-up">
            <i class="bi bi-arrow-left"></i> Orqaga
        </a>

        <div class="card fade-up locked-card">
            <div class="locked-video" style="background:linear-gradient(135deg,{{ $lesson->science->color ?? '#6C5CE7' }},#9C8CFF);">
                <i class="bi bi-lock-fill"></i>
            </div>
            <div class="locked-body">
                <h1>{{ $lesson->title }}</h1>
                <p class="locked-desc">
                    Bu darsni ko'rish uchun obuna kerak. Har bir fandan dastlabki {{ \App\Models\Lesson::FREE_PREVIEW_COUNT }} ta dars — tekin.
                </p>

                <div class="plans-grid">
                    @foreach ($plans as $key => $plan)
                        <div class="plan-card {{ $key === 'monthly' ? 'is-popular' : '' }}">
                            @if ($key === 'monthly')
                                <div class="plan-badge">Mashhur</div>
                            @endif
                            <div class="plan-label">{{ $plan['label'] }}</div>
                            <div class="plan-price">{{ number_format($plan['price'], 0, '.', ' ') }} <span>so'm</span></div>
                            <form action="{{ route('student-subscription.subscribe') }}" method="POST">
                                @csrf
                                <input type="hidden" name="plan" value="{{ $key }}">
                                <button type="submit" class="plan-btn">Faollashtirish</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @include('student.lessons.partials._lesson_card_styles')
    <style>
        .locked-card {
            padding: 0;
            overflow: hidden;
        }

        .locked-video {
            width: 100%;
            aspect-ratio: 21 / 9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 2.4rem;
        }

        .locked-body {
            padding: 26px 24px 30px;
            text-align: center;
        }

        .locked-body h1 {
            font-size: 1.3rem;
            margin: 0 0 8px;
        }

        .locked-desc {
            color: var(--muted);
            font-size: .88rem;
            max-width: 480px;
            margin: 0 auto 24px;
        }

        .plans-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            max-width: 720px;
            margin: 0 auto;
        }

        @media (max-width:700px) {
            .plans-grid {
                grid-template-columns: 1fr;
            }
        }

        .plan-card {
            position: relative;
            background: var(--bg-soft);
            border: 1.5px solid var(--line);
            border-radius: 16px;
            padding: 20px 16px;
        }

        .plan-card.is-popular {
            border-color: var(--primary);
        }

        .plan-badge {
            position: absolute;
            top: -11px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--primary);
            color: #fff;
            font-size: .64rem;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 20px;
            text-transform: uppercase;
        }

        .plan-label {
            font-weight: 700;
            font-size: .88rem;
            color: var(--muted);
            margin-bottom: 8px;
        }

        .plan-price {
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: 1.3rem;
            margin-bottom: 14px;
        }

        .plan-price span {
            font-size: .72rem;
            font-weight: 600;
            color: var(--muted);
        }

        .plan-btn {
            width: 100%;
            padding: 10px;
            border-radius: 9px;
            border: none;
            background: var(--primary);
            color: #fff;
            font-weight: 700;
            font-size: .82rem;
        }

        .plan-btn:hover {
            background: #5A4BD6;
        }
    </style>
@endsection
