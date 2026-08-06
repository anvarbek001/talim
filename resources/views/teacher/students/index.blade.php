@extends('layouts.teacher')

@php
    $typeLabel = fn ($class) => match ($class) {
        \App\Models\TopicTest::class => 'Mavzu testi',
        \App\Models\DtmTest::class => 'DTM testi',
        \App\Models\SertifikatTest::class => 'Sertifikat testi',
        \App\Models\LanguageExamTest::class => 'Til imtihoni',
        default => 'Test',
    };
    $pendingAttempts = $students->flatMap(fn ($row) => $row['attempts'])->filter(fn ($a) => $a->hasPendingGrading());
    $pendingGradingCount = $pendingAttempts->count();
    $firstPendingAttempt = $pendingAttempts->first();
@endphp

@section('content')
    <div class="page">
        <div class="page-head fade-up">
            <div>
                <h1>O'quvchilarim</h1>
                <p class="page-sub">Testlaringizni yechgan barcha o'quvchilar va ularning natijalari shu yerda.</p>
            </div>
        </div>

        @if ($students->isEmpty())
            <div class="students-empty fade-up">
                <div class="students-empty-icon"><i class="bi bi-mortarboard"></i></div>
                <div class="students-empty-title">Hozircha natija yo'q</div>
                <div class="students-empty-sub">O'quvchilar testlaringizni yechishi bilan ular shu yerda ko'rinadi.</div>
            </div>
        @else
            <div class="students-summary fade-up">
                <div class="summary-chip"><i class="bi bi-people"></i> {{ $students->count() }} ta o'quvchi</div>
                <div class="summary-chip"><i class="bi bi-patch-question"></i> {{ $students->sum('attempts_count') }} ta urinish</div>
                @if ($pendingGradingCount > 0)
                    <a href="{{ route('teacher-students.result', $firstPendingAttempt) }}" class="summary-chip summary-chip-alert">
                        <i class="bi bi-pencil-square"></i> {{ $pendingGradingCount }} ta yozma javob baholash kerak
                        <i class="bi bi-arrow-right"></i>
                    </a>
                @endif
            </div>

            <div class="students-list">
                @foreach ($students as $index => $row)
                    @php
                        $student = $row['student'];
                    @endphp
                    <div class="student-card fade-up" style="animation-delay:{{ min($index * 0.05, 0.3) }}s;">
                        <div class="student-head" data-toggle-attempts>
                            <div class="student-avatar">
                                @if ($student->avatarUrl())
                                    <img src="{{ $student->avatarUrl() }}" alt="{{ $student->name }}">
                                @else
                                    {{ $student->initials() }}
                                @endif
                            </div>
                            <div class="student-info">
                                <div class="student-name">{{ $student->name }}</div>
                                <div class="student-email">{{ $student->email }}</div>
                            </div>
                            <div class="student-stats">
                                <div class="student-stat">
                                    <div class="student-stat-num">{{ $row['attempts_count'] }}</div>
                                    <div class="student-stat-lbl">urinish</div>
                                </div>
                                <div class="student-stat">
                                    <div class="student-stat-num">{{ $row['average_percent'] }}%</div>
                                    <div class="student-stat-lbl">o'rtacha</div>
                                </div>
                            </div>
                            <button type="button" class="toggle-btn">
                                <i class="bi bi-chevron-down"></i>
                            </button>
                        </div>

                        <div class="student-attempts">
                            @foreach ($row['attempts'] as $attempt)
                                <a href="{{ route('teacher-students.result', $attempt) }}" class="attempt-row">
                                    <span class="attempt-type">{{ $typeLabel($attempt->testable_type) }}</span>
                                    <div class="attempt-title">{{ $attempt->testable->title ?? "O'chirilgan test" }}</div>
                                    <div class="attempt-score">
                                        @if ($attempt->hasPendingGrading())
                                            <span class="pending-badge"><i class="bi bi-pencil-square"></i> Baholash</span>
                                        @else
                                            {{ $attempt->score }} / {{ $attempt->max_score }}
                                        @endif
                                    </div>
                                    <div class="attempt-date">{{ $attempt->submitted_at?->format('d.m.Y H:i') }}</div>
                                    <i class="bi bi-chevron-right attempt-arrow"></i>
                                </a>
                            @endforeach
                        </div>
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

        .students-empty {
            background: var(--card);
            border: 1px dashed var(--line);
            border-radius: 18px;
            padding: 56px 24px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }

        .students-empty-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: var(--primary-soft);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 8px;
        }

        .students-empty-title {
            font-weight: 700;
            font-size: 1.05rem;
        }

        .students-empty-sub {
            color: var(--muted);
            font-size: .86rem;
            max-width: 360px;
        }

        .students-summary {
            display: flex;
            gap: 10px;
            margin-bottom: 18px;
        }

        .summary-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--bg-soft);
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 8px 16px;
            font-size: .82rem;
            font-weight: 700;
            color: var(--muted);
        }

        .summary-chip-alert {
            background: var(--coral-soft);
            border-color: transparent;
            color: var(--coral);
            text-decoration: none;
            cursor: pointer;
            transition: background .15s;
        }

        .summary-chip-alert:hover {
            background: var(--coral);
            color: #fff;
        }

        .students-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .student-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .student-head {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 18px;
            cursor: pointer;
        }

        .student-avatar {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), #9C8CFF);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-family: 'Sora', sans-serif;
            flex-shrink: 0;
            overflow: hidden;
        }

        .student-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .student-info {
            flex: 1;
            min-width: 0;
        }

        .student-name {
            font-weight: 700;
            font-size: .94rem;
        }

        .student-email {
            font-size: .76rem;
            color: var(--muted);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .student-stats {
            display: flex;
            gap: 18px;
            flex-shrink: 0;
        }

        .student-stat {
            text-align: center;
        }

        .student-stat-num {
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: 1.05rem;
        }

        .student-stat-lbl {
            font-size: .68rem;
            color: var(--muted);
        }

        .toggle-btn {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: none;
            background: var(--bg-soft);
            color: var(--muted);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform .2s;
            flex-shrink: 0;
        }

        .student-card.is-open .toggle-btn {
            transform: rotate(180deg);
        }

        .student-attempts {
            display: none;
            padding: 0 18px 16px;
            border-top: 1px solid var(--line);
        }

        .student-card.is-open .student-attempts {
            display: block;
            padding-top: 12px;
        }

        .attempt-row {
            display: grid;
            grid-template-columns: 110px 1fr 100px 130px 18px;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px solid var(--line);
            font-size: .84rem;
            color: inherit;
            text-decoration: none;
            transition: background .15s;
            border-radius: 8px;
        }

        .attempt-row:hover {
            background: var(--bg-soft);
        }

        .attempt-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .attempt-arrow {
            color: var(--muted);
            font-size: .9rem;
            justify-self: end;
        }

        .attempt-type {
            display: inline-flex;
            align-items: center;
            font-size: .68rem;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 20px;
            background: var(--primary-soft);
            color: var(--primary);
            width: fit-content;
        }

        .attempt-title {
            font-weight: 600;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .attempt-score {
            font-weight: 700;
            text-align: right;
        }

        .pending-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: var(--coral);
            color: #fff;
            font-weight: 700;
            font-size: .74rem;
            padding: 5px 11px;
            border-radius: 20px;
            white-space: nowrap;
        }

        .attempt-date {
            font-size: .76rem;
            color: var(--muted);
            text-align: right;
        }

        @media (max-width:640px) {
            .student-stats {
                display: none;
            }

            .attempt-row {
                grid-template-columns: 1fr 90px 18px;
            }

            .attempt-type,
            .attempt-date {
                display: none;
            }
        }
    </style>
@endsection

@section('scripts')
    <script>
        document.querySelectorAll('[data-toggle-attempts]').forEach(btn => {
            btn.addEventListener('click', () => {
                btn.closest('.student-card').classList.toggle('is-open');
            });
        });
    </script>
@endsection
