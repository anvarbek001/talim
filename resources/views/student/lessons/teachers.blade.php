@extends('layouts.student')

@section('content')
    <div class="page">
        <a href="{{ route('student-lessons.index') }}" class="back-link fade-up">
            <i class="bi bi-arrow-left"></i> Fanlarga qaytish
        </a>

        <div class="page-head fade-up">
            <div>
                <h1>{{ $science->title }}</h1>
                <p class="page-sub">Shu fandan dars beruvchi o'qituvchini tanlang.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="teacher-alert fade-up"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="teacher-alert teacher-alert-error fade-up"><i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}</div>
        @endif

        @if ($teachers->isEmpty())
            <div class="lessons-empty fade-up">
                <div class="lessons-empty-icon"><i class="bi bi-person-video3"></i></div>
                <div class="lessons-empty-title">Hozircha o'qituvchi yo'q</div>
                <div class="lessons-empty-sub">Yaqin orada bu fandan darslar joylanadi.</div>
            </div>
        @else
            <div class="teacher-grid">
                @foreach ($teachers as $index => $teacher)
                    @php $isSubscribed = in_array($teacher->id, $subscribedTeacherIds, true); @endphp
                    <div class="teacher-card fade-up" style="animation-delay:{{ min($index * 0.05, 0.3) }}s;">
                        <a href="{{ route('student-lessons.by-teacher', [$science, $teacher]) }}" class="teacher-card-link">
                            <div class="teacher-avatar">
                                @if ($teacher->avatarUrl())
                                    <img src="{{ $teacher->avatarUrl() }}" alt="{{ $teacher->name }}">
                                @else
                                    {{ $teacher->initials() }}
                                @endif
                            </div>
                            <div class="teacher-name">{{ $teacher->name }}</div>
                            <div class="teacher-count">{{ $teacher->lessons_count }} ta dars</div>
                        </a>

                        @if ($isSubscribed)
                            <div class="teacher-sub-badge teacher-sub-badge-active">
                                <i class="bi bi-patch-check-fill"></i> Obuna faol
                            </div>
                        @elseif ($teacher->subscription_price > 0)
                            <form action="{{ route('student-purchases.store', ['teacher', $teacher->id]) }}" method="POST" class="teacher-sub-form">
                                @csrf
                                <button type="submit" class="teacher-sub-btn">
                                    <i class="bi bi-stars"></i>
                                    Obuna — {{ number_format($teacher->subscription_price, 0, '.', ' ') }} so'm/oy
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @include('student.lessons.partials._lesson_card_styles')
    <style>
        .teacher-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
            gap: 16px;
        }

        .teacher-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 22px 18px;
            box-shadow: var(--shadow-sm);
            transition: transform .25s ease, box-shadow .25s ease;
            text-align: center;
        }

        .teacher-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow);
        }

        .teacher-card-link {
            display: block;
            color: inherit;
            text-decoration: none;
        }

        .teacher-sub-form {
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px solid var(--line);
        }

        .teacher-sub-btn {
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 9px 10px;
            border-radius: 9px;
            border: 1.5px solid var(--primary);
            background: var(--primary-soft);
            color: var(--primary);
            font-weight: 700;
            font-size: .76rem;
            text-align: center;
            transition: .2s;
        }

        .teacher-sub-btn:hover {
            background: var(--primary);
            color: #fff;
        }

        .teacher-sub-badge {
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px solid var(--line);
            font-size: .76rem;
            font-weight: 700;
        }

        .teacher-sub-badge-active {
            color: var(--mint);
        }

        .teacher-alert {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            border-radius: 12px;
            font-weight: 600;
            font-size: .87rem;
            margin-bottom: 16px;
            background: var(--mint-soft);
            color: var(--mint);
        }

        .teacher-alert-error {
            background: var(--coral-soft);
            color: var(--coral);
        }

        .teacher-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), #9C8CFF);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.15rem;
            font-family: 'Sora', sans-serif;
            margin: 0 auto 14px;
            overflow: hidden;
        }

        .teacher-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .teacher-name {
            font-weight: 700;
            font-size: .92rem;
            color: var(--text);
            margin-bottom: 4px;
        }

        .teacher-count {
            font-size: .76rem;
            color: var(--muted);
        }
    </style>
@endsection
