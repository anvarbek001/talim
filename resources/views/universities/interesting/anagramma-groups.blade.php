@extends('layouts.university')
@section('body')
    <style>
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--muted);
            font-weight: 600;
            font-size: .85rem;
            margin-bottom: 10px;
            transition: .2s;
        }

        .back-link:hover {
            color: var(--primary);
        }

        /* Yaratish formasi */
        .create-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .create-row .input-wrap {
            position: relative;
            flex: 1;
            min-width: 220px;
        }

        .create-row .input-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
        }

        .create-row input {
            width: 100%;
            padding: 12px 14px 12px 40px;
            border-radius: 12px;
            border: 1.5px solid var(--line);
            background: var(--bg-soft);
            color: var(--text);
            font-family: inherit;
            font-size: .92rem;
            outline: none;
            transition: .2s;
        }

        .create-row input:focus {
            border-color: var(--primary);
            background: var(--card);
            box-shadow: 0 0 0 4px var(--primary-soft);
        }

        .btn-main {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 12px 20px;
            border-radius: 12px;
            border: none;
            background: var(--primary);
            color: #fff;
            font-weight: 700;
            font-size: .88rem;
            transition: .2s;
        }

        .btn-main:hover {
            opacity: .9;
            transform: translateY(-2px);
        }

        /* Guruhlar to'ri */
        .groups-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 16px;
        }

        .group-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 20px;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            gap: 16px;
            transition: .25s;
            position: relative;
            overflow: hidden;
        }

        .group-card:hover {
            transform: translateY(-4px);
            border-color: var(--primary);
            box-shadow: var(--shadow);
        }

        .group-top {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .group-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .tone-0 .group-icon {
            background: var(--primary-soft);
            color: var(--primary);
        }

        .tone-1 .group-icon {
            background: var(--mint-soft);
            color: var(--mint);
        }

        .tone-2 .group-icon {
            background: var(--amber-soft);
            color: var(--amber);
        }

        .tone-3 .group-icon {
            background: var(--coral-soft);
            color: var(--coral);
        }

        .group-name {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 1.05rem;
            margin: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .group-date {
            font-size: .75rem;
            color: var(--muted);
            margin-top: 2px;
        }

        .group-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .g-stat {
            background: var(--bg-soft);
            border-radius: 12px;
            padding: 10px 12px;
        }

        .g-stat .lbl {
            font-size: .66rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--muted);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .g-stat .val {
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: 1.25rem;
            margin-top: 2px;
        }

        .group-actions {
            display: flex;
            gap: 8px;
            margin-top: auto;
        }

        .btn-open {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 10px 14px;
            border-radius: 11px;
            background: var(--primary-soft);
            color: var(--primary);
            font-weight: 700;
            font-size: .85rem;
            transition: .2s;
        }

        .btn-open:hover {
            background: var(--primary);
            color: #fff;
        }

        .act-btn {
            width: 40px;
            height: 40px;
            border-radius: 11px;
            border: 1px solid var(--line);
            background: var(--card);
            color: var(--muted);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: .2s;
        }

        .act-btn.danger:hover {
            color: var(--coral);
            border-color: var(--coral);
            background: var(--coral-soft);
        }

        /* Bo'sh holat */
        .empty {
            text-align: center;
            padding: 46px 16px;
            color: var(--muted);
            background: var(--card);
            border: 1.5px dashed var(--line);
            border-radius: 18px;
        }

        .empty i {
            font-size: 2.4rem;
            display: block;
            margin-bottom: 10px;
            color: var(--primary);
        }

        .empty b {
            display: block;
            color: var(--text);
            font-family: 'Sora', sans-serif;
            margin-bottom: 4px;
        }
    </style>

    <div class="page fade-up">
        <a href="{{ route('interesting.index') }}" class="back-link">
            <i class="bi bi-arrow-left"></i> Barcha o'yinlar
        </a>

        <div class="page-head">
            <div>
                <div class="greet-eyebrow">So'z o'yini</div>
                <h1>Anagramma guruhlari</h1>
                <p class="page-sub">Har bir guruh uchun alohida so'zlar, QR kod va natijalar. Guruhni tanlab, uning
                    so'zlarini boshqaring.</p>
            </div>
        </div>

        {{-- Yangi guruh --}}
        <div class="card">
            <div class="card-head">
                <div>
                    <div class="card-title">Yangi guruh</div>
                    <div class="card-sub-text">Masalan: 1-guruh, 2-guruh yoki fan nomi</div>
                </div>
            </div>

            <form action="{{ route('anagramma.groups.store') }}" method="POST" class="create-row" autocomplete="off">
                @csrf
                <div class="input-wrap">
                    <i class="bi bi-people"></i>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="1-guruh" required
                        maxlength="100">
                </div>
                <button type="submit" class="btn-main"><i class="bi bi-plus-lg"></i> Guruh yaratish</button>
            </form>
        </div>

        {{-- Guruhlar --}}
        @if ($groups->isEmpty())
            <div class="empty">
                <i class="bi bi-collection"></i>
                <b>Hali guruh yo'q</b>
                Yuqoridagi forma orqali birinchi guruhni yarating.
            </div>
        @else
            <div class="groups-grid">
                @foreach ($groups as $g)
                    <div class="group-card tone-{{ $loop->index % 4 }}">
                        <div class="group-top">
                            <div class="group-icon"><i class="bi bi-shuffle"></i></div>
                            <div style="min-width:0;">
                                <h3 class="group-name" title="{{ $g->name }}">{{ $g->name }}</h3>
                                <div class="group-date">{{ $g->created_at->format('d.m.Y') }} da yaratilgan</div>
                            </div>
                        </div>

                        <div class="group-stats">
                            <div class="g-stat">
                                <div class="lbl"><i class="bi bi-fonts"></i> So'zlar</div>
                                <div class="val">{{ $g->anagrams_count }}</div>
                            </div>
                            <div class="g-stat">
                                <div class="lbl"><i class="bi bi-trophy"></i> Natijalar</div>
                                <div class="val">{{ $g->results_count }}</div>
                            </div>
                        </div>

                        <div class="group-actions">
                            <a href="{{ route('anagramma.show', $g) }}" class="btn-open">
                                Ochish <i class="bi bi-arrow-right"></i>
                            </a>
                            <form action="{{ route('anagramma.groups.destroy', $g) }}" method="POST"
                                onsubmit="return confirm('Guruh va barcha so\'zlari o\'chadi. Davom etasizmi?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="act-btn danger" title="O'chirish">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
