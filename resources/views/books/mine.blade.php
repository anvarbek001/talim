@extends('layouts.teacher')

@section('content')
    <div class="page">

        <div class="page-head fade-up mine-head">
            <div>
                <h1>Kitoblarim</h1>
                <p class="page-sub">Joylagan kitob va qo'llanmalaringiz shu yerda to'planadi.</p>
            </div>
            <a href="{{ route('book') }}" class="btn-primary mine-add-btn">
                <i class="bi bi-cloud-upload"></i> Yangi kitob joylash
            </a>
        </div>

        @if (session('success'))
            <div class="mine-alert fade-up">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        <form method="GET" action="{{ route('books.mine') }}" class="mine-filter-bar fade-up">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Kitob nomi bo'yicha qidirish..." class="mine-filter-input">
            <button type="submit" class="btn-ghost"><i class="bi bi-search"></i> Qidirish</button>
            @if (request('q'))
                <a href="{{ route('books.mine') }}" class="mine-filter-reset">Tozalash</a>
            @endif
        </form>

        @if ($books->isEmpty())
            <div class="mine-empty fade-up">
                <div class="mine-empty-icon"><i class="bi bi-journal-bookmark"></i></div>
                @if (request('q'))
                    <div class="mine-empty-title">"{{ request('q') }}" bo'yicha kitob topilmadi</div>
                @else
                    <div class="mine-empty-title">Hali kitob joylanmagan</div>
                    <div class="mine-empty-sub">Birinchi kitobingizni joylab, o'quvchilaringizga yetkazing.</div>
                    <a href="{{ route('book') }}" class="btn-primary">
                        <i class="bi bi-cloud-upload"></i> Kitob joylash
                    </a>
                @endif
            </div>
        @else
            <div class="mine-grid">
                @foreach ($books as $index => $book)
                    <div class="book-card fade-up" style="animation-delay:{{ min($index * 0.06, 0.3) }}s;">
                        <div class="book-thumb">
                            <i class="bi bi-file-earmark-pdf-fill"></i>
                        </div>

                        <div class="book-card-body">
                            <div class="book-card-title">{{ $book->title }}</div>

                            <div class="book-chip-row">
                                <span class="book-chip {{ $book->isFree() ? 'book-chip-free' : 'book-chip-paid' }}">
                                    @if ($book->isFree())
                                        <i class="bi bi-unlock"></i> Bepul
                                    @else
                                        <i class="bi bi-cash-coin"></i> {{ number_format($book->price, 0, '.', ' ') }} so'm
                                    @endif
                                </span>
                                <span class="book-chip book-chip-muted">
                                    <i class="bi bi-files"></i> {{ $book->files->count() }} fayl
                                </span>
                                <span class="book-chip book-chip-muted">
                                    <i class="bi bi-clock"></i> {{ $book->created_at->diffForHumans() }}
                                </span>
                            </div>

                            @if ($book->description)
                                <div class="book-card-desc">{{ \Illuminate\Support\Str::limit($book->description, 110) }}</div>
                            @endif

                            <a href="{{ route('books.view', $book) }}" class="btn-ghost book-view-btn">
                                <i class="bi bi-eye"></i> Ko'rish
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            {{ $books->links() }}
        @endif
    </div>

    <style>
        .page-head {
            margin-bottom: 22px;
        }

        .page-head h1 {
            font-size: 1.5rem;
            margin: 4px 0 6px;
        }

        .page-sub {
            color: var(--muted);
            font-size: .88rem;
            margin: 0;
            max-width: 620px;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 22px;
            border-radius: 10px;
            border: none;
            background: var(--primary);
            color: #fff;
            font-weight: 700;
            font-size: .88rem;
            transition: .2s;
        }

        .btn-primary:hover {
            background: #5A4BD6;
        }

        .mine-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .mine-add-btn {
            flex-shrink: 0;
        }

        .mine-alert {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--mint-soft);
            color: var(--mint);
            border-radius: 12px;
            padding: 13px 16px;
            font-weight: 600;
            font-size: .88rem;
            margin-bottom: 20px;
        }

        .mine-empty {
            background: var(--card);
            border: 1px dashed var(--line);
            border-radius: 18px;
            padding: 60px 24px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }

        .mine-empty-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: var(--primary-soft);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 8px;
        }

        .mine-empty-title {
            font-weight: 700;
            font-size: 1.05rem;
        }

        .mine-empty-sub {
            color: var(--muted);
            font-size: .86rem;
            margin-bottom: 18px;
            max-width: 360px;
        }

        .mine-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .book-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .book-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow);
        }

        .book-thumb {
            width: 100%;
            aspect-ratio: 16 / 8;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 2.2rem;
            background: linear-gradient(135deg, var(--coral), #FF9B7B);
        }

        .book-card-body {
            padding: 16px 18px 18px;
        }

        .book-card-title {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 10px;
            line-height: 1.3;
        }

        .book-chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 10px;
        }

        .book-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: .72rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            white-space: nowrap;
        }

        .book-chip-free {
            background: var(--mint-soft);
            color: var(--mint);
        }

        .book-chip-paid {
            background: var(--amber-soft);
            color: #8A6100;
        }

        .book-chip-muted {
            background: var(--bg-soft);
            color: var(--muted);
        }

        .book-card-desc {
            font-size: .82rem;
            color: var(--muted);
            line-height: 1.5;
            margin-bottom: 12px;
        }

        .btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 9px;
            border: 1.5px solid var(--line);
            color: var(--text);
            font-weight: 600;
            font-size: .82rem;
            transition: .2s;
        }

        .btn-ghost:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        @media (max-width:767px) {
            .mine-grid {
                grid-template-columns: 1fr;
            }
        }

        .mine-filter-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .mine-filter-input {
            flex: 1 1 260px;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: .86rem;
            background: var(--card);
            color: var(--text);
        }

        .mine-filter-reset {
            display: inline-flex;
            align-items: center;
            font-size: .84rem;
            font-weight: 600;
            color: var(--muted);
        }

        .mine-filter-reset:hover {
            color: var(--coral);
        }
    </style>
@endsection
