@extends('layouts.university')
@section('body')
    <style>
        .games-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 18px;
        }

        .game-card {
            position: relative;
            display: flex;
            flex-direction: column;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
        }

        a.game-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow);
            border-color: var(--primary);
        }

        .game-cover {
            position: relative;
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--primary), #9C8CFF);
            overflow: hidden;
        }

        .game-cover::before,
        .game-cover::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, .12);
        }

        .game-cover::before {
            width: 150px;
            height: 150px;
            top: -60px;
            right: -40px;
        }

        .game-cover::after {
            width: 100px;
            height: 100px;
            bottom: -45px;
            left: -25px;
        }

        .letter-tile {
            position: relative;
            z-index: 1;
            width: 42px;
            height: 48px;
            border-radius: 11px;
            background: #fff;
            color: var(--primary);
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 18px -6px rgba(0, 0, 0, .3);
            transition: transform .3s ease;
        }

        .letter-tile:nth-child(1) {
            transform: rotate(-8deg) translateY(4px);
        }

        .letter-tile:nth-child(2) {
            transform: rotate(5deg) translateY(-6px);
        }

        .letter-tile:nth-child(3) {
            transform: rotate(-4deg) translateY(6px);
        }

        .letter-tile:nth-child(4) {
            transform: rotate(9deg) translateY(-3px);
        }

        .letter-tile:nth-child(5) {
            transform: rotate(-6deg) translateY(5px);
        }

        a.game-card:hover .letter-tile {
            transform: rotate(0) translateY(0);
        }

        .game-tag {
            position: absolute;
            top: 14px;
            left: 14px;
            z-index: 2;
            padding: 4px 11px;
            border-radius: 20px;
            font-size: .7rem;
            font-weight: 700;
            background: rgba(255, 255, 255, .22);
            color: #fff;
            backdrop-filter: blur(6px);
        }

        .game-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            flex: 1;
        }

        .game-title {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 1.1rem;
            margin: 0;
        }

        .game-desc {
            color: var(--muted);
            font-size: .85rem;
            line-height: 1.5;
            margin: 0;
        }

        .game-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 2px;
        }

        .meta-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: .74rem;
            font-weight: 600;
            background: var(--bg-soft);
            color: var(--muted);
        }

        .meta-chip.mint {
            background: var(--mint-soft);
            color: var(--mint);
        }

        .game-foot {
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 700;
            font-size: .88rem;
            color: var(--primary);
        }

        .game-foot .arrow {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: var(--primary-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: .25s;
        }

        a.game-card:hover .arrow {
            background: var(--primary);
            color: #fff;
            transform: translateX(4px);
        }

        /* Tez orada kartasi */
        .game-card.soon {
            opacity: .7;
            border-style: dashed;
            box-shadow: none;
        }

        .game-card.soon .game-cover {
            background: var(--bg-soft);
        }

        .game-card.soon .game-cover i {
            font-size: 2.4rem;
            color: var(--muted);
            position: relative;
            z-index: 1;
        }

        .game-card.soon .game-foot {
            color: var(--muted);
        }

        /* Ha / Yo'q kartasi */
        .game-cover.yn {
            background: linear-gradient(135deg, #12B886, #0CA678 45%, #FF6B6B 55%, #F03E3E);
            gap: 14px;
        }

        .yn-badge {
            position: relative;
            z-index: 1;
            width: 62px;
            height: 62px;
            border-radius: 18px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            box-shadow: 0 10px 22px -8px rgba(0, 0, 0, .35);
            transition: transform .3s ease;
        }

        .yn-badge.yes {
            color: var(--mint);
            transform: rotate(-9deg) translateY(4px);
        }

        .yn-badge.no {
            color: var(--coral);
            transform: rotate(9deg) translateY(-4px);
        }

        .yn-vs {
            position: relative;
            z-index: 2;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .25);
            backdrop-filter: blur(6px);
            color: #fff;
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: .95rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        a.game-card:hover .yn-badge.yes {
            transform: rotate(0) translateY(-4px) scale(1.06);
        }

        a.game-card:hover .yn-badge.no {
            transform: rotate(0) translateY(-4px) scale(1.06);
        }

        .game-card.yn-card:hover {
            border-color: var(--mint);
        }

        .game-card.yn-card .game-foot {
            color: var(--mint);
        }

        .game-card.yn-card .game-foot .arrow {
            background: var(--mint-soft);
        }

        a.game-card.yn-card:hover .arrow {
            background: var(--mint);
        }
    </style>

    <div class="page fade-up">
        <div class="page-head">
            <div>
                <div class="greet-eyebrow">O'yinlar</div>
                <h1>Qiziqarli darslar</h1>
                <p class="page-sub">O'ynab o'rganing: talabalar uchun turli usuldagi o'quv o'yinlari.</p>
            </div>
        </div>

        <div class="games-grid">

            {{-- 1. Anagramma --}}
            <a href="{{ route('anagramma.index') }}" class="game-card">
                <div class="game-cover">
                    <span class="game-tag">So'z o'yini</span>
                    <div class="letter-tile">R</div>
                    <div class="letter-tile">S</div>
                    <div class="letter-tile">A</div>
                    <div class="letter-tile">D</div>
                    <div class="letter-tile">Q</div>
                </div>
                <div class="game-body">
                    <h3 class="game-title">Anagramma</h3>
                    <p class="game-desc">Aralashtirib yuborilgan harflardan to'g'ri so'zni toping. Fan atamalarini
                        yodlashning qiziqarli usuli.</p>
                    {{-- <div class="game-meta">
                        <span class="meta-chip"><i class="bi bi-clock"></i> 5-10 daqiqa</span>
                        <span class="meta-chip"><i class="bi bi-person"></i> Yakka</span>
                        <span class="meta-chip mint"><i class="bi bi-lightning-charge-fill"></i> Oson</span>
                    </div> --}}
                    <div class="game-foot">
                        <span>Boshlash</span>
                        <span class="arrow"><i class="bi bi-arrow-right"></i></span>
                    </div>
                </div>
            </a>

            {{-- 2. Ha/Yo'q --}}
            <a href="{{ route('yesno.index') }}" class="game-card yn-card">
                <div class="game-cover yn">
                    <span class="game-tag">Ha/Yo'q o'yini</span>
                    <div class="yn-badge yes"><i class="bi bi-check-lg"></i></div>
                    <div class="yn-vs">?</div>
                    <div class="yn-badge no"><i class="bi bi-x-lg"></i></div>
                </div>
                <div class="game-body">
                    <h3 class="game-title">Ha/Yo'q</h3>
                    <p class="game-desc">Berilgan tasdiqlarni o'qing va ular to'g'ri yoki noto'g'riligini tez aniqlang.</p>
                    <div class="game-foot">
                        <span>Boshlash</span>
                        <span class="arrow"><i class="bi bi-arrow-right"></i></span>
                    </div>
                </div>
            </a>

            {{-- Keyingi o'yin uchun joy --}}
            <div class="game-card soon">
                <div class="game-cover">
                    <i class="bi bi-puzzle"></i>
                </div>
                <div class="game-body">
                    <h3 class="game-title">Yangi o'yin</h3>
                    <p class="game-desc">Tez orada yangi o'yin qo'shiladi.</p>
                    <div class="game-foot">
                        <span>Tez orada</span>
                        <span class="arrow" style="background: var(--bg-soft);"><i
                                class="bi bi-hourglass-split"></i></span>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
