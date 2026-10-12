<!DOCTYPE html>
<html lang="uz">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Anagramma</title>
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

            h1,
            h2,
            .display {
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
                background: var(--primary-soft);
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
                background: linear-gradient(135deg, var(--primary), #9C8CFF);
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

            .hint-box {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 8px 14px;
                border-radius: 12px;
                background: var(--amber-soft);
                color: #8A6100;
                font-size: .85rem;
                font-weight: 600;
                margin-bottom: 22px;
                text-align: left;
            }

            @media (prefers-color-scheme: dark) {
                .hint-box {
                    color: var(--amber);
                }
            }

            .tiles-row {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 8px;
                min-height: 54px;
            }

            .slots-row {
                margin-bottom: 22px;
            }

            .slot,
            .tile {
                width: 46px;
                height: 52px;
                border-radius: 13px;
                font-family: 'Sora', sans-serif;
                font-weight: 800;
                font-size: 1.25rem;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .slot {
                border: 2px dashed var(--line);
                background: var(--bg-soft);
                color: var(--text);
                cursor: pointer;
            }

            .slot.filled {
                border-style: solid;
                border-color: var(--primary);
                background: var(--primary-soft);
                color: var(--primary);
            }

            .slot.correct {
                border-color: var(--mint);
                background: var(--mint-soft);
                color: var(--mint);
            }

            .slots-row.shake {
                animation: shake .45s;
            }

            .slots-row.shake .slot.filled {
                border-color: var(--coral);
                background: var(--coral-soft);
                color: var(--coral);
            }

            @keyframes shake {

                0%,
                100% {
                    transform: translateX(0)
                }

                20% {
                    transform: translateX(-9px)
                }

                40% {
                    transform: translateX(9px)
                }

                60% {
                    transform: translateX(-6px)
                }

                80% {
                    transform: translateX(6px)
                }
            }

            .tile {
                border: 1px solid var(--line);
                background: var(--card);
                color: var(--primary);
                box-shadow: 0 8px 16px -8px rgba(108, 92, 231, .35);
                cursor: pointer;
                transition: transform .2s, opacity .2s;
            }

            .tile:hover:not(:disabled) {
                transform: translateY(-4px);
            }

            .tile:disabled {
                opacity: .2;
                box-shadow: none;
                cursor: default;
            }

            .actions {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 8px;
                margin-top: 24px;
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
            }

            .toast.success {
                background: var(--mint);
            }

            .toast.error {
                background: var(--coral);
            }

            @media (max-width: 420px) {

                .slot,
                .tile {
                    width: 38px;
                    height: 44px;
                    font-size: 1.05rem;
                }
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

            @if ($words->count() < 1)
                <div class="play-card">
                    <div class="screen">
                        <div class="screen-icon" style="background: var(--bg-soft); color: var(--muted);"><i
                                class="bi bi-hourglass-split"></i></div>
                        <h2>O'yin hali tayyor emas</h2>
                        <p>O'qituvchi hali so'z qo'shmagan. Keyinroq urinib ko'ring.</p>
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
                    {{-- Ism kiritish --}}
                    <div class="screen" id="startScreen">
                        <div class="screen-icon"><i class="bi bi-shuffle"></i></div>
                        <h2>Anagramma</h2>
                        <p>Ismingizni kiriting va o'yinni boshlang. Aralashgan harflardan to'g'ri so'zni toping.</p>
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
                        <div class="round-info"><span id="roundInfo"></span><span id="lenInfo"></span></div>
                        <div class="hint-box"><i class="bi bi-lightbulb-fill"></i> <span id="hintText"></span></div>
                        <div class="tiles-row slots-row" id="slotsRow"></div>
                        <div class="tiles-row" id="poolRow"></div>
                        <div class="actions">
                            <button class="btn-game" id="hintBtn"><i class="bi bi-magic"></i> Maslahat (-5)</button>
                            <button class="btn-game" id="clearBtn"><i class="bi bi-eraser"></i> Tozalash</button>
                            <button class="btn-game" id="skipBtn"><i class="bi bi-skip-forward"></i>
                                O'tkazish</button>
                        </div>
                    </div>

                    {{-- Natija --}}
                    <div class="screen hidden" id="endScreen">
                        <div class="screen-icon" style="background: linear-gradient(135deg, var(--mint), #5eead4);"><i
                                class="bi bi-trophy-fill"></i></div>
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

        @if ($words->count() > 0)
            <script>
                const WORDS = @json($words);
                const SUBMIT_URL = @json(route('anagramma.finish', $token));
                const CSRF = document.querySelector('meta[name="csrf-token"]').content;
                const ROUND_COUNT = Math.min(8, WORDS.length);
                const TIME_PER_WORD = 45;

                const $ = id => document.getElementById(id);
                let playerName = '',
                    queue = [],
                    idx = 0,
                    score = 0,
                    streak = 0,
                    correctCount = 0;
                let pool = [],
                    slots = [],
                    hintCount = 0,
                    locked = false,
                    timeLeft = TIME_PER_WORD,
                    timer = null;

                function toast(type, text) {
                    const t = document.createElement('div');
                    t.className = 'toast ' + type;
                    t.textContent = text;
                    document.body.appendChild(t);
                    setTimeout(() => t.remove(), 1800);
                }

                function shuffle(arr) {
                    const a = [...arr];
                    for (let i = a.length - 1; i > 0; i--) {
                        const j = Math.floor(Math.random() * (i + 1));
                        [a[i], a[j]] = [a[j], a[i]];
                    }
                    return a;
                }

                function updateStats() {
                    $('scoreVal').textContent = score;
                    $('streakVal').textContent = streak;
                }

                function startGame() {
                    const name = $('playerName').value.trim();
                    if (name.length < 2) {
                        toast('error', 'Ismingizni kiriting');
                        $('playerName').focus();
                        return;
                    }
                    playerName = name;
                    queue = shuffle(WORDS).slice(0, ROUND_COUNT);
                    idx = 0;
                    score = 0;
                    streak = 0;
                    correctCount = 0;
                    updateStats();
                    $('startScreen').classList.add('hidden');
                    $('endScreen').classList.add('hidden');
                    $('gameScreen').classList.remove('hidden');
                    loadWord();
                }

                function loadWord() {
                    const w = queue[idx];
                    const letters = Array.from(w.word).map((ch, i) => ({
                        id: i,
                        ch
                    }));
                    let tries = 0;
                    do {
                        pool = shuffle(letters);
                        tries++;
                    }
                    while (pool.map(p => p.ch).join('') === w.word && tries < 10);

                    slots = Array(letters.length).fill(null);
                    hintCount = 0;
                    locked = false;
                    $('roundInfo').textContent = `${idx + 1} / ${queue.length}`;
                    $('lenInfo').textContent = `${letters.length} ta harf`;
                    $('hintText').textContent = w.hint;
                    render();
                    startTimer();
                }

                function render() {
                    const w = Array.from(queue[idx].word);
                    $('slotsRow').innerHTML = slots.map((id, i) => {
                        if (id === null) return `<div class="slot" data-slot="${i}"></div>`;
                        const ch = pool.find(p => p.id === id).ch;
                        return `<div class="slot filled ${locked && ch === w[i] ? 'correct' : ''}" data-slot="${i}">${ch}</div>`;
                    }).join('');
                    $('poolRow').innerHTML = pool.map(p =>
                            `<button class="tile" data-id="${p.id}" ${slots.includes(p.id) ? 'disabled' : ''}>${p.ch}</button>`)
                        .join('');
                }

                function place(id) {
                    if (locked || slots.includes(id)) return;
                    const i = slots.indexOf(null);
                    if (i === -1) return;
                    slots[i] = id;
                    render();
                    if (!slots.includes(null)) checkAnswer();
                }

                function removeAt(i) {
                    if (locked || slots[i] === null) return;
                    slots[i] = null;
                    render();
                }

                function removeLast() {
                    if (locked) return;
                    for (let i = slots.length - 1; i >= 0; i--)
                        if (slots[i] !== null) {
                            removeAt(i);
                            return;
                        }
                }

                function checkAnswer() {
                    const w = queue[idx].word;
                    const guess = slots.map(id => pool.find(p => p.id === id).ch).join('');
                    if (guess === w) {
                        locked = true;
                        clearInterval(timer);
                        const gained = Math.max(10 + timeLeft - hintCount * 5, 5);
                        score += gained;
                        streak++;
                        correctCount++;
                        updateStats();
                        render();
                        toast('success', `To'g'ri! +${gained} ball`);
                        setTimeout(nextWord, 1000);
                    } else {
                        streak = 0;
                        updateStats();
                        const row = $('slotsRow');
                        row.classList.add('shake');
                        setTimeout(() => {
                            row.classList.remove('shake');
                            slots = Array(Array.from(w).length).fill(null);
                            render();
                        }, 500);
                    }
                }

                function useHint() {
                    if (locked) return;
                    const w = Array.from(queue[idx].word);
                    if (hintCount >= w.length - 1) return;
                    hintCount++;
                    slots = Array(w.length).fill(null);
                    const used = new Set();
                    for (let j = 0; j < hintCount; j++) {
                        const tile = pool.find(p => p.ch === w[j] && !used.has(p.id));
                        used.add(tile.id);
                        slots[j] = tile.id;
                    }
                    render();
                }

                function paintTimer() {
                    $('timeVal').textContent = timeLeft + 's';
                    const f = $('timerFill');
                    f.style.width = (timeLeft / TIME_PER_WORD * 100) + '%';
                    f.classList.toggle('low', timeLeft <= 10);
                }

                function startTimer() {
                    clearInterval(timer);
                    timeLeft = TIME_PER_WORD;
                    paintTimer();
                    timer = setInterval(() => {
                        timeLeft--;
                        paintTimer();
                        if (timeLeft <= 0) {
                            clearInterval(timer);
                            locked = true;
                            streak = 0;
                            updateStats();
                            toast('error', 'Vaqt tugadi. Javob: ' + queue[idx].word);
                            setTimeout(nextWord, 1500);
                        }
                    }, 1000);
                }

                function skipWord() {
                    if (locked) return;
                    locked = true;
                    clearInterval(timer);
                    streak = 0;
                    updateStats();
                    toast('error', 'Javob: ' + queue[idx].word);
                    setTimeout(nextWord, 1300);
                }

                function nextWord() {
                    idx++;
                    idx >= queue.length ? finishGame() : loadWord();
                }

                function finishGame() {
                    clearInterval(timer);
                    $('gameScreen').classList.add('hidden');
                    $('endScreen').classList.remove('hidden');
                    $('timeVal').textContent = '--';
                    $('endName').textContent = playerName + ', o\'yin tugadi!';
                    $('endScore').textContent = score;
                    $('endCorrect').textContent = `${correctCount} / ${queue.length}`;
                    const r = correctCount / queue.length;
                    $('endText').textContent = r >= 0.8 ? "Ajoyib natija!" : r >= 0.5 ? "Yaxshi natija!" :
                        "Mashq qilishda davom eting.";

                    fetch(SUBMIT_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': CSRF
                        },
                        body: JSON.stringify({
                            player_name: playerName,
                            score,
                            correct: correctCount,
                            total: queue.length
                        })
                    }).catch(() => toast('error', "Natijani yuborib bo'lmadi"));
                }

                $('startBtn').addEventListener('click', startGame);
                $('playerName').addEventListener('keydown', e => {
                    if (e.key === 'Enter') startGame();
                });
                $('restartBtn').addEventListener('click', () => {
                    $('endScreen').classList.add('hidden');
                    $('startScreen').classList.remove('hidden');
                });
                $('hintBtn').addEventListener('click', useHint);
                $('skipBtn').addEventListener('click', skipWord);
                $('clearBtn').addEventListener('click', () => {
                    if (!locked) {
                        slots = slots.map(() => null);
                        render();
                    }
                });
                $('poolRow').addEventListener('click', e => {
                    const b = e.target.closest('.tile');
                    if (b) place(Number(b.dataset.id));
                });
                $('slotsRow').addEventListener('click', e => {
                    const s = e.target.closest('.slot');
                    if (s) removeAt(Number(s.dataset.slot));
                });
                document.addEventListener('keydown', e => {
                    if ($('gameScreen').classList.contains('hidden') || locked || e.target.tagName === 'INPUT') return;
                    if (e.key === 'Backspace') {
                        removeLast();
                        return;
                    }
                    if (e.key.length !== 1) return;
                    const tile = pool.find(p => p.ch === e.key.toUpperCase() && !slots.includes(p.id));
                    if (tile) place(tile.id);
                });
            </script>
        @endif
    </body>

</html>
