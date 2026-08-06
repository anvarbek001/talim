@extends('layouts.student')

@php
    $testable = $attempt->testable;
    $deadline = $attempt->started_at->copy()->addMinutes($testable->duration_minutes);
    $remainingSeconds = max(0, (int) now()->diffInSeconds($deadline, false));
@endphp

@section('content')
    <div class="page take-page">
        <div class="take-head" id="takeHead">
            <div class="take-head-top">
                <div>
                    <div class="take-eyebrow">Test yechilmoqda</div>
                    <h1>{{ $testable->title }}</h1>
                    @if ($testable->user)
                        <div class="take-teacher-chip">
                            <span class="take-teacher-avatar">
                                @if ($testable->user->avatarUrl())
                                    <img src="{{ $testable->user->avatarUrl() }}" alt="{{ $testable->user->name }}">
                                @else
                                    {{ $testable->user->initials() }}
                                @endif
                            </span>
                            {{ $testable->user->name }}
                        </div>
                    @endif
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
                <button type="button" class="qnav-toggle" id="qnavToggle">
                    <i class="bi bi-grid-3x3-gap-fill"></i> Savollar
                </button>
                <button type="button" class="qnav-next-empty" id="qnavNextEmpty">
                    <i class="bi bi-skip-forward-fill"></i> Bo'shiga o'tish
                </button>
            </div>

            <div class="qnav-panel" id="qnavPanel">
                <div class="qnav-legend">
                    <span><i class="qnav-dot is-answered"></i> Javob berilgan</span>
                    <span><i class="qnav-dot"></i> Bo'sh</span>
                </div>
                <div class="qnav-grid" id="qnavGrid"></div>
            </div>
        </div>

        <form action="{{ route('student-tests.submit', $attempt) }}" method="POST" id="takeTestForm">
            @csrf

            @foreach ($testable->questions as $question)
                <div class="card fade-up q-card" id="q-{{ $loop->iteration }}" style="animation-delay:{{ min($loop->index * 0.03, 0.3) }}s;">
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

            @if (($testable instanceof \App\Models\SertifikatTest || $testable instanceof \App\Models\LanguageExamTest) && $testable->writtenQuestions->isNotEmpty())
                <div class="card fade-up q-card" style="animation-delay:.32s;">
                    <div class="q-written-head"><i class="bi bi-pencil-square"></i> Yozma qism</div>
                    @foreach ($testable->writtenQuestions as $writtenQuestion)
                        <div class="q-written-block" id="wq-{{ $loop->iteration }}">
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
            position: fixed;
            top: 87px;
            left: var(--rail-w);
            right: 0;
            z-index: 20;
            background: var(--bg);
            padding: 6px 32px 14px;
            border-bottom: 1px solid var(--line);
        }

        #takeTestForm {
            padding-top: var(--take-head-h, 130px);
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

        .take-teacher-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: .78rem;
            font-weight: 600;
            color: var(--muted);
            margin-top: 4px;
        }

        .take-teacher-avatar {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), #9C8CFF);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .62rem;
            font-weight: 700;
            font-family: 'Sora', sans-serif;
            overflow: hidden;
            flex-shrink: 0;
        }

        .take-teacher-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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
            flex-wrap: wrap;
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

        .qnav-toggle,
        .qnav-next-empty {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--bg-soft);
            border: 1.5px solid var(--line);
            border-radius: 20px;
            padding: 7px 14px;
            font-size: .76rem;
            font-weight: 700;
            color: var(--text);
            flex-shrink: 0;
            white-space: nowrap;
        }

        .qnav-toggle:hover,
        .qnav-next-empty:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .qnav-toggle.is-open {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }

        .qnav-panel {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 8px;
            width: min(360px, 100%);
            background: var(--card);
            border: 1.5px solid var(--line);
            border-radius: 14px;
            box-shadow: var(--shadow);
            padding: 14px;
            z-index: 25;
            max-height: 320px;
            overflow-y: auto;
        }

        .qnav-panel.show {
            display: block;
        }

        .qnav-legend {
            display: flex;
            gap: 16px;
            font-size: .74rem;
            color: var(--muted);
            margin-bottom: 10px;
        }

        .qnav-legend span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .qnav-dot {
            width: 10px;
            height: 10px;
            border-radius: 3px;
            background: var(--bg-soft);
            border: 1.5px solid var(--line);
            display: inline-block;
        }

        .qnav-dot.is-answered {
            background: var(--mint);
            border-color: var(--mint);
        }

        .qnav-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(38px, 1fr));
            gap: 8px;
        }

        .qnav-chip {
            width: 100%;
            aspect-ratio: 1;
            border-radius: 9px;
            border: 1.5px solid var(--line);
            background: var(--bg-soft);
            color: var(--text);
            font-size: .78rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qnav-chip.is-answered {
            background: var(--mint-soft);
            border-color: var(--mint);
            color: var(--mint);
        }

        .qnav-chip-written {
            font-size: .68rem;
        }

        .q-card {
            margin-bottom: 16px;
            scroll-margin-top: 120px;
        }

        .q-card.qnav-flash,
        .q-written-block.qnav-flash {
            animation: qnavFlash 1.1s ease;
        }

        @keyframes qnavFlash {
            0% {
                box-shadow: 0 0 0 3px var(--primary);
            }

            100% {
                box-shadow: none;
            }
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
                left: 0;
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
        // ============================================================
        // TEPADAGI PANEL — topbar balandligiga aniq moslab qotiriladi,
        // aks holda ekran o'lchamiga qarab panel topbar ostida
        // yashirinib, "Savollar" tugmasiga yetib bo'lmay qolardi.
        // ============================================================
        (function() {
            const topbar = document.querySelector('.topbar');
            const takeHead = document.getElementById('takeHead');
            if (!topbar || !takeHead) return;

            function syncOffset() {
                takeHead.style.top = topbar.getBoundingClientRect().height + 'px';
            }

            syncOffset();
            window.addEventListener('resize', syncOffset);
            window.addEventListener('load', syncOffset);
        })();
    </script>

    <script>
        // ============================================================
        // Savollar endi qattiq (fixed) qotirilgan panel ostida qolmasligi
        // uchun, panelning haqiqiy balandligiga qarab formaga yuqoridan
        // bo'shliq beriladi — savollar ko'p bo'lganda ham panel doim
        // ko'rinib turadi.
        // ============================================================
        (function() {
            const takeHead = document.getElementById('takeHead');
            if (!takeHead) return;

            function syncSpacing() {
                document.documentElement.style.setProperty('--take-head-h', (takeHead.offsetHeight + 16) + 'px');
            }

            syncSpacing();
            window.addEventListener('resize', syncSpacing);
            window.addEventListener('load', syncSpacing);

            if (window.ResizeObserver) {
                new ResizeObserver(syncSpacing).observe(takeHead);
            }
        })();
    </script>

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

    <script>
        // ============================================================
        // SAVOLLAR NAVIGATORI — o'tkazib yuborilgan savolni tez topish
        // ============================================================
        (function() {
            const toggle = document.getElementById('qnavToggle');
            const panel = document.getElementById('qnavPanel');
            const grid = document.getElementById('qnavGrid');
            const nextEmptyBtn = document.getElementById('qnavNextEmpty');
            if (!toggle || !panel || !grid) return;

            function isMcqAnswered(card) {
                return !!card.querySelector('.q-answer-input:checked');
            }

            function isWrittenAnswered(block) {
                const textarea = block.querySelector('textarea');
                return !!textarea && textarea.value.trim().length > 0;
            }

            const items = [];

            document.querySelectorAll('.q-card[id^="q-"]').forEach(card => {
                const num = card.id.replace('q-', '');
                const chip = document.createElement('button');
                chip.type = 'button';
                chip.className = 'qnav-chip';
                chip.textContent = num;
                chip.addEventListener('click', () => jumpTo(card));
                grid.appendChild(chip);
                items.push({
                    el: card,
                    chip,
                    answered: () => isMcqAnswered(card)
                });

                card.querySelectorAll('.q-answer-input').forEach(input => {
                    input.addEventListener('change', refresh);
                });
            });

            document.querySelectorAll('.q-written-block[id^="wq-"]').forEach(block => {
                const num = block.id.replace('wq-', '');
                const chip = document.createElement('button');
                chip.type = 'button';
                chip.className = 'qnav-chip qnav-chip-written';
                chip.textContent = 'Y' + num;
                chip.title = num + '-yozma savol';
                chip.addEventListener('click', () => jumpTo(block));
                grid.appendChild(chip);
                items.push({
                    el: block,
                    chip,
                    answered: () => isWrittenAnswered(block)
                });

                const textarea = block.querySelector('textarea');
                if (textarea) textarea.addEventListener('input', refresh);
            });

            function refresh() {
                items.forEach(item => {
                    item.chip.classList.toggle('is-answered', item.answered());
                });
            }

            function jumpTo(el) {
                el.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                el.classList.remove('qnav-flash');
                // restart animation
                void el.offsetWidth;
                el.classList.add('qnav-flash');
                closePanel();
            }

            function openPanel() {
                panel.classList.add('show');
                toggle.classList.add('is-open');
            }

            function closePanel() {
                panel.classList.remove('show');
                toggle.classList.remove('is-open');
            }

            toggle.addEventListener('click', () => {
                panel.classList.contains('show') ? closePanel() : openPanel();
            });

            document.addEventListener('click', (e) => {
                if (!panel.contains(e.target) && e.target !== toggle && !toggle.contains(e.target)) {
                    closePanel();
                }
            });

            nextEmptyBtn?.addEventListener('click', () => {
                const firstEmpty = items.find(item => !item.answered());
                if (!firstEmpty) {
                    nextEmptyBtn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Hammasi to\'ldirilgan';
                    setTimeout(() => {
                        nextEmptyBtn.innerHTML = '<i class="bi bi-skip-forward-fill"></i> Bo\'shiga o\'tish';
                    }, 1800);
                    return;
                }
                jumpTo(firstEmpty.el);
            });

            refresh();
        })();
    </script>
@endsection
