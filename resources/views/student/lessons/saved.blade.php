@extends('layouts.student')

@section('content')
    <div class="page">
        <div class="page-head fade-up">
            <div>
                <h1>Saqlangan darslarim</h1>
                <p class="page-sub">Yoqqan darslaringiz shu yerda to'planadi.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="lessons-alert fade-up">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        <div class="lessons-tabs fade-up" style="animation-delay:.04s;">
            <a href="{{ route('student-lessons.index') }}" class="lessons-tab">
                <i class="bi bi-book"></i> Fanlar
            </a>
            <a href="{{ route('student-lessons.index', ['saved' => 1]) }}" class="lessons-tab active">
                <i class="bi bi-bookmark-heart"></i> Saqlanganlar ({{ count($savedIds) }})
            </a>
        </div>

        @if ($lessons->isEmpty())
            <div class="lessons-empty fade-up">
                <div class="lessons-empty-icon"><i class="bi bi-bookmark-heart"></i></div>
                <div class="lessons-empty-title">Hali dars saqlamagansiz</div>
                <div class="lessons-empty-sub">
                    Yoqqan darslaringizni bu yerga saqlab qo'yishingiz mumkin —
                    <a href="{{ route('student-lessons.index') }}" style="color:var(--primary);font-weight:600;">fanlarni ko'ring</a>.
                </div>
            </div>
        @else
            <div class="lessons-grid">
                @foreach ($lessons as $index => $lesson)
                    @include('student.lessons.partials._lesson_card', ['lesson' => $lesson, 'savedIds' => $savedIds, 'index' => $index])
                @endforeach
            </div>
        @endif
    </div>

    @include('student.lessons.partials._lesson_card_styles')
    <style>
        .lessons-tabs {
            display: flex;
            gap: 6px;
            background: var(--bg-soft);
            padding: 4px;
            border-radius: 12px;
            width: fit-content;
            margin-bottom: 20px;
        }

        .lessons-tab {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 14px;
            border-radius: 9px;
            font-size: .84rem;
            font-weight: 700;
            color: var(--muted);
        }

        .lessons-tab.active {
            background: var(--card);
            color: var(--primary);
            box-shadow: var(--shadow-sm);
        }
    </style>
@endsection
