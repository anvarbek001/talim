<!DOCTYPE html>
<html lang="uz" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Statistika — DarsQil Admin</title>

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
            background: var(--coral-soft);
            color: var(--coral);
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
            background: linear-gradient(135deg, var(--coral), #FF9B7B);
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

        @media (max-width:767px) {
            .sidebar {
                display: none;
            }

            .main {
                margin-left: 0;
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
        }

        /* ===== SHARED COMPONENTS ===== */
        .page-head {
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .page-head h1 {
            font-size: 1.5rem;
            margin: 4px 0 6px;
        }

        .page-sub {
            color: var(--muted);
            font-size: .88rem;
            margin: 0;
            max-width: 680px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 22px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 18px;
        }

        .card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            gap: 10px;
            flex-wrap: wrap;
        }

        .card-title {
            font-weight: 700;
            font-size: 1rem;
        }

        .card-sub-text {
            font-size: .82rem;
            color: var(--muted);
            margin-top: 2px;
        }

        .table-wrap {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            min-width: 560px;
            border-collapse: collapse;
            font-size: .86rem;
        }

        .data-table th,
        .data-table td {
            padding: 12px 14px;
            text-align: left;
            vertical-align: middle;
        }

        .data-table th {
            color: var(--muted);
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
            border-bottom: 1.5px solid var(--line);
            white-space: nowrap;
        }

        .data-table tbody tr {
            border-bottom: 1px solid var(--line);
        }

        .data-table tbody tr:last-child {
            border-bottom: none;
        }

        .data-table tbody tr:hover {
            background: var(--bg-soft);
        }

        .cell-strong {
            font-weight: 700;
        }

        .cell-muted {
            color: var(--muted);
            font-size: .82rem;
        }

        .badge-role {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: .72rem;
            font-weight: 700;
            background: var(--mint-soft);
            color: var(--mint);
            text-transform: capitalize;
        }

        .badge-role.pending {
            background: var(--amber-soft);
            color: #8A6100;
        }

        .stat-card {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .stat-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .stat-value {
            font-size: 1.55rem;
            font-weight: 800;
            font-family: 'Sora', sans-serif;
        }

        .stat-delta {
            font-size: .78rem;
            color: var(--mint);
            font-weight: 600;
        }

        /* ===== TOAST ===== */
        .toast-stack {
            position: fixed;
            top: 22px;
            right: 22px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 360px;
        }

        .toast-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 14px 16px;
            box-shadow: var(--shadow);
            animation: toastIn .35s cubic-bezier(.2, .7, .3, 1) both;
        }

        .toast-item.hide {
            animation: toastOut .3s ease forwards;
        }

        .toast-icon {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .95rem;
        }

        .toast-item.success .toast-icon {
            background: var(--mint-soft);
            color: var(--mint);
        }

        .toast-item.error .toast-icon {
            background: var(--coral-soft);
            color: var(--coral);
        }

        .toast-body {
            flex: 1;
            min-width: 0;
        }

        .toast-title {
            font-weight: 700;
            font-size: .88rem;
            margin-bottom: 2px;
        }

        .toast-text {
            font-size: .82rem;
            color: var(--muted);
            line-height: 1.4;
            word-break: break-word;
        }

        .toast-close {
            background: transparent;
            border: none;
            color: var(--muted);
            font-size: 1rem;
            line-height: 1;
            padding: 2px;
            flex-shrink: 0;
        }

        .toast-close:hover {
            color: var(--text);
        }

        @keyframes toastIn {
            from {
                opacity: 0;
                transform: translateX(24px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes toastOut {
            from {
                opacity: 1;
                transform: translateX(0);
            }

            to {
                opacity: 0;
                transform: translateX(24px);
            }
        }

        @media (max-width:767px) {
            .toast-stack {
                left: 14px;
                right: 14px;
                top: 14px;
                max-width: none;
            }
        }
    </style>
</head>

<body>

    <aside class="sidebar">
        <div class="brand">
            <div class="dot">D</div><span>DarsQil</span>
        </div>

        <div class="nav-eyebrow">Boshqaruv</div>
        <a href="{{ route('universities.index') }}"
            class="nav-link {{ request()->routeIs('universities.index') ? 'active' : '' }}"><i
                class="bi bi-bar-chart-line"></i>
            Statistika</a>
        <a href="{{ route('guruhs.index') }}" class="nav-link {{ request()->routeIs('guruhs.index') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Guruhlarim
        </a>

        <a href="#" class="nav-link">
            <i class="bi bi-journal-bookmark"></i> Darslarim
        </a>

        <a href="#" class="nav-link">
            <i class="bi bi-book"></i> Qo'llanmalar
        </a>

        <div class="sidebar-foot">
            <div class="mini-avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name ?? '', 0, 2)) }}</div>
            <div>
                <div style="font-size:.85rem;font-weight:700;">{{ auth()->user()->name }}</div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-icon-btn" style="background: transparent;" title="Chiqish"><i
                        class="bi bi-box-arrow-right"></i></button>
            </form>
        </div>
    </aside>

    <div class="main">
        <div class="topbar">
            <div class="greet">
                <div class="greet-eyebrow">{{ auth()->user()->university->title ?? '' }}</div>
                <h1>{{ auth()->user()->name ?? '' }}</h1>
            </div>
            <div class="topbar-right">
                <button class="icon-btn theme-toggle" id="themeToggle" title="Rejimni almashtirish">
                    <i class="bi bi-moon-stars-fill"></i>
                    <i class="bi bi-sun-fill"></i>
                </button>
                <a href="#" class="icon-btn" title="Chiqish"><i class="bi bi-box-arrow-right"></i></a>
                <div class="avatar-lg">{{ mb_strtoupper(mb_substr(auth()->user()->name ?? '', 0, 2)) }}</div>
            </div>
        </div>

        @yield('body')

        <div class="toast-stack" id="toastStack"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script>
        const root = document.documentElement;
        const toggleBtn = document.getElementById('themeToggle');
        const saved = localStorage.getItem('darsqil-theme');
        if (saved) root.setAttribute('data-theme', saved);

        toggleBtn.addEventListener('click', () => {
            const next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-theme', next);
            localStorage.setItem('darsqil-theme', next);
        });

        new Chart(document.getElementById('usersChart'), {
            type: 'line',
            data: {
                labels: ['Dush', 'Sesh', 'Chor', 'Pay', 'Jum', 'Shan', 'Yak'],
                datasets: [{
                    label: "Ro'yxatdan o'tishlar",
                    data: [12, 19, 14, 22, 30, 24, 28],
                    borderColor: '#6C5CE7',
                    backgroundColor: 'rgba(108,92,231,.12)',
                    tension: .35,
                    fill: true,
                    pointRadius: 3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('categoryChart'), {
            type: 'doughnut',
            data: {
                labels: ['Kitoblar', 'Video darslar', 'Testlar'],
                datasets: [{
                    data: [45, 32, 23],
                    backgroundColor: ['#6C5CE7', '#12B886', '#FFB020'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });
    </script>

    <script>
        function showToast(type, message, title = null) {
            const stack = document.getElementById('toastStack');
            if (!stack || !message) return;

            const isSuccess = type === 'success';
            const defaultTitle = isSuccess ? 'Muvaffaqiyatli' : 'Xatolik';
            const icon = isSuccess ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill';

            const item = document.createElement('div');
            item.className = `toast-item ${isSuccess ? 'success' : 'error'}`;
            item.innerHTML = `
            <div class="toast-icon"><i class="bi ${icon}"></i></div>
            <div class="toast-body">
                <div class="toast-title">${title || defaultTitle}</div>
                <div class="toast-text">${message}</div>
            </div>
            <button class="toast-close" aria-label="Yopish"><i class="bi bi-x-lg"></i></button>
        `;

            stack.appendChild(item);

            const remove = () => {
                item.classList.add('hide');
                setTimeout(() => item.remove(), 280);
            };

            item.querySelector('.toast-close').addEventListener('click', remove);
            setTimeout(remove, 5000);
        }

        // Backenddan (Laravel session flash) kelgan xabarlarni avtomatik ko'rsatish
        @if (session('success'))
            showToast('success', @json(session('success')));
        @endif

        @if (session('error'))
            showToast('error', @json(session('error')));
        @endif

        @if ($errors->any())
            showToast('error', @json($errors->first()));
        @endif
    </script>

    @stack('scripts')
</body>

</html>
