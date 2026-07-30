@extends('layouts.student')

@php
    $testable = $attempt->testable;
    $deadline = $attempt->started_at->copy()->addMinutes($testable->duration_minutes);
    $remainingSeconds = max(0, (int) now()->diffInSeconds($deadline, false));
@endphp

@section('content')
    <div class="page take-page">
        <div class="take-head fade-up" id="takeHead">
            <div class="take-head-top">
                <div>
                    <div class="take-eyebrow">Test yechilmoqda</div>
                    <h1>{{ $testable->title }}</h1>
                </div>
                <div class="timer-box" id="timerBox">
                    <i class="bi bi-stopwatch"></i>
                    <span id="timerText">--:--</span>
                </div>
            </div>
            <div class="progress-row">
                <div class="progress-track">
                    <div class="progress-fill" id="progressFill"></div>
                </div>
                <span class="progress-label" id="progressLabel">0 / {{ $testable->questions->count() }} javob berildi</span>
            </div>
        </div>

        <form action="{{ route('student-tests.submit', $attempt) }}" method="POST" id="takeTestForm">
            @csrf

            @foreach ($testable->questions as $question)
                <div class="card fade-up q-card" style="animation-delay:{{ min($loop->index * 0.03, 0.3) }}s;">
                    <div class="q-num">{{ $loop->iteration }}-savol / {{ $testable->questions->count() }}</div>
                    <div class="q-text">{{ $question->question }}</div>
                    <div class="q-options">
                        @foreach ($question->options as $option)
                            <label class="q-option">
                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" required class="q-answer-input">
                                <span>{{ $option->option_text }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            @if ($testable instanceof \App\Models\SertifikatTest && $testable->writtenQuestions->isNotEmpty())
                <div class="card fade-up q-card" style="animation-delay:.32s;">
                    <div class="q-written-head"><i class="bi bi-pencil-square"></i> Yozma qism</div>
                    @foreach ($testable->writtenQuestions as $writtenQuestion)
                        <div class="q-written-block">
                            <div class="q-num">{{ $loop->iteration }}-yozma savol <span class="q-written-score">({{ $writtenQuestion->max_score }} ball)</span></div>
                            <div class="q-text">{{ $writtenQuestion->question }}</div>
                            <textarea name="written_answers[{{ $writtenQuestion->id }}]" class="text-control" rows="4"
                                placeholder="Javobingizni shu yerga yozing..."></textarea>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="form-actions fade-up">
                <button type="submit" class="btn-primary" id="submitBtn">
                    <i class="bi bi-check-circle"></i> Testni yakunlash
                </button>
            </div>
        </form>
    </div>

    <style>
        .take-page {
            padding-bottom: 96px;
        }

        .take-head {
            position: sticky;
            top: 87px;
            z-index: 20;
            background: var(--bg);
            padding: 6px 0 14px;
            margin-bottom: 8px;
        }

        .take-head-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .take-eyebrow {
            font-size: .72rem;
            letter-spacing: .09em;
            text-transform: uppercase;
            color: var(--primary);
            font-weight: 700;
        }

        .take-head h1 {
            font-size: 1.35rem;
            margin: 4px 0 0;
            line-height: 1.3;
        }

        .timer-box {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--card);
            border: 1.5px solid var(--line);
            border-radius: 12px;
            padding: 10px 16px;
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: 1.1rem;
            color: var(--text);
            box-shadow: var(--shadow-sm);
            flex-shrink: 0;
        }

        .timer-box.danger {
            border-color: var(--coral);
            color: var(--coral);
            animation: timerPulse 1s ease-in-out infinite;
        }

        @keyframes timerPulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.04);
            }
        }

        .progress-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 12px;
        }

        .progress-track {
            flex: 1;
            height: 8px;
            background: var(--bg-soft);
            border: 1px solid var(--line);
            border-radius: 20px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, var(--mint), #33D6A0);
            border-radius: 20px;
            transition: width .25s ease;
        }

        .progress-label {
            font-size: .74rem;
            font-weight: 700;
            color: var(--muted);
            white-space: nowrap;
            flex-shrink: 0;
        }

        .q-card {
            margin-bottom: 16px;
            scroll-margin-top: 120px;
        }

        .q-num {
            font-size: .76rem;
            font-weight: 700;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: .04em;
            margin-bottom: 8px;
        }

        .q-text {
            font-weight: 700;
            font-size: .98rem;
            margin-bottom: 14px;
            line-height: 1.45;
        }

        .q-options {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .q-option {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 14px;
            min-height: 48px;
            border-radius: 12px;
            border: 1.5px solid var(--line);
            background: var(--bg-soft);
            cursor: pointer;
            font-size: .9rem;
            line-height: 1.4;
            transition: .15s;
            -webkit-tap-highlight-color: transparent;
        }

        .q-option:active {
            transform: scale(.99);
        }

        .q-option:hover {
            border-color: var(--primary);
        }

        .q-option input {
            accent-color: var(--primary);
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        .q-option:has(input:checked) {
            border-color: var(--primary);
            background: var(--primary-soft);
            font-weight: 600;
        }

        .q-written-head {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            font-size: .96rem;
            color: #8A6100;
            margin-bottom: 12px;
        }

        .q-written-block {
            margin-bottom: 16px;
        }

        .q-written-block:last-child {
            margin-bottom: 0;
        }

        .q-written-score {
            color: var(--muted);
            font-weight: 600;
            text-transform: none;
            letter-spacing: 0;
        }

        .text-control {
            background: var(--bg-soft);
            border: 1.5px solid var(--line);
            border-radius: 10px;
            padding: 12px;
            font-family: 'Inter', sans-serif;
            font-size: .95rem;
            color: var(--text);
            width: 100%;
            resize: vertical;
        }

        .text-control:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--card);
            box-shadow: 0 0 0 3px var(--primary-soft);
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            position: sticky;
            bottom: 14px;
            z-index: 15;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 26px;
            border-radius: 12px;
            border: none;
            background: var(--primary);
            color: #fff;
            font-weight: 700;
            font-size: .95rem;
            box-shadow: var(--shadow);
        }

        .btn-primary:hover {
            background: #5A4BD6;
        }

        .btn-primary:disabled {
            opacity: .6;
        }

        @media (max-width:767px) {
            .take-page {
                padding-bottom: 110px;
            }

            .take-head {
                top: 70px;
                margin: 0 -18px 8px;
                padding: 10px 18px 12px;
                border-bottom: 1px solid var(--line);
            }

            .take-head h1 {
                font-size: 1.1rem;
            }

            .timer-box {
                padding: 8px 12px;
                font-size: 1rem;
            }

            .q-text {
                font-size: .95rem;
            }

            .form-actions {
                bottom: calc(76px + 12px);
            }

            .form-actions .btn-primary {
                width: 100%;
            }
        }

        @media (max-width:420px) {
            .take-head-top {
                gap: 10px;
            }

            .progress-label {
                display: none;
            }
        }
    </style>
