<!DOCTYPE html>
<html lang="uz" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>O'qituvchi kabineti — DarsQil</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
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

        /* ===== SIDEBAR ===== */
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
            background: var(--amber-soft);
            color: #8A6100;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-family: 'Sora', sans-serif;
            overflow: hidden;
            flex-shrink: 0;
        }

        .mini-avatar img,
        .avatar-lg img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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

        /* ===== MAIN ===== */
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
            background: linear-gradient(135deg, var(--primary), #9C8CFF);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-family: 'Sora', sans-serif;
            overflow: hidden;
            flex-shrink: 0;
        }

        .page {
            padding: 6px 32px 50px;
        }

        /* ===== STAT CARDS ===== */
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

        .stat-delta {
            font-size: .74rem;
            font-weight: 700;
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            padding: 3px 9px;
            border-radius: 20px;
        }

        .stat-delta.up {
            color: var(--mint);
            background: var(--mint-soft);
        }

        .stat-delta.down {
            color: var(--coral);
            background: var(--coral-soft);
        }

        /* ===== MAIN GRID ===== */
        .grid-main {
            display: grid;
            grid-template-columns: 1.6fr 1fr;
            gap: 18px;
        }

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
            justify-content: between;
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

        /* Lesson rows */
        .lesson-row {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 0;
            border-bottom: 1px solid var(--line);
        }

        .lesson-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .lesson-thumb {
            width: 64px;
            height: 44px;
            border-radius: 10px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1rem;
        }

        .lesson-info {
            flex: 1;
            min-width: 0;
        }

        .lesson-title {
            font-weight: 600;
            font-size: .9rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .lesson-sub {
            font-size: .76rem;
            color: var(--muted);
            margin-top: 2px;
        }

        .lesson-views {
            font-size: .8rem;
            color: var(--muted);
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .badge-pill {
            font-size: .68rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            white-space: nowrap;
        }

        .badge-pill.on {
            background: var(--mint-soft);
            color: var(--mint);
        }

        .badge-pill.wait {
            background: var(--amber-soft);
            color: #8A6100;
        }

        /* Weekly activity bars */
        .week-chart {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            height: 120px;
            padding-top: 8px;
        }

        .week-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            height: 100%;
            gap: 8px;
        }

        .week-bar {
            width: 100%;
            max-width: 28px;
            border-radius: 8px 8px 4px 4px;
            background: linear-gradient(180deg, var(--primary), #9C8CFF);
            transform: scaleY(0);
            transform-origin: bottom;
            animation: growBar .7s cubic-bezier(.2, .8, .3, 1) forwards;
        }

        @keyframes growBar {
            to {
                transform: scaleY(1);
            }
        }

        .week-lbl {
            font-size: .72rem;
            color: var(--muted);
            font-weight: 600;
        }

        /* Comments */
        .comment-row {
            display: flex;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid var(--line);
        }

        .comment-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .comment-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: .78rem;
            font-family: 'Sora', sans-serif;
        }

        .comment-name {
            font-weight: 700;
            font-size: .85rem;
        }

        .comment-text {
            font-size: .83rem;
            color: var(--muted);
            margin-top: 2px;
            line-height: 1.4;
        }

        .comment-meta {
            font-size: .72rem;
            color: var(--muted);
            margin-top: 6px;
        }

        /* Quick actions */
        .qa-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 14px;
            border-radius: 13px;
            border: 1px solid var(--line);
            background: var(--bg-soft);
            margin-bottom: 10px;
            font-weight: 600;
            font-size: .87rem;
            transition: .2s;
        }

        .qa-btn:hover {
            border-color: var(--primary);
            transform: translateX(3px);
        }

        .qa-btn:last-child {
            margin-bottom: 0;
        }

        .qa-icon {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* ===== BOTTOM NAV (mobile) ===== */
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
            font-size: .95rem;
            margin-bottom: 10px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .06em;
            font-size: .7rem;
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
        }
    </style>
</head>

