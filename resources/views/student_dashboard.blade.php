<!DOCTYPE html>
<html lang="uz" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>O'quvchi kabineti — Darslik</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap"
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
            --shadow: 0 14px 34px -16px rgba(30, 25, 60, .14);
            --shadow-sm: 0 6px 16px -8px rgba(30, 25, 60, .10);
            --rail-w: 236px;
        }

        [data-theme="dark"] {
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
            --shadow: 0 14px 34px -16px rgba(0, 0, 0, .55);
            --shadow-sm: 0 6px 16px -8px rgba(0, 0, 0, .4);
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            overflow-x: hidden;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            transition: background .35s ease, color .35s ease;
            -webkit-tap-highlight-color: transparent;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        button {
            font-family: inherit;
            cursor: pointer;
        }

        h1,
        h2,
        h3,
        .display {
            font-family: 'Sora', sans-serif;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-up {
            animation: fadeUp .55s cubic-bezier(.2, .7, .3, 1) both;
        }

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: var(--rail-w);
            background: var(--card);
            border-right: 1px solid var(--line);
            padding: 24px 18px;
            display: flex;
            flex-direction: column;
            z-index: 40;
            transition: background .35s ease, border-color .35s ease;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
            padding: 0 6px;
        }

        .brand .dot {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary), #9C8CFF);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 800;
            font-family: 'Sora', sans-serif;
        }

        .brand span {
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: 1.15rem;
        }

        .nav-eyebrow {
            font-size: .68rem;
            letter-spacing: .09em;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 700;
            margin: 16px 8px 8px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 11px;
            color: var(--muted);
            font-weight: 600;
            font-size: .9rem;
            margin-bottom: 3px;
            transition: .2s;
        }

        .nav-link i {
            font-size: 1.05rem;
            width: 20px;
            text-align: center;
        }

        .nav-link:hover {
            background: var(--bg-soft);
            color: var(--text);
        }

        .nav-link.active {
            background: var(--primary-soft);
            color: var(--primary);
        }

        .sidebar-foot {
            margin-top: auto;
            border-top: 1px solid var(--line);
            padding-top: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .mini-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--mint-soft);
            color: #0C6B4F;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-family: 'Sora', sans-serif;
        }

        .logout-link {
            color: var(--coral) !important;
        }

        .logout-link:hover {
            background: var(--coral-soft) !important;
        }

        .logout-icon-btn {
            margin-left: auto;
            width: 34px;
            height: 34px;
            border-radius: 9px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--muted);
            font-size: 1rem;
            transition: .2s;
        }

        .logout-icon-btn:hover {
            background: var(--coral-soft);
            color: var(--coral);
        }

        /* MAIN */
        .main {
            margin-left: var(--rail-w);
            min-height: 100vh;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 22px 32px;
            position: sticky;
            top: 0;
            background: var(--bg);
            z-index: 30;
            transition: background .35s ease;
        }

        .greet {
            min-width: 0;
            flex: 1 1 auto;
            overflow: hidden;
        }

        .greet-eyebrow {
            font-size: .72rem;
            letter-spacing: .09em;
            text-transform: uppercase;
            color: var(--primary);
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .greet h1 {
            font-size: 1.5rem;
            margin: 4px 0 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-shrink: 0;
        }

        .icon-btn {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--card);
            border: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--muted);
            font-size: 1.05rem;
            box-shadow: var(--shadow-sm);
            position: relative;
            transition: .2s;
        }

        .icon-btn:hover {
            color: var(--text);
            transform: translateY(-2px);
        }

        .icon-btn .dot {
            position: absolute;
            top: 8px;
            right: 9px;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--coral);
        }

        .theme-toggle .bi-sun-fill {
            display: none;
        }

        [data-theme="dark"] .theme-toggle .bi-moon-stars-fill {
            display: none;
        }

        [data-theme="dark"] .theme-toggle .bi-sun-fill {
            display: inline;
        }

        .avatar-lg {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--mint), #33D6A0);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-family: 'Sora', sans-serif;
        }

        .page {
            padding: 6px 32px 50px;
        }

        /* STAT CARDS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 22px;
        }

        .stat-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 20px;
            box-shadow: var(--shadow-sm);
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow);
        }

        .stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            margin-bottom: 14px;
        }

        .stat-num {
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: 1.7rem;
        }

        .stat-lbl {
            color: var(--muted);
            font-size: .82rem;
            margin-top: 2px;
        }

        .streak-flame {
            display: inline-block;
            animation: flamePulse 1.4s ease-in-out infinite;
        }

        @keyframes flamePulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.15);
            }
        }

        /* CONTINUE WATCHING */
        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 22px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 18px;
            transition: background .35s ease, border-color .35s ease;
        }

        .card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .card-title {
            font-weight: 700;
            font-size: 1rem;
        }

        .card-link {
            font-size: .82rem;
            color: var(--primary);
            font-weight: 600;
        }

        .cw-scroll {
            display: flex;
            gap: 14px;
            overflow-x: auto;
            padding-bottom: 4px;
            margin: 0 -2px;
        }

        .cw-scroll::-webkit-scrollbar {
            height: 0;
        }

        .cw-card {
            flex: 0 0 auto;
            width: 230px;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: .25s;
        }

        .cw-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow);
        }

        .ring {
            --p: 0;
            width: 78px;
            height: 78px;
            border-radius: 50%;
            background: conic-gradient(var(--ring-color, var(--primary)) calc(var(--p) * 3.6deg), var(--line) 0deg);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            margin-bottom: 14px;
            transition: background 1.1s cubic-bezier(.2, .8, .3, 1);
        }

        .ring::before {
            content: "";
            position: absolute;
            inset: 7px;
            background: var(--card);
            border-radius: 50%;
        }

        .ring .ring-val {
            position: relative;
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: .95rem;
        }

        .cw-title {
            font-weight: 700;
            font-size: .87rem;
            margin-bottom: 2px;
        }

        .cw-sub {
            font-size: .74rem;
            color: var(--muted);
            margin-bottom: 12px;
        }

        .cw-btn {
            width: 100%;
            padding: 9px;
            border-radius: 10px;
            border: none;
            background: var(--primary-soft);
            color: var(--primary);
            font-weight: 700;
            font-size: .82rem;
            transition: .2s;
        }

        .cw-btn:hover {
            background: var(--primary);
            color: #fff;
        }

        /* Goal progress */
        .goal-track {
            height: 12px;
            background: var(--bg-soft);
            border-radius: 20px;
            overflow: hidden;
            margin: 12px 0 8px;
        }

        .goal-fill {
            height: 100%;
            border-radius: 20px;
            background: linear-gradient(90deg, var(--mint), #33D6A0);
            width: 0%;
            transition: width 1.2s cubic-bezier(.2, .8, .3, 1);
        }

        /* Recommended */
        .rec-scroll {
            display: flex;
            gap: 14px;
            overflow-x: auto;
            padding-bottom: 4px;
        }

        .rec-card {
            flex: 0 0 auto;
            width: 170px;
            border: 1px solid var(--line);
            border-radius: 14px;
            overflow: hidden;
            transition: .25s;
        }

        .rec-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow);
        }

        .rec-thumb {
            height: 88px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.2rem;
        }

        .rec-body {
            padding: 10px 12px;
        }

        .rec-title {
            font-size: .8rem;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .rec-sub {
            font-size: .7rem;
            color: var(--muted);
            margin-top: 2px;
        }

        /* Leaderboard */
        .lb-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid var(--line);
        }

        .lb-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .lb-row.me {
            background: var(--primary-soft);
            border-radius: 12px;
            padding: 10px 12px;
            border-bottom: none;
            margin: 4px 0;
        }

        .lb-rank {
            width: 22px;
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            color: var(--muted);
            font-size: .85rem;
            text-align: center;
        }

        .lb-avatar {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: .72rem;
            font-family: 'Sora', sans-serif;
        }

        .lb-name {
            font-weight: 600;
            font-size: .85rem;
            flex: 1;
        }

        .lb-pts {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: .82rem;
            color: var(--muted);
        }

        /* Subscription card */
        .sub-card {
            background: linear-gradient(135deg, var(--primary), #9C8CFF);
            color: #fff;
            border-radius: 16px;
            padding: 20px;
        }

        .sub-plan {
            font-weight: 800;
            font-family: 'Sora', sans-serif;
            font-size: 1.1rem;
        }

        .sub-sub {
            font-size: .8rem;
            opacity: .85;
            margin-top: 2px;
        }

        .sub-track {
            height: 6px;
            background: rgba(255, 255, 255, .25);
            border-radius: 10px;
            overflow: hidden;
            margin: 14px 0 8px;
        }

        .sub-fill {
            height: 100%;
            background: #fff;
            border-radius: 10px;
            width: 0%;
            transition: width 1.2s ease;
        }

        .sub-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff;
            color: var(--primary);
            font-weight: 700;
            font-size: .8rem;
            padding: 8px 14px;
            border-radius: 9px;
            margin-top: 10px;
            border: none;
        }

        /* BOTTOM NAV */
        .bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--card);
            border-top: 1px solid var(--line);
            padding: 8px 4px calc(8px + env(safe-area-inset-bottom));
            z-index: 50;
            justify-content: space-around;
            box-shadow: 0 -8px 20px -14px rgba(0, 0, 0, .15);
        }

        .bn-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
            color: var(--muted);
            font-size: 1.15rem;
            padding: 4px 8px;
            border-radius: 10px;
            background: none;
            border: none;
        }

        .bn-link span {
            font-size: .6rem;
            font-weight: 700;
        }

        .bn-link.active {
            color: var(--primary);
        }

        /* ===== BOTTOM SHEET (all sections, mobile) ===== */
        .sheet-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 15, 20, .5);
            z-index: 60;
            opacity: 0;
            transition: opacity .25s ease;
        }

        .sheet-overlay.show {
            display: block;
            opacity: 1;
        }

        .bottom-sheet {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--card);
            border-radius: 20px 20px 0 0;
            z-index: 61;
            padding: 10px 18px calc(18px + env(safe-area-inset-bottom));
            transform: translateY(100%);
            transition: transform .3s cubic-bezier(.2, .8, .3, 1);
            max-height: 78vh;
            overflow-y: auto;
            box-shadow: 0 -10px 40px rgba(0, 0, 0, .2);
        }

        .bottom-sheet.show {
            transform: translateY(0);
        }

        .sheet-handle {
            width: 38px;
            height: 4px;
            border-radius: 4px;
            background: var(--line);
            margin: 6px auto 14px;
        }

        .sheet-title {
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .06em;
            font-size: .7rem;
            margin-bottom: 10px;
        }

        .sheet-link {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 13px 8px;
            border-radius: 12px;
            color: var(--text);
            font-weight: 600;
            font-size: .92rem;
        }

        .sheet-link i {
            font-size: 1.2rem;
            width: 22px;
            text-align: center;
            color: var(--muted);
        }

        .sheet-link:active {
            background: var(--bg-soft);
        }

        .sheet-divider {
            height: 1px;
            background: var(--line);
            margin: 8px 0;
        }

        .sheet-link.logout-link i {
            color: var(--coral);
        }

        .grid-main {
            display: grid;
            grid-template-columns: 1.6fr 1fr;
            gap: 18px;
        }

        @media (max-width:1100px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .grid-main {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width:767px) {
            .sidebar {
                display: none;
            }

            .main {
                margin-left: 0;
                padding-bottom: 76px;
            }

            .bottom-nav {
                display: flex;
            }

            .topbar {
                padding: 16px 18px;
            }

            .page {
                padding: 0 18px 30px;
            }

            .greet h1 {
                font-size: 1.2rem;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .stat-card {
                padding: 15px;
            }

            .stat-num {
                font-size: 1.3rem;
            }

            .card {
                padding: 16px;
            }

            .cw-card {
                width: 200px;
            }

            .icon-btn {
                width: 38px;
                height: 38px;
                font-size: .95rem;
            }

            .avatar-lg {
                width: 38px;
                height: 38px;
            }

            .topbar-right {
                gap: 8px;
            }
        }

        @media (max-width:420px) {

            .topbar-right .icon-btn.bell-btn,
            .topbar-right .icon-btn.logout-topbar-btn {
                display: none;
            }

            .greet-eyebrow {
                font-size: .66rem;
            }

            .greet h1 {
                font-size: 1.05rem;
            }

            .stat-icon {
                width: 36px;
                height: 36px;
                font-size: 1rem;
                margin-bottom: 10px;
            }

            .stat-num {
                font-size: 1.15rem;
            }

            .stat-lbl {
                font-size: .76rem;
            }

            .cw-card {
                width: 172px;
                padding: 14px;
            }

            .ring {
                width: 64px;
                height: 64px;
            }

            .rec-card {
                width: 150px;
            }
        }

        .cw-scroll,
        .rec-scroll {
            scroll-snap-type: x proximity;
            scroll-padding-left: 2px;
        }

        .cw-card,
        .rec-card {
            scroll-snap-align: start;
        }
    </style>
</head>

<body>

    <aside class="sidebar">
        <div class="brand">
            <div class="dot">D</div><span>Darslik</span>
        </div>

        <div class="nav-eyebrow">Umumiy</div>
        <a href="#" class="nav-link active"><i class="bi bi-grid-1x2-fill"></i> Bosh sahifa</a>
        <a href="#" class="nav-link"><i class="bi bi-camera-reels"></i> Darslarim</a>
        <a href="#" class="nav-link"><i class="bi bi-graph-up"></i> Progressim</a>

        <div class="nav-eyebrow">Yutuqlar</div>
        <a href="#" class="nav-link"><i class="bi bi-award"></i> Sertifikatlarim</a>
        <a href="#" class="nav-link"><i class="bi bi-trophy"></i> Reyting</a>

        <div class="nav-eyebrow">Hisob</div>
        <a href="#" class="nav-link"><i class="bi bi-credit-card-2-front"></i> Obunam</a>
        <a href="#" class="nav-link"><i class="bi bi-gear"></i> Sozlamalar</a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
        <a href="{{ route('logout') }}" class="nav-link logout-link"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bi bi-box-arrow-right"></i> Chiqish
        </a>

        <div class="sidebar-foot">
            <div class="mini-avatar">MJ</div>
            <div>
                <div style="font-size:.85rem;font-weight:700;">Madina Jo'rayeva</div>
                <div style="font-size:.72rem;color:var(--muted);">Standart obuna</div>
            </div>
            <a href="#" class="logout-icon-btn" title="Chiqish"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </aside>

    <div class="main">
        <div class="topbar">
            <div class="greet">
                <div class="greet-eyebrow">Xayrli kun</div>
                <h1>Salom, Madina!</h1>
            </div>
            <div class="topbar-right">
                <button class="icon-btn theme-toggle" id="themeToggle" title="Rejimni almashtirish">
                    <i class="bi bi-moon-stars-fill"></i>
                    <i class="bi bi-sun-fill"></i>
                </button>
                <button class="icon-btn bell-btn"><i class="bi bi-bell"></i><span class="dot"></span></button>
                <a href="{{ route('logout') }}" class="icon-btn logout-topbar-btn" title="Chiqish"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
                <div class="avatar-lg">MJ</div>
            </div>
        </div>

        <div class="page">
            <!-- STATS -->
            <div class="stats-grid">
                <div class="stat-card fade-up" style="animation-delay:.02s;">
                    <div class="stat-icon" style="background:var(--mint-soft);color:var(--mint);"><i
                            class="bi bi-check2-circle"></i></div>
                    <div class="stat-num" data-count="34">0</div>
                    <div class="stat-lbl">Tugallangan darslar</div>
                </div>
                <div class="stat-card fade-up" style="animation-delay:.08s;">
                    <div class="stat-icon" style="background:var(--primary-soft);color:var(--primary);"><i
                            class="bi bi-clock-history"></i></div>
                    <div class="stat-num" data-count="126">0</div>
                    <div class="stat-lbl">Umumiy soat</div>
                </div>
                <div class="stat-card fade-up" style="animation-delay:.14s;">
                    <div class="stat-icon" style="background:var(--coral-soft);color:var(--coral);"><i
                            class="bi bi-fire streak-flame"></i></div>
                    <div class="stat-num" data-count="12">0</div>
                    <div class="stat-lbl">Kunlik seriya (streak)</div>
                </div>
                <div class="stat-card fade-up" style="animation-delay:.2s;">
                    <div class="stat-icon" style="background:var(--amber-soft);color:#8A6100;"><i
                            class="bi bi-award"></i></div>
                    <div class="stat-num" data-count="5">0</div>
                    <div class="stat-lbl">Sertifikatlar</div>
                </div>
            </div>

            <!-- CONTINUE WATCHING -->
            <div class="card fade-up" style="animation-delay:.1s;">
                <div class="card-head">
                    <div class="card-title">Davom etayotgan darslar</div>
                    <a href="#" class="card-link">Barchasini ko'rish</a>
                </div>
                <div class="cw-scroll">
                    <div class="cw-card">
                        <div class="ring" data-pct="72" style="--ring-color:var(--primary);"><span
                                class="ring-val">72%</span></div>
                        <div class="cw-title">Kvadrat tenglamalar</div>
                        <div class="cw-sub">Matematika · Aziz Karimov</div>
                        <button class="cw-btn">Davom etish</button>
                    </div>
                    <div class="cw-card">
                        <div class="ring" data-pct="45" style="--ring-color:var(--mint);"><span
                                class="ring-val">45%</span></div>
                        <div class="cw-title">Present Simple mavzusi</div>
                        <div class="cw-sub">Ingliz tili · Dilnoza Y.</div>
                        <button class="cw-btn">Davom etish</button>
                    </div>
                    <div class="cw-card">
                        <div class="ring" data-pct="88" style="--ring-color:var(--amber);"><span
                                class="ring-val">88%</span></div>
                        <div class="cw-title">Python — funksiyalar</div>
                        <div class="cw-sub">Dasturlash · Sardor R.</div>
                        <button class="cw-btn">Davom etish</button>
                    </div>
                    <div class="cw-card">
                        <div class="ring" data-pct="20" style="--ring-color:var(--coral);"><span
                                class="ring-val">20%</span></div>
                        <div class="cw-title">Organik kimyo asoslari</div>
                        <div class="cw-sub">Kimyo · Nilufar M.</div>
                        <button class="cw-btn">Davom etish</button>
                    </div>
                </div>
            </div>

            <div class="grid-main">
                <!-- LEFT -->
                <div>
                    <div class="card fade-up" style="animation-delay:.16s;">
                        <div class="card-head">
                            <div class="card-title">Bugungi maqsad</div><span class="card-link"
                                style="color:var(--muted);">45 / 60 daqiqa</span>
                        </div>
                        <div class="goal-track">
                            <div class="goal-fill" data-goal="75"></div>
                        </div>
                        <div style="font-size:.78rem;color:var(--muted);">Maqsadga yetishga 15 daqiqa qoldi — davom
                            eting!</div>
                    </div>

                    <div class="card fade-up" style="animation-delay:.22s;">
                        <div class="card-head">
                            <div class="card-title">Tavsiya etilgan darslar</div>
                            <a href="#" class="card-link">Ko'proq</a>
                        </div>
                        <div class="rec-scroll">
                            <div class="rec-card">
                                <div class="rec-thumb"
                                    style="background:linear-gradient(135deg,var(--primary),#9C8CFF);"><i
                                        class="bi bi-play-fill"></i></div>
                                <div class="rec-body">
                                    <div class="rec-title">Logarifmlar</div>
                                    <div class="rec-sub">Matematika</div>
                                </div>
                            </div>
                            <div class="rec-card">
                                <div class="rec-thumb"
                                    style="background:linear-gradient(135deg,var(--mint),#33D6A0);"><i
                                        class="bi bi-play-fill"></i></div>
                                <div class="rec-body">
                                    <div class="rec-title">Grammar: Modals</div>
                                    <div class="rec-sub">Ingliz tili</div>
                                </div>
                            </div>
                            <div class="rec-card">
                                <div class="rec-thumb"
                                    style="background:linear-gradient(135deg,var(--amber),#FFCB6B);"><i
                                        class="bi bi-play-fill"></i></div>
                                <div class="rec-body">
                                    <div class="rec-title">Massivlar bilan ishlash</div>
                                    <div class="rec-sub">Dasturlash</div>
                                </div>
                            </div>
                            <div class="rec-card">
                                <div class="rec-thumb"
                                    style="background:linear-gradient(135deg,var(--coral),#FF9B9B);"><i
                                        class="bi bi-play-fill"></i></div>
                                <div class="rec-body">
                                    <div class="rec-title">Hujayra bo'linishi</div>
                                    <div class="rec-sub">Biologiya</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT -->
                <div>
                    <div class="card fade-up" style="animation-delay:.14s;">
                        <div class="card-head">
                            <div class="card-title">Reyting (bu hafta)</div>
                        </div>
                        <div class="lb-row">
                            <div class="lb-rank">1</div>
                            <div class="lb-avatar" style="background:var(--amber);color:#4a3300;">B</div>
                            <div class="lb-name">Bekzod Q.</div>
                            <div class="lb-pts">2,480</div>
                        </div>
                        <div class="lb-row">
                            <div class="lb-rank">2</div>
                            <div class="lb-avatar" style="background:var(--mint);">J</div>
                            <div class="lb-name">Jasur T.</div>
                            <div class="lb-pts">2,110</div>
                        </div>
                        <div class="lb-row me">
                            <div class="lb-rank">3</div>
                            <div class="lb-avatar" style="background:var(--primary);">M</div>
                            <div class="lb-name">Siz — Madina J.</div>
                            <div class="lb-pts">1,940</div>
                        </div>
                        <div class="lb-row">
                            <div class="lb-rank">4</div>
                            <div class="lb-avatar" style="background:var(--coral);">Z</div>
                            <div class="lb-name">Zilola S.</div>
                            <div class="lb-pts">1,802</div>
                        </div>
                    </div>

                    <div class="sub-card fade-up" style="animation-delay:.2s;">
                        <div class="sub-plan">Standart reja</div>
                        <div class="sub-sub">Barcha fanlarga to'liq kirish</div>
                        <div class="sub-track">
                            <div class="sub-fill" data-sub="62"></div>
                        </div>
                        <div style="font-size:.75rem;opacity:.85;">Keyingi to'lov: 18 kundan so'ng</div>
                        <button class="sub-btn"><i class="bi bi-arrow-up-circle"></i> Rejani yangilash</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <nav class="bottom-nav">
        <a href="#" class="bn-link active"><i class="bi bi-grid-1x2-fill"></i><span>Bosh</span></a>
        <a href="#" class="bn-link"><i class="bi bi-camera-reels"></i><span>Darslar</span></a>
        <a href="#" class="bn-link"><i class="bi bi-graph-up"></i><span>Progress</span></a>
        <a href="#" class="bn-link"><i class="bi bi-trophy"></i><span>Reyting</span></a>
        <button type="button" class="bn-link" id="moreBtn"><i
                class="bi bi-grid-3x3-gap-fill"></i><span>Ko'proq</span></button>
    </nav>

    <div class="sheet-overlay" id="sheetOverlay"></div>
    <div class="bottom-sheet" id="bottomSheet">
        <div class="sheet-handle"></div>
        <div class="sheet-title">Barcha bo'limlar</div>
        <a href="#" class="sheet-link"><i class="bi bi-bell"></i> Bildirishnomalar</a>
        <a href="#" class="sheet-link"><i class="bi bi-grid-1x2-fill"></i> Bosh sahifa</a>
        <a href="#" class="sheet-link"><i class="bi bi-camera-reels"></i> Darslarim</a>
        <a href="#" class="sheet-link"><i class="bi bi-graph-up"></i> Progressim</a>
        <a href="#" class="sheet-link"><i class="bi bi-award"></i> Sertifikatlarim</a>
        <a href="#" class="sheet-link"><i class="bi bi-trophy"></i> Reyting</a>
        <a href="#" class="sheet-link"><i class="bi bi-credit-card-2-front"></i> Obunam</a>
        <a href="#" class="sheet-link"><i class="bi bi-gear"></i> Sozlamalar</a>
        <div class="sheet-divider"></div>
        <a href="{{ route('logout') }}" class="sheet-link logout-link"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bi bi-box-arrow-right"></i> Chiqish
        </a>
    </div>

    <script>
        // Theme toggle
        const root = document.documentElement;

        // Mobile "more" bottom sheet
        const sheet = document.getElementById('bottomSheet');
        const sheetOverlay = document.getElementById('sheetOverlay');
        const moreBtn = document.getElementById('moreBtn');

        function openSheet() {
            sheet.classList.add('show');
            sheetOverlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeSheet() {
            sheet.classList.remove('show');
            sheetOverlay.classList.remove('show');
            document.body.style.overflow = '';
        }
        moreBtn?.addEventListener('click', openSheet);
        sheetOverlay?.addEventListener('click', closeSheet);
        const toggleBtn = document.getElementById('themeToggle');
        const saved = localStorage.getItem('darslik-theme');
        if (saved) root.setAttribute('data-theme', saved);

        toggleBtn.addEventListener('click', () => {
            const next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-theme', next);
            localStorage.setItem('darslik-theme', next);
        });

        // Animated count-up numbers
        document.querySelectorAll('.stat-num[data-count]').forEach(el => {
            const target = parseInt(el.getAttribute('data-count'), 10);
            const duration = 1000;
            const start = performance.now();

            function frame(now) {
                const progress = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                el.textContent = Math.floor(eased * target);
                if (progress < 1) requestAnimationFrame(frame);
                else el.textContent = target;
            }
            requestAnimationFrame(frame);
        });

        // Animated circular progress rings
        document.querySelectorAll('.ring[data-pct]').forEach(ring => {
            const target = parseInt(ring.getAttribute('data-pct'), 10);
            let current = 0;
            const duration = 1100;
            const start = performance.now();

            function frame(now) {
                const progress = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                current = eased * target;
                ring.style.setProperty('--p', current.toFixed(1));
                if (progress < 1) requestAnimationFrame(frame);
                else ring.style.setProperty('--p', target);
            }
            requestAnimationFrame(frame);
        });

        // Goal & subscription bar fill-in
        requestAnimationFrame(() => {
            document.querySelectorAll('.goal-fill[data-goal]').forEach(el => {
                el.style.width = el.getAttribute('data-goal') + '%';
            });
            document.querySelectorAll('.sub-fill[data-sub]').forEach(el => {
                el.style.width = el.getAttribute('data-sub') + '%';
            });
        });
    </script>
</body>

</html>