@endsection

@section('scripts')
    <script>
        (function() {
            let remaining = {{ $remainingSeconds }};
            const timerText = document.getElementById('timerText');
            const timerBox = document.getElementById('timerBox');
            const form = document.getElementById('takeTestForm');
            const submitBtn = document.getElementById('submitBtn');
            let autoSubmitted = false;

            function render() {
                const m = Math.floor(remaining / 60);
                const s = remaining % 60;
                timerText.textContent = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                timerBox.classList.toggle('danger', remaining <= 60);
            }

            render();

            const interval = setInterval(() => {
                remaining--;
                if (remaining <= 0) {
                    remaining = 0;
                    render();
                    clearInterval(interval);
                    if (!autoSubmitted) {
                        autoSubmitted = true;
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Vaqt tugadi, yuborilmoqda...';
                        form.submit();
                    }
                    return;
                }
                render();
            }, 1000);

            form.addEventListener('submit', function() {
                if (!autoSubmitted) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Yuborilmoqda...';
                }
            });
        })();
    </script>

    <script>
        // ============================================================
        // JAVOBLAR PROGRESSI — nechta savolga javob berilganini ko'rsatadi
        // ============================================================
        (function() {
            const form = document.getElementById('takeTestForm');
            const inputs = form.querySelectorAll('.q-answer-input');
            const fill = document.getElementById('progressFill');
            const label = document.getElementById('progressLabel');
            if (!inputs.length || !fill || !label) return;

            const totalQuestions = new Set(Array.from(inputs).map(el => el.name)).size;

            function update() {
                const answered = new Set(
                    Array.from(inputs).filter(el => el.checked).map(el => el.name)
                ).size;
                const pct = totalQuestions ? Math.round((answered / totalQuestions) * 100) : 0;
                fill.style.width = pct + '%';
                label.textContent = answered + ' / ' + totalQuestions + ' javob berildi';
            }

            inputs.forEach(el => el.addEventListener('change', update));
            update();
        })();
    </script>
@endsection
