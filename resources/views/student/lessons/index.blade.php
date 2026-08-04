@extends('layouts.student')

@section('content')
    <div class="page">
        <div class="page-head fade-up">
            <div>
                <h1>Darslarim</h1>
                <p class="page-sub">Fanni tanlang — o'qituvchilarni va ularning video darslarini ko'rasiz.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="lessons-alert fade-up">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        <div class="lessons-tabs fade-up" style="animation-delay:.04s;">
            <a href="{{ route('student-lessons.index') }}" class="lessons-tab active">
                <i class="bi bi-book"></i> Fanlar
            </a>
            <a href="{{ route('student-lessons.index', ['saved' => 1]) }}" class="lessons-tab">
                <i class="bi bi-bookmark-heart"></i> Saqlanganlar
            </a>
        </div>

        <form method="GET" action="{{ route('student-lessons.index') }}" class="science-filter-bar fade-up" style="animation-delay:.05s;">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Fan nomi bo'yicha qidirish..." class="science-filter-input">
            <button type="submit" class="science-filter-btn"><i class="bi bi-search"></i> Qidirish</button>
            @if (request('q'))
                <a href="{{ route('student-lessons.index') }}" class="science-filter-reset">Tozalash</a>
            @endif
        </form>

        @if ($sciences->isEmpty())
            <div class="lessons-empty fade-up">
                <div class="lessons-empty-icon"><i class="bi bi-camera-reels"></i></div>
                @if (request('q'))
                    <div class="lessons-empty-title">"{{ request('q') }}" bo'yicha fan topilmadi</div>
                    <div class="lessons-empty-sub">Boshqa nom bilan qidirib ko'ring.</div>
                @else
                    <div class="lessons-empty-title">Hozircha dars yo'q</div>
                    <div class="lessons-empty-sub">Yaqin orada yangi darslar joylanadi.</div>
                @endif
            </div>
        @else
            <div class="science-grid">
                @foreach ($sciences as $index => $science)
                    <a href="{{ route('student-lessons.teachers', $science) }}" class="science-card fade-up" style="animation-delay:{{ min($index * 0.04, 0.3) }}s;">
                        <div class="science-icon" style="background:{{ $science->color ?? '#6C5CE7' }}1A;color:{{ $science->color ?? '#6C5CE7' }};">
                            <i class="bi {{ $science->icon ?? 'bi-book' }}"></i>
                        </div>
                        <div class="science-title">{{ $science->title }}</div>
                        <div class="science-count">{{ $science->lessons_count }} ta dars</div>
                    </a>
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

        .science-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
            gap: 16px;
        }

        .science-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 22px 18px;
            box-shadow: var(--shadow-sm);
            transition: transform .25s ease, box-shadow .25s ease;
            text-align: center;
        }

        .science-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow);
        }

        .science-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin: 0 auto 14px;
        }

        .science-title {
            font-weight: 700;
            font-size: .94rem;
            color: var(--text);
            margin-bottom: 4px;
        }

        .science-count {
            font-size: .76rem;
            color: var(--muted);
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
