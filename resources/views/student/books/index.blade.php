@extends('layouts.student')

@section('content')
    <div class="page">
        <div class="page-head fade-up">
            <h1>Kitoblarim</h1>
            <p class="page-sub">O'qituvchilar tomonidan joylangan kitob va qo'llanmalarni shu yerdan o'qing.</p>
        </div>

        @if (session('success'))
            <div class="alert-success fade-up"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert-error fade-up"><i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}</div>
        @endif

        <form method="GET" action="{{ route('student-books.index') }}" class="book-filter-bar fade-up" style="animation-delay:.03s;">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Kitob nomi yoki o'qituvchi bo'yicha qidirish..." class="book-filter-input">
            <button type="submit" class="btn-primary book-filter-btn"><i class="bi bi-search"></i> Qidirish</button>
            @if (request('q'))
                <a href="{{ route('student-books.index') }}" class="book-filter-reset">Tozalash</a>
            @endif
        </form>

        <div class="card fade-up" style="animation-delay:.06s;">
            <div class="card-head">
                <div class="card-title">Mavjud kitoblar</div>
            </div>

            @if ($catalog->isEmpty())
                <div class="empty-hint">
                    <i class="bi bi-info-circle"></i>
                    @if (request('q'))
                        "{{ request('q') }}" bo'yicha kitob topilmadi.
                    @else
                        Hozircha hech qanday kitob joylanmagan.
                    @endif
                </div>
            @else
                <div class="book-grid">
                    @foreach ($catalog as $item)
                        <div class="book-card fade-up" style="animation-delay:{{ min($loop->index * 0.05, 0.3) }}s;">
                            <div class="book-card-icon"><i class="bi bi-file-earmark-pdf"></i></div>
                            <div class="book-card-title">{{ $item['title'] }}</div>
                            <div class="book-card-meta">
                                <span><i class="bi bi-person"></i> {{ $item['author'] }}</span>
                                <span><i class="bi bi-files"></i> {{ $item['files_count'] }} fayl</span>
                            </div>
                            @if ($item['description'])
                                <div class="book-card-desc">{{ \Illuminate\Support\Str::limit($item['description'], 90) }}</div>
                            @endif
                            <div class="book-card-price {{ $item['price'] > 0 ? '' : 'is-free' }}">
                                {{ $item['price'] > 0 ? number_format($item['price'], 0, '.', ' ')." so'm" : 'Bepul' }}
                            </div>
                            @if ($item['price'] > 0 && ! $item['purchased'])
                                <form action="{{ route('student-purchases.store', ['book', $item['id']]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-primary book-card-btn">
                                        <i class="bi bi-bag-check"></i> Sotib olish
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('books.view', $item['id']) }}" class="btn-primary book-card-btn">
                                    <i class="bi bi-eye"></i> Ochish
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
                {{ $catalog->links() }}
            @endif
        </div>
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

        .alert-success {
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

        .alert-error {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--coral-soft);
            color: var(--coral);
            border-radius: 12px;
            padding: 13px 16px;
            font-weight: 600;
            font-size: .88rem;
            margin-bottom: 20px;
        }

        .empty-hint {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 11px 13px;
            border-radius: 10px;
            background: var(--amber-soft);
            color: #8A6100;
            font-size: .84rem;
            border: 1px dashed #E0B24C;
        }

        .book-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 16px;
        }

        .book-card {
            background: var(--bg-soft);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .book-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow);
        }

        .book-card-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--coral-soft);
            color: var(--coral);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .book-card-title {
            font-weight: 700;
            font-size: .96rem;
            line-height: 1.35;
        }

        .book-card-meta {
            display: flex;
            flex-direction: column;
            gap: 4px;
            font-size: .78rem;
            color: var(--muted);
        }

        .book-card-meta i {
            width: 16px;
            text-align: center;
        }

        .book-card-desc {
            font-size: .8rem;
            color: var(--muted);
            line-height: 1.4;
        }

        .book-card-price {
            font-weight: 700;
            font-size: .84rem;
            color: var(--primary);
        }

        .book-card-price.is-free {
            color: var(--mint);
        }

        .book-card-btn {
            width: 100%;
            justify-content: center;
            margin-top: 4px;
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
        }

        .btn-primary:hover {
            background: #5A4BD6;
        }

        @media (max-width:767px) {
            .page-head h1 {
                font-size: 1.25rem;
            }

            .book-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .book-card {
                padding: 16px;
            }

            .card {
                padding: 16px;
            }
        }

        .book-filter-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .book-filter-input {
            flex: 1 1 260px;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: .86rem;
            background: var(--card);
            color: var(--text);
        }

        .book-filter-btn {
            flex-shrink: 0;
        }

        .book-filter-reset {
            display: inline-flex;
            align-items: center;
            font-size: .84rem;
            font-weight: 600;
            color: var(--muted);
        }

        .book-filter-reset:hover {
            color: var(--coral);
        }
    </style>
@endsection