<body>

    <aside class="sidebar">
        <div class="brand">
            <div class="dot">D</div><span>DarsQil</span>
        </div>

        <div class="nav-eyebrow">Umumiy</div>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> Bosh sahifa
        </a>
        @if (config('features.lessons_enabled'))
            <a href="{{ route('lessons.mine') }}" class="nav-link {{ request()->routeIs('lessons.mine') ? 'active' : '' }}">
                <i class="bi bi-collection-play"></i> Mening darslarim
            </a>
            <a href="{{ route('lesson') }}" class="nav-link {{ request()->routeIs('lesson') ? 'active' : '' }}">
                <i class="bi bi-cloud-upload"></i> Dars joylash
            </a>
        @endif
        <a href="{{ route('tests.index') }}" class="nav-link {{ request()->routeIs('tests.index') ? 'active' : '' }}">
            <i class="bi bi-patch-question"></i> Testlarim
        </a>
        <a href="{{ route('books.mine') }}" class="nav-link {{ request()->routeIs('books.mine') ? 'active' : '' }}">
            <i class="bi bi-journal-bookmark-fill"></i> Kitoblarim
        </a>

        <div class="nav-eyebrow">Auditoriya</div>
        <a href="{{ route('teacher-students.index') }}" class="nav-link {{ request()->routeIs('teacher-students.*') ? 'active' : '' }}">
            <i class="bi bi-mortarboard"></i> O'quvchilarim
        </a>

        <div class="nav-eyebrow">Hisob</div>
        <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="bi bi-gear"></i> Sozlamalar
        </a>
        {{--
        <a href="#" class="nav-link"><i class="bi bi-wallet2"></i> Daromadim</a> --}}

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
        <a href="{{ route('logout') }}" class="nav-link logout-link"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bi bi-box-arrow-right"></i> Chiqish
        </a>

        <div class="sidebar-foot">
            <div class="mini-avatar">
                @if (auth()->user()->avatarUrl())
                    <img src="{{ auth()->user()->avatarUrl() }}" alt="{{ auth()->user()->name }}">
                @else
                    {{ auth()->user()->initials() }}
                @endif
            </div>
            <div>
                <div class="small fw-semibold" style="font-size:.85rem;font-weight:700;">{{ auth()->user()->name }}</div>
                <div style="font-size:.72rem;color:var(--muted);">O'qituvchi</div>
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
                <h1>{{ auth()->user()->name }}</h1>
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
                <div class="avatar-lg">
                    @if (auth()->user()->avatarUrl())
                        <img src="{{ auth()->user()->avatarUrl() }}" alt="{{ auth()->user()->name }}">
                    @else
                        {{ auth()->user()->initials() }}
                    @endif
                </div>
            </div>
        </div>
        @yield('content')
    </div>

    <nav class="bottom-nav">
        <a href="{{ route('dashboard') }}" class="bn-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i><span>Bosh</span>
        </a>
        @if (config('features.lessons_enabled'))
            <a href="{{ route('lessons.mine') }}" class="bn-link {{ request()->routeIs('lessons.mine') ? 'active' : '' }}">
                <i class="bi bi-collection-play"></i><span>Darslarim</span>
            </a>
            <a href="{{ route('lesson') }}" class="bn-link {{ request()->routeIs('lesson') ? 'active' : '' }}">
                <i class="bi bi-cloud-upload"></i><span>Joylash</span>
            </a>
        @endif
        <a href="{{ route('tests.index') }}" class="bn-link {{ request()->routeIs('tests.index') ? 'active' : '' }}">
            <i class="bi bi-mortarboard"></i><span>Testlarim</span>
        </a>
        <a href="{{ route('books.mine') }}" class="bn-link {{ request()->routeIs('books.mine') ? 'active' : '' }}">
            <i class="bi bi-journal-bookmark-fill"></i><span>Kitoblarim</span>
        </a>
        <a href="{{ route('teacher-students.index') }}" class="bn-link {{ request()->routeIs('teacher-students.*') ? 'active' : '' }}">
            <i class="bi bi-mortarboard"></i> <span>O'quvchilarim</span>
        </a>
        {{-- <button type="button" class="bn-link" id="moreBtn"><i
                class="bi bi-grid-3x3-gap-fill"></i><span>Ko'proq</span></button> --}}
    </nav>

    <div class="sheet-overlay" id="sheetOverlay"></div>
    <div class="bottom-sheet" id="bottomSheet">
        <div class="sheet-handle"></div>
        <div class="sheet-title">Barcha bo'limlar</div>
        <a href="{{ route('profile.edit') }}" class="sheet-link"><i class="bi bi-gear"></i> Sozlamalar</a>
        {{-- <a href="#" class="sheet-link"><i class="bi bi-wallet2"></i> Daromadim</a> --}}
        <div class="sheet-divider"></div>
        <a href="{{ route('logout') }}" class="sheet-link logout-link"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bi bi-box-arrow-right"></i> Chiqish
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
    </script>

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
        const saved = localStorage.getItem('darsqil-theme');
        if (saved) root.setAttribute('data-theme', saved);

        toggleBtn.addEventListener('click', () => {
            const next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-theme', next);
            localStorage.setItem('darsqil-theme', next);
        });

        // Animated count-up numbers
        document.querySelectorAll('.stat-num[data-count]').forEach(el => {
            const target = parseInt(el.getAttribute('data-count'), 10);
            const duration = 1100;
            const start = performance.now();

            function frame(now) {
                const progress = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                const value = Math.floor(eased * target);
                el.textContent = value.toLocaleString('fr-FR').replace(/,/g, ' ');
                if (progress < 1) requestAnimationFrame(frame);
                else el.textContent = target.toLocaleString('fr-FR').replace(/,/g, ' ');
            }
            requestAnimationFrame(frame);
        });
    </script>
</body>

</html>
