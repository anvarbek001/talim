<!DOCTYPE html>
<html lang="uz">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Ha / Yo'q</title>
        <link
            href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap"
            rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <style>
            :root {
                --bg: #FAFAFB;
                --bg-soft: #F2F1F7;
                --card: #FFFFFF;
                --line: #ECEBF2;
                --text: #17171D;
                --muted: #83828F;
                --primary: #6C5CE7;
                --primary-soft: #EFEAFE;
                --mint: #12B886;
                --mint-soft: #E3F9F1;
                --coral: #FF6B6B;
                --coral-soft: #FFE9E9;
                --amber: #FFB020;
                --amber-soft: #FFF3DC;
            }

            @media (prefers-color-scheme: dark) {
                :root {
                    --bg: #0E0E13;
                    --bg-soft: #17171F;
                    --card: #1A1A22;
                    --line: #28282F;
                    --text: #F1F1F6;
                    --muted: #93939F;
                    --primary: #9C8CFF;
                    --primary-soft: rgba(156, 140, 255, .14);
                    --mint: #2FD9A6;
                    --mint-soft: rgba(47, 217, 166, .12);
                    --coral: #FF8585;
                    --coral-soft: rgba(255, 133, 133, .12);
                    --amber: #FFC24B;
                    --amber-soft: rgba(255, 194, 75, .12);
                }
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                background: var(--bg);
                color: var(--text);
                font-family: 'Inter', sans-serif;
                min-height: 100vh;
                padding: 18px;
            }

            h2 {
                font-family: 'Sora', sans-serif;
            }

            .wrap {
                max-width: 640px;
                margin: 0 auto;
            }

            .brand {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 18px;
                font-family: 'Sora', sans-serif;
                font-weight: 800;
            }

            .brand .dot {
                width: 34px;
                height: 34px;
                border-radius: 10px;
                background: linear-gradient(135deg, var(--primary), #9C8CFF);
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .brand small {
                display: block;
                font-family: 'Inter', sans-serif;
                font-weight: 500;
                color: var(--muted);
                font-size: .75rem;
            }

            .stats-row {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 10px;
                margin-bottom: 14px;
            }

            .mini-stat {
                background: var(--card);
                border: 1px solid var(--line);
                border-radius: 14px;
                padding: 12px 14px;
            }

            .mini-stat .lbl {
                font-size: .66rem;
                text-transform: uppercase;
                letter-spacing: .06em;
                color: var(--muted);
                font-weight: 700;
            }

            .mini-stat .val {
                font-family: 'Sora', sans-serif;
                font-weight: 800;
                font-size: 1.3rem;
                margin-top: 2px;
            }

            .val.primary {
                color: var(--primary);
            }

            .val.amber {
                color: var(--amber);
            }

            .play-card {
                background: var(--card);
                border: 1px solid var(--line);
                border-radius: 22px;
                padding: 26px 20px;
                text-align: center;
                position: relative;
                overflow: hidden;
                box-shadow: 0 14px 34px -16px rgba(30, 25, 60, .18);
            }

            .play-card::before {
                content: '';
                position: absolute;
                width: 200px;
                height: 200px;
                border-radius: 50%;
                background: var(--mint-soft);
                top: -100px;
                right: -70px;
            }

            .play-card>* {
                position: relative;
            }

            .screen-icon {
                width: 72px;
                height: 72px;
                border-radius: 20px;
                margin: 0 auto 16px;
                background: linear-gradient(135deg, var(--mint), #5eead4);
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.9rem;
            }

            .screen h2 {
                font-size: 1.35rem;
                margin: 0 0 8px;
            }

            .screen p {
                color: var(--muted);
                font-size: .9rem;
                margin: 0 auto 20px;
                max-width: 380px;
            }

            .name-input {
                width: 100%;
                max-width: 320px;
                padding: 13px 16px;
                border-radius: 13px;
                border: 1.5px solid var(--line);
                background: var(--bg-soft);
                color: var(--text);
                font-family: inherit;
                font-size: 1rem;
                text-align: center;
                outline: none;
                margin-bottom: 14px;
            }

            .name-input:focus {
                border-color: var(--primary);
                box-shadow: 0 0 0 4px var(--primary-soft);
                background: var(--card);
            }

            .btn-game {
                display: inline-flex;
                align-items: center;
                gap: 7px;
                padding: 11px 18px;
                border-radius: 12px;
                border: 1px solid var(--line);
                background: var(--card);
                color: var(--text);
                font-weight: 700;
                font-size: .88rem;
                cursor: pointer;
                transition: .2s;
                font-family: inherit;
            }

            .btn-game:hover {
                transform: translateY(-2px);
                border-color: var(--primary);
            }

            .btn-game.primary {
                background: var(--primary);
                border-color: var(--primary);
                color: #fff;
            }

            .btn-game:disabled {
                opacity: .6;
                cursor: default;
                transform: none;
            }

            .timer-bar {
                height: 8px;
                border-radius: 8px;
                background: var(--bg-soft);
                overflow: hidden;
                margin-bottom: 8px;
            }

            .timer-fill {
                height: 100%;
                width: 100%;
                border-radius: 8px;
                background: linear-gradient(90deg, var(--mint), var(--primary));
                transition: width 1s linear;
            }

            .timer-fill.low {
                background: var(--coral);
            }

            .round-info {
                display: flex;
                justify-content: space-between;
                font-size: .8rem;
                font-weight: 600;
                color: var(--muted);
                margin-bottom: 18px;
            }

            .statement {
                font-family: 'Sora', sans-serif;
                font-weight: 700;
                font-size: 1.2rem;
                line-height: 1.5;
                padding: 22px 16px;
                border-radius: 18px;
                background: var(--bg-soft);
                margin-bottom: 20px;
                min-height: 110px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .answer-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 12px;
            }

            .ans-btn {
                padding: 18px 10px;
                border-radius: 16px;
                border: 2px solid var(--line);
                background: var(--card);
                font-family: 'Sora', sans-serif;
                font-weight: 800;
                font-size: 1.2rem;
                cursor: pointer;
                transition: .2s;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }

            .ans-btn.yes {
                color: var(--mint);
            }

            .ans-btn.no {
                color: var(--coral);
            }

            .ans-btn.yes:hover:not(:disabled) {
                background: var(--mint-soft);
                border-color: var(--mint);
                transform: translateY(-3px);
            }

            .ans-btn.no:hover:not(:disabled) {
                background: var(--coral-soft);
                border-color: var(--coral);
                transform: translateY(-3px);
            }

            .ans-btn:disabled {
                cursor: default;
                opacity: .45;
            }

            .ans-btn.chosen {
                opacity: 1;
            }

            .ans-btn.yes.chosen {
                background: var(--mint-soft);
                border-color: var(--mint);
            }

            .ans-btn.no.chosen {
                background: var(--coral-soft);
                border-color: var(--coral);
            }

            .feedback {
                margin-top: 18px;
                padding: 14px 16px;
                border-radius: 14px;
                text-align: left;
                font-size: .9rem;
                line-height: 1.5;
            }

            .feedback.ok {
                background: var(--mint-soft);
                color: var(--mint);
            }

            .feedback.bad {
                background: var(--coral-soft);
                color: var(--coral);
            }

            .feedback b {
                display: block;
                font-family: 'Sora', sans-serif;
                font-size: 1rem;
                margin-bottom: 3px;
            }

            .feedback .expl {
                color: var(--text);
                margin-top: 6px;
                opacity: .85;
            }

            .next-row {
                margin-top: 14px;
            }

            .result-grid {
                display: flex;
                justify-content: center;
                gap: 12px;
                margin-bottom: 22px;
            }

            .result-grid .mini-stat {
                min-width: 120px;
            }

            .hidden {
                display: none !important;
            }

            .toast {
                position: fixed;
                top: 16px;
                left: 50%;
                transform: translateX(-50%);
                z-index: 99;
                padding: 11px 18px;
                border-radius: 12px;
                font-weight: 700;
                font-size: .88rem;
                box-shadow: 0 10px 24px -8px rgba(0, 0, 0, .3);
                color: #fff;
                background: var(--coral);
            }
        </style>
    </head>

    <body>
        <div class="wrap">
            <div class="brand">
                <div class="dot">D</div>
                <div>DarsQil
                    <small>{{ $teacher->name }}{{ $teacher->university?->title ? ' · ' . $teacher->university->title : '' }}</small>
                </div>
            </div>

            @if ($count < 1)
                <div class="play-card">
                    <div class="screen">
                        <div class="screen-icon" style="background: var(--bg-soft); color: var(--muted);"><i
                                class="bi bi-hourglass-split"></i></div>
                        <h2>O'yin hali tayyor emas</h2>
                        <p>O'qituvchi hali savol qo'shmagan. Keyinroq urinib ko'ring.</p>
                    </div>
                </div>
            @else
                <div class="stats-row">
                    <div class="mini-stat">
                        <div class="lbl">Ball</div>
                        <div class="val primary" id="scoreVal">0</div>
                    </div>
                    <div class="mini-stat">
                        <div class="lbl">Ketma-ket</div>
                        <div class="val amber" id="streakVal">0</div>
                    </div>
                    <div class="mini-stat">
                        <div class="lbl">Vaqt</div>
                        <div class="val" id="timeVal">--</div>
                    </div>
                </div>

                <div class="play-card">
                    {{-- Ism --}}
                    <div class="screen" id="startScreen">
                        <div class="screen-icon"><i class="bi bi-patch-question-fill"></i></div>
                        <h2>Ha / Yo'q</h2>
                        <p>Ismingizni kiriting. Tasdiqni o'qing va u to'g'ri bo'lsa «Ha», noto'g'ri bo'lsa «Yo'q» ni
                            bosing. Har savolga {{ $time }} soniya.</p>
                        <input type="text" id="playerName" class="name-input" maxlength="60"
                            placeholder="Ism va familiyangiz" autocomplete="off">
                        <div><button class="btn-game primary" id="startBtn"><i class="bi bi-play-fill"></i>
                                Boshlash</button></div>
                    </div>

                    {{-- O'yin --}}
                    <div class="hidden" id="gameScreen">
                        <div class="timer-bar">
                            <div class="timer-fill" id="timerFill"></div>
                        </div>
                        <div class="round-info"><span id="roundInfo"></span><span>Tasdiq to'g'rimi?</span></div>

                        <div class="statement" id="statement"></div>

                        <div class="answer-row">
                            <button class="ans-btn yes" id="yesBtn"><i class="bi bi-check-lg"></i> Ha</button>
                            <button class="ans-btn no" id="noBtn"><i class="bi bi-x-lg"></i> Yo'q</button>
                        </div>

                        <div id="feedback" class="feedback hidden"></div>
                        <div class="next-row hidden" id="nextRow">
                            <button class="btn-game primary" id="nextBtn">Keyingi <i
                                    class="bi bi-arrow-right"></i></button>
                        </div>
                    </div>

                    {{-- Natija --}}
                    <div class="screen hidden" id="endScreen">
                        <div class="screen-icon"><i class="bi bi-trophy-fill"></i></div>
                        <h2 id="endName">O'yin tugadi!</h2>
                        <p id="endText"></p>
                        <div class="result-grid">
                            <div class="mini-stat">
                                <div class="lbl">Jami ball</div>
                                <div class="val primary" id="endScore">0</div>
                            </div>
                            <div class="mini-stat">
                                <div class="lbl">To'g'ri</div>
                                <div class="val" id="endCorrect">0</div>
                            </div>
                        </div>
                        <button class="btn-game primary" id="restartBtn"><i class="bi bi-arrow-repeat"></i> Qayta
                            o'ynash</button>
                    </div>
                </div>
            @endif
        </div>

        @if ($count > 0)
            <script>
                const URLS = {
                    start: @json(route('yesno.start', $token)),
                    answer: @json(route('yesno.answer', $token)),
                    next: @json(route('yesno.next', $token)),
                };
                const CSRF = document.querySelector('meta[name="csrf-token"]').content;
                const $ = id => document.getElementById(id);

                let playerName = '',
                    totalTime = 15,
                    timeLeft = 15,
                    timer = null,
                    locked = true;

                function toast(text) {
                    const t = document.createElement('div');
                    t.className = 'toast';
                    t.textContent = text;
                    document.body.appendChild(t);
                    setTimeout(() => t.remove(), 2200);
                }

                async function api(url, body = {}) {
                    const r = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': CSRF
                        },
                        body: JSON.stringify(body)
                    });
                    const data = await r.json().catch(() => ({}));
                    if (!r.ok) {
                        throw new Error(data.message || (data.errors && Object.values(data.errors)[0][0]) ||
                            'Xatolik yuz berdi');
                    }
                    return data;
                }

                function paintTimer() {
                    $('timeVal').textContent = timeLeft + 's';
                    const f = $('timerFill');
                    f.style.width = (timeLeft / totalTime * 100) + '%';
                    f.classList.toggle('low', timeLeft <= 5);
                }

                function startTimer() {
                    clearInterval(timer);
                    timeLeft = totalTime;
                    paintTimer();
                    timer = setInterval(() => {
                        timeLeft--;
                        paintTimer();
                        if (timeLeft <= 0) {
                            clearInterval(timer);
                            submit(null);
                        }
                    }, 1000);
                }

                function showQuestion(q, time) {
                    totalTime = time;
                    $('roundInfo').textContent = `${q.index} / ${q.total}`;
                    $('statement').textContent = q.statement;
                    $('feedback').classList.add('hidden');
                    $('nextRow').classList.add('hidden');
                    ['yesBtn', 'noBtn'].forEach(id => {
                        $(id).disabled = false;
                        $(id).classList.remove('chosen');
                    });
                    locked = false;
                    startTimer();
                }

                async function startGame() {
                    const name = $('playerName').value.trim();
                    if (name.length < 2) {
                        toast('Ismingizni kiriting');
                        $('playerName').focus();
                        return;
                    }
                    playerName = name;
                    $('startBtn').disabled = true;
                    try {
                        const d = await api(URLS.start, {
                            player_name: name
                        });
                        $('scoreVal').textContent = 0;
                        $('streakVal').textContent = 0;
                        $('startScreen').classList.add('hidden');
                        $('endScreen').classList.add('hidden');
                        $('gameScreen').classList.remove('hidden');
                        showQuestion(d.question, d.time);
                    } catch (e) {
                        toast(e.message);
                    }
                    $('startBtn').disabled = false;
                }

                async function submit(value) {
                    if (locked) return;
                    locked = true;
                    clearInterval(timer);
                    ['yesBtn', 'noBtn'].forEach(id => $(id).disabled = true);
                    if (value === true) $('yesBtn').classList.add('chosen');
                    if (value === false) $('noBtn').classList.add('chosen');

                    try {
                        const d = await api(URLS.answer, {
                            answer: value
                        });

                        $('scoreVal').textContent = d.score;
                        $('streakVal').textContent = d.streak;

                        const fb = $('feedback');
                        fb.className = 'feedback ' + (d.correct ? 'ok' : 'bad');
                        const title = d.correct ? `To'g'ri! +${d.gained} ball` :
                            (value === null ? 'Vaqt tugadi' : "Noto'g'ri");
                        fb.innerHTML = `<b></b><div>To'g'ri javob: <strong></strong></div><div class="expl"></div>`;
                        fb.querySelector('b').textContent = title;
                        fb.querySelector('strong').textContent = d.right ? 'Ha' : "Yo'q";
                        const ex = fb.querySelector('.expl');
                        d.explanation ? ex.textContent = d.explanation : ex.remove();

                        $('nextRow').classList.remove('hidden');
                        $('nextBtn').innerHTML = d.finished ?
                            'Natijani ko\'rish <i class="bi bi-flag-fill"></i>' :
                            'Keyingi <i class="bi bi-arrow-right"></i>';
                        $('nextBtn').onclick = d.finished ? () => finishGame(d) : nextQuestion;
                    } catch (e) {
                        toast(e.message);
                        locked = false;
                        startTimer();
                    }
                }

                async function nextQuestion() {
                    $('nextBtn').disabled = true;
                    try {
                        const d = await api(URLS.next);
                        showQuestion(d.question, d.time);
                    } catch (e) {
                        toast(e.message);
                    }
                    $('nextBtn').disabled = false;
                }

                function finishGame(d) {
                    $('gameScreen').classList.add('hidden');
                    $('endScreen').classList.remove('hidden');
                    $('timeVal').textContent = '--';
                    $('endName').textContent = playerName + ", o'yin tugadi!";
                    $('endScore').textContent = d.score;
                    $('endCorrect').textContent = `${d.correct_cnt} / ${d.total}`;
                    const r = d.correct_cnt / d.total;
                    $('endText').textContent = r >= 0.8 ? "Ajoyib natija!" : r >= 0.5 ? "Yaxshi natija!" :
                        "Mashq qilishda davom eting.";
                }

                $('startBtn').addEventListener('click', startGame);
                $('playerName').addEventListener('keydown', e => {
                    if (e.key === 'Enter') startGame();
                });
                $('yesBtn').addEventListener('click', () => submit(true));
                $('noBtn').addEventListener('click', () => submit(false));
                $('restartBtn').addEventListener('click', () => {
                    $('endScreen').classList.add('hidden');
                    $('startScreen').classList.remove('hidden');
                });
            </script>
        @endif
    </body>

</html>
