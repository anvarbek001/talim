<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, follow">
    <title>{{ $group->name }} guruhiga taklif — DarsQil</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            --navy: #141B33;
            --navy-deep: #0C1024;
            --gold: #F2A93B;
            --ink: #1B1B18;
            --muted: #6B7280;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: radial-gradient(1200px 500px at 80% -10%, #1c2550 0%, var(--navy) 55%, var(--navy-deep) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        h1, h2, .display-font {
            font-family: 'Sora', sans-serif;
        }

        .invite-card {
            background: #fff;
            border-radius: 20px;
            padding: 44px 38px;
            max-width: 440px;
            width: 100%;
            text-align: center;
            box-shadow: 0 30px 60px -20px rgba(0, 0, 0, .5);
        }

        .invite-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--navy), #2F3B73);
            color: var(--gold);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin: 0 auto 20px;
        }

        .invite-teacher {
            color: var(--muted);
            font-size: .9rem;
            margin-bottom: 4px;
        }

        .btn-gold {
            background: var(--gold);
            color: var(--navy-deep);
            font-weight: 700;
            border-radius: 10px;
            border: none;
            padding: 12px 20px;
        }

        .btn-gold:hover {
            background: #e0961f;
            color: var(--navy-deep);
        }

        .btn-outline-navy {
            border: 1.5px solid var(--navy);
            color: var(--navy);
            font-weight: 600;
            border-radius: 10px;
            padding: 11px 20px;
        }

        .btn-outline-navy:hover {
            background: var(--navy);
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="invite-card">
        <div class="invite-icon"><i class="bi bi-people-fill"></i></div>
        <div class="invite-teacher">{{ $group->teacher->name }} sizni taklif qilmoqda</div>
        <h1 class="h3 mb-3">{{ $group->name }}</h1>
        @if ($group->description)
            <p class="text-muted small mb-4">{{ $group->description }}</p>
        @endif

        @if ($alreadyMember)
            <p class="text-muted small mb-4">Siz allaqachon shu guruh a'zosisiz.</p>
            <a href="{{ route('groups.show', $group) }}" class="btn btn-gold w-100">Guruhga o'tish</a>
        @elseif (auth()->check())
            <form method="POST" action="{{ route('group-invites.accept', $code) }}">
                @csrf
                <button type="submit" class="btn btn-gold w-100">
                    <i class="bi bi-check-lg"></i> Guruhga qo'shilish
                </button>
            </form>
        @else
            <p class="text-muted small mb-4">Guruhga qo'shilish uchun avval hisobingiz bo'lishi kerak.</p>
            <div class="d-flex flex-column gap-2">
                <a href="{{ route('register') }}" class="btn btn-gold">Ro'yxatdan o'tish</a>
                <a href="{{ route('login') }}" class="btn btn-outline-navy">Hisobim bor, kirish</a>
            </div>
        @endif
    </div>
</body>

</html>
