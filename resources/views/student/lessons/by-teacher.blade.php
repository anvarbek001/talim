@extends('layouts.student')

@section('content')
    <div class="page">
        <a href="{{ route('student-lessons.teachers', $science) }}" class="back-link fade-up">
            <i class="bi bi-arrow-left"></i> O'qituvchilarga qaytish
        </a>

        <div class="page-head fade-up teacher-head">
            <div class="teacher-head-avatar">
                @if ($teacher->avatarUrl())
                    <img src="{{ $teacher->avatarUrl() }}" alt="{{ $teacher->name }}">
                @else
                    {{ $teacher->initials() }}
                @endif
            </div>
            <div>
                <h1>{{ $teacher->name }}</h1>
                <p class="page-sub">{{ $science->title }} fanidan video darslar — sinf va bo'limlar bo'yicha.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('student-lessons.by-teacher', [$science, $teacher]) }}" class="science-filter-bar fade-up">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Dars nomi bo'yicha qidirish..." class="science-filter-input">
            <button type="submit" class="science-filter-btn"><i class="bi bi-search"></i> Qidirish</button>
            @if (request('q'))
                <a href="{{ route('student-lessons.by-teacher', [$science, $teacher]) }}" class="science-filter-reset">Tozalash</a>
            @endif
        </form>

        @if ($grouped->isEmpty())
            <div class="lessons-empty fade-up">
                <div class="lessons-empty-icon"><i class="bi bi-camera-reels"></i></div>
                @if (request('q'))
                    <div class="lessons-empty-title">"{{ request('q') }}" bo'yicha dars topilmadi</div>
                @else
                    <div class="lessons-empty-title">Hozircha dars yo'q</div>
                @endif
            </div>
        @else
            @foreach ($grouped as $gradeTitle => $gradeLessons)
                <div class="grade-block fade-up">
                    <div class="grade-heading"><i class="bi bi-mortarboard"></i> {{ $gradeTitle }}</div>

                    @foreach ($gradeLessons->groupBy(fn ($l) => $l->section->title ?? '—') as $sectionTitle => $sectionLessons)
                        <div class="section-block">
                            <div class="section-heading"><i class="bi bi-collection"></i> {{ $sectionTitle }}</div>
                            <div class="lessons-grid">
                                @foreach ($sectionLessons as $index => $lesson)
                                    @include('student.lessons.partials._lesson_card', ['lesson' => $lesson, 'savedIds' => $savedIds, 'index' => $index])
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        @endif
    </div>

    @include('student.lessons.partials._lesson_card_styles')
    <style>
        .teacher-head {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .teacher-head-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), #9C8CFF);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            font-family: 'Sora', sans-serif;
            overflow: hidden;
            flex-shrink: 0;
        }

        .teacher-head-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .grade-block {
            margin-bottom: 28px;
        }

        .grade-heading {
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: 1.05rem;
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--line);
        }

        .section-block {
            margin-bottom: 20px;
        }

        .section-block:last-child {
            margin-bottom: 0;
        }

        .section-heading {
            display: flex;
            align-items: center;
            gap: 7px;
            font-weight: 700;
            font-size: .84rem;
            color: var(--muted);
            margin-bottom: 12px;
        }

        .science-filter-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .science-filter-input {
            flex: 1 1 260px;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: .86rem;
            background: var(--card);
            color: var(--text);
        }

        .science-filter-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            border-radius: 10px;
            padding: 10px 18px;
            background: var(--primary);
            color: #fff;
            font-weight: 700;
            font-size: .84rem;
        }

        .science-filter-reset {
            display: inline-flex;
            align-items: center;
            font-size: .84rem;
            font-weight: 600;
            color: var(--muted);
        }

        .science-filter-reset:hover {
            color: var(--coral);
        }
    </style>
@endsection
