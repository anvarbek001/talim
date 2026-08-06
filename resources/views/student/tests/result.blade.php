@extends('layouts.student')

@php
    $writtenTypes = [\App\Models\SertifikatTestWrittenQuestion::class, \App\Models\LanguageExamTestWrittenQuestion::class];
    $mcqAnswers = $attempt->answers->filter(fn($a) => ! in_array($a->questionable_type, $writtenTypes, true));
    $writtenAnswers = $attempt->answers->filter(fn($a) => in_array($a->questionable_type, $writtenTypes, true));
    $mcqScore = round($mcqAnswers->sum('score'), 2);
    $mcqMax = round($mcqAnswers->sum('max_score'), 2);
    $pending = $attempt->hasPendingGrading();
@endphp

@section('content')
    <div class="page">
        <div class="page-head fade-up">
            <h1>{{ $attempt->testable->title ?? "O'chirilgan test" }}</h1>
            <p class="page-sub">Natijalaringiz</p>
        </div>

        <div class="card fade-up score-card" style="animation-delay:.04s;">
            @if ($pending)
                <div class="score-pending">
                    <div class="score-pending-icon"><i class="bi bi-hourglass-split"></i></div>
                    <div>
                        <div class="score-pending-title">Yozma qism baholanmoqda</div>
                        <div class="score-pending-sub">Test testlari (MCQ): <strong>{{ $mcqScore }} / {{ $mcqMax }}</strong> — yozma javoblaringiz o'qituvchi tomonidan ko'rib chiqilgach yakuniy ball chiqadi.</div>
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
                        <div class="review-answer">Sizning javobingiz: <strong>{{ $selected?->option_text ?? 'Javob berilmagan' }}</strong></div>
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
                                <div class="review-score pending"><i class="bi bi-hourglass-split"></i> Hali baholanmagan</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <a href="{{ route('student-tests.index') }}" class="card-link back-link"><i class="bi bi-arrow-left"></i> Testlar ro'yxatiga qaytish</a>
    </div>

    <style>
        .page-head {
            margin-bottom: 20px;
        }

        .page-head h1 {
            font-size: 1.4rem;
            margin: 4px 0 4px;
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

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 4px;
        }

        @media (max-width:767px) {
            .page-head h1 {
                font-size: 1.15rem;
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
