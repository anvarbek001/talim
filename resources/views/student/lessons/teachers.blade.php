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

        @if ($teachers->isEmpty())
            <div class="lessons-empty fade-up">
                <div class="lessons-empty-icon"><i class="bi bi-person-video3"></i></div>
                <div class="lessons-empty-title">Hozircha o'qituvchi yo'q</div>
                <div class="lessons-empty-sub">Yaqin orada bu fandan darslar joylanadi.</div>
            </div>
        @else
            <div class="teacher-grid">
                @foreach ($teachers as $index => $teacher)
                    <a href="{{ route('student-lessons.by-teacher', [$science, $teacher]) }}" class="teacher-card fade-up" style="animation-delay:{{ min($index * 0.05, 0.3) }}s;">
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
