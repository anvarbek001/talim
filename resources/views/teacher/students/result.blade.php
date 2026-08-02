@extends('layouts.teacher')

@php
    $mcqAnswers = $attempt->answers->filter(fn($a) => $a->questionable_type !== \App\Models\SertifikatTestWrittenQuestion::class);
    $writtenAnswers = $attempt->answers->filter(fn($a) => $a->questionable_type === \App\Models\SertifikatTestWrittenQuestion::class);
    $mcqScore = round($mcqAnswers->sum('score'), 2);
    $mcqMax = round($mcqAnswers->sum('max_score'), 2);
    $pending = $attempt->hasPendingGrading();
    $student = $attempt->user;
@endphp

@section('content')
    <div class="page">
        <a href="{{ route('teacher-students.index') }}" class="card-link back-link"><i class="bi bi-arrow-left"></i> O'quvchilar ro'yxatiga qaytish</a>

        <div class="page-head fade-up">
            <div class="student-head-chip">
                <div class="student-avatar">
                    @if ($student?->avatarUrl())
                        <img src="{{ $student->avatarUrl() }}" alt="{{ $student->name }}">
                    @else
                        {{ $student?->initials() }}
                    @endif
                </div>
                <div>
                    <h1>{{ $attempt->testable->title ?? "O'chirilgan test" }}</h1>
                    <p class="page-sub">{{ $student->name ?? "O'chirilgan foydalanuvchi" }} natijasi</p>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert-success fade-up"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert-error fade-up"><i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}</div>
        @endif

        <div class="card fade-up score-card" style="animation-delay:.04s;">
            @if ($pending)
                <div class="score-pending">
                    <div class="score-pending-icon"><i class="bi bi-hourglass-split"></i></div>
                    <div>
                        <div class="score-pending-title">Yozma qism baholanmoqda</div>
                        <div class="score-pending-sub">Test testlari (MCQ): <strong>{{ $mcqScore }} / {{ $mcqMax }}</strong> — yozma javoblarni baholab, yakuniy ball chiqadi.</div>
                    </div>
                </div>
            @else
                <div class="score-final">
                    <div class="score-ring" style="--p: {{ $attempt->max_score > 0 ? round($attempt->score / $attempt->max_score * 100) : 0 }};">
                        <span>{{ $attempt->max_score > 0 ? round($attempt->score / $attempt->max_score * 100) : 0 }}%</span>
                    </div>
                    <div>
                        <div class="score-final-num">{{ $attempt->score }} / {{ $attempt->max_score }}</div>
                        <div class="score-final-sub">Umumiy ball</div>
                    </div>
                </div>
            @endif
        </div>

        <div class="card fade-up" style="animation-delay:.1s;">
            <div class="card-head">
                <div class="card-title">Savollar bo'yicha natija</div>
            </div>

            @foreach ($mcqAnswers as $answer)
                @php
                    $question = $answer->questionable;
                    $selected = $question?->options->firstWhere('id', $answer->selected_option_id);
                    $correct = $question?->options->firstWhere('is_correct', true);
                    $isCorrect = (bool) $answer->score;
                @endphp
                <div class="review-block {{ $isCorrect ? 'is-correct' : 'is-wrong' }}">
                    <div class="review-icon"><i class="bi {{ $isCorrect ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i></div>
                    <div class="review-body">
                        <div class="review-question">{{ $loop->iteration }}. {{ $question?->question }}</div>
                        <div class="review-answer">O'quvchi javobi: <strong>{{ $selected?->option_text ?? 'Javob berilmagan' }}</strong></div>
                        @unless ($isCorrect)
                            <div class="review-correct">To'g'ri javob: <strong>{{ $correct?->option_text }}</strong></div>
                        @endunless
                    </div>
                </div>
            @endforeach

            @if ($writtenAnswers->isNotEmpty())
                <div class="written-review-head"><i class="bi bi-pencil-square"></i> Yozma qism</div>
                @foreach ($writtenAnswers as $answer)
                    <div class="review-block is-written">
                        <div class="review-icon"><i class="bi bi-pencil-square"></i></div>
                        <div class="review-body">
                            <div class="review-question">{{ $answer->questionable?->question }}</div>
                            <div class="review-answer-text">{{ $answer->answer_text ?: 'Javob berilmagan' }}</div>
                            @if ($answer->isGraded())
                                <div class="review-score">Ball: <strong>{{ $answer->score }} / {{ $answer->max_score }}</strong></div>
                            @else
                                <form action="{{ route('teacher-students.grade', [$attempt, $answer]) }}" method="POST" class="grade-form">
                                    @csrf
                                    <label class="grade-label">Ball (maks. {{ $answer->max_score }})</label>
                                    <input type="number" name="score" class="text-control grade-score-input" min="0"
                                        max="{{ $answer->max_score }}" required>
                                    <button type="submit" class="btn-primary">
                                        <i class="bi bi-check-circle"></i> Baholash
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <style>
        .page-head {
            margin-bottom: 20px;
        }

        .student-head-chip {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .student-avatar {
            width: 48px;
            height: 48px;
            border-radius: 14px;
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

        .page-head h1 {
            font-size: 1.3rem;
            margin: 0 0 4px;
        }

        .page-sub {
            color: var(--muted);
            font-size: .86rem;
            margin: 0;
        }

        .score-card {
            display: flex;
        }

        .score-pending {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .score-pending-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: var(--amber-soft);
            color: #8A6100;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .score-pending-title {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 4px;
        }

        .score-pending-sub {
            font-size: .84rem;
            color: var(--muted);
        }

        .score-final {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .score-ring {
            --p: 0;
            width: 84px;
            height: 84px;
            border-radius: 50%;
            background: conic-gradient(var(--mint) calc(var(--p) * 3.6deg), var(--line) 0deg);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            flex-shrink: 0;
        }

        .score-ring::before {
            content: "";
            position: absolute;
            inset: 8px;
            background: var(--card);
            border-radius: 50%;
        }

        .score-ring span {
            position: relative;
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: 1rem;
        }

        .score-final-num {
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: 1.5rem;
        }

        .score-final-sub {
            color: var(--muted);
            font-size: .84rem;
        }

        .review-block {
            display: flex;
            gap: 12px;
            padding: 14px 0;
            border-bottom: 1px solid var(--line);
        }

        .review-block:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .review-icon {
            width: 30px;
            height: 30px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1rem;
        }

        .is-correct .review-icon {
            background: var(--mint-soft);
            color: var(--mint);
        }

        .is-wrong .review-icon {
            background: var(--coral-soft);
            color: var(--coral);
        }

        .is-written .review-icon {
            background: var(--amber-soft);
            color: #8A6100;
        }

        .review-question {
            font-weight: 700;
            font-size: .9rem;
            margin-bottom: 6px;
        }

        .review-answer,
        .review-correct {
            font-size: .82rem;
            color: var(--muted);
        }

        .review-answer-text {
            font-size: .84rem;
            background: var(--bg-soft);
            border-radius: 8px;
            padding: 8px 10px;
            margin: 4px 0;
            white-space: pre-wrap;
        }

        .review-score {
            font-size: .82rem;
            color: var(--text);
            margin-top: 4px;
        }

        .review-score.pending {
            color: #8A6100;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .written-review-head {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            font-size: .9rem;
            color: #8A6100;
            padding: 14px 0 4px;
        }

        .alert-success {
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

        .alert-error {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--coral-soft);
            color: var(--coral);
            border-radius: 12px;
            padding: 13px 16px;
            font-weight: 600;
            font-size: .88rem;
            margin-bottom: 20px;
        }

        .grade-form {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 8px;
        }

        .grade-label {
            font-size: .78rem;
            font-weight: 600;
            color: var(--muted);
            display: block;
            margin-bottom: 6px;
        }

        .grade-score-input {
            width: 100px;
        }

        .text-control {
            background: var(--bg-soft);
            border: 1.5px solid var(--line);
            border-radius: 10px;
            padding: 10px 12px;
            font-family: 'Inter', sans-serif;
            font-size: .88rem;
            color: var(--text);
        }

        .text-control:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--card);
            box-shadow: 0 0 0 3px var(--primary-soft);
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            background: var(--primary);
            color: #fff;
            font-weight: 700;
            font-size: .86rem;
        }

        .btn-primary:hover {
            background: #5A4BD6;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 14px;
        }

        @media (max-width:767px) {
            .page-head h1 {
                font-size: 1.1rem;
            }

            .card {
                padding: 16px;
            }

            .score-pending {
                flex-direction: column;
                align-items: flex-start;
                text-align: left;
                gap: 12px;
            }

            .score-final {
                gap: 14px;
            }

            .score-ring {
                width: 68px;
                height: 68px;
            }

            .score-final-num {
                font-size: 1.25rem;
            }

            .review-block {
                gap: 10px;
            }

            .review-question {
                font-size: .87rem;
            }
        }
    </style>
@endsection
