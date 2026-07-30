@extends('layouts.student')

@section('content')
    <div class="page">
        <div class="page-head fade-up">
            <h1>Testlarim</h1>
            <p class="page-sub">O'qituvchilar tomonidan yaratilgan testlarni yeching, natijalaringizni shu yerdan kuzating.</p>
        </div>

        @if (session('success'))
            <div class="alert-success fade-up"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
        @endif

        <div class="card fade-up" style="animation-delay:.06s;">
            <div class="card-head">
                <div class="card-title">Mavjud testlar</div>
            </div>

            @if ($catalog->isEmpty())
                <div class="empty-hint">
                    <i class="bi bi-info-circle"></i>
                    Hozircha hech qanday test yaratilmagan.
                </div>
            @else
                <div class="test-grid">
                    @foreach ($catalog as $item)
                        <div class="test-card fade-up" style="animation-delay:{{ min($loop->index * 0.05, 0.3) }}s;">
                            <div class="test-card-top">
                                <span class="test-type-chip test-type-{{ $item['type'] }}">
                                    @if ($item['type'] === 'topic') <i class="bi bi-bookmark"></i> Mavzu
                                    @elseif ($item['type'] === 'dtm') <i class="bi bi-mortarboard"></i> DTM
                                    @else <i class="bi bi-patch-check"></i> Sertifikat
                                    @endif
                                </span>
                                @if ($item['has_written'])
                                    <span class="test-written-chip" title="Yozma qism mavjud"><i class="bi bi-pencil-square"></i></span>
                                @endif
                            </div>
                            <div class="test-card-title">{{ $item['title'] }}</div>
                            <div class="test-card-meta">
                                <span><i class="bi bi-book"></i> {{ $item['science']->title ?? '—' }}</span>
                                <span><i class="bi bi-info-circle"></i> {{ $item['extra'] }}</span>
                            </div>
                            <div class="test-card-stats">
                                <span><i class="bi bi-patch-question"></i> {{ $item['questions_count'] }} savol</span>
                                <span><i class="bi bi-clock"></i> {{ $item['duration_minutes'] }} daq</span>
                            </div>
                            <form action="{{ route('student-tests.start', [$item['type'], $item['id']]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-primary test-card-btn">
                                    <i class="bi bi-play-fill"></i> Testni boshlash
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card fade-up" style="animation-delay:.12s;">
            <div class="card-head">
                <div class="card-title">Mening urinishlarim</div>
            </div>

            @if ($attempts->isEmpty())
                <div class="empty-hint">
                    <i class="bi bi-info-circle"></i>
                    Hali birorta test yechilmagan.
                </div>
            @else
                <div class="table-wrap">
                    <table class="attempts-table">
                        <thead>
                            <tr>
                                <th>Test</th>
                                <th>Holati</th>
                                <th>Natija</th>
                                <th>Sana</th>
                                <th class="text-end">Amallar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($attempts as $attempt)
                                <tr>
                                    <td class="attempt-title-cell" data-label="Test">{{ $attempt->testable->title ?? "O'chirilgan test" }}</td>
                                    <td data-label="Holati">
                                        @if ($attempt->isInProgress())
                                            <span class="badge-pill wait">Jarayonda</span>
                                        @elseif ($attempt->hasPendingGrading())
                                            <span class="badge-pill wait">Baholanmoqda</span>
                                        @else
                                            <span class="badge-pill on">Yakunlangan</span>
                                        @endif
                                    </td>
                                    <td data-label="Natija">
                                        @if ($attempt->isSubmitted() && !$attempt->hasPendingGrading())
                                            {{ $attempt->score }} / {{ $attempt->max_score }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td data-label="Sana">{{ $attempt->created_at->diffForHumans() }}</td>
                                    <td class="text-end" data-label="Amallar">
                                        @if ($attempt->isInProgress())
                                            <a href="{{ route('student-tests.show', $attempt) }}" class="card-link">Davom ettirish</a>
                                        @else
                                            <a href="{{ route('student-tests.result', $attempt) }}" class="card-link">Natijani ko'rish</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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

        .test-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 16px;
        }

        .test-card {
            background: var(--bg-soft);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .test-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow);
        }

        .test-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .test-type-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: .72rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            background: var(--primary-soft);
            color: var(--primary);
        }

        .test-type-dtm {
            background: var(--amber-soft);
            color: #8A6100;
        }

        .test-type-sertifikat {
            background: var(--mint-soft);
            color: var(--mint);
        }

        .test-written-chip {
            width: 26px;
            height: 26px;
            border-radius: 8px;
            background: var(--coral-soft);
            color: var(--coral);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .8rem;
        }

        .test-card-title {
            font-weight: 700;
            font-size: .96rem;
            line-height: 1.35;
        }

        .test-card-meta {
            display: flex;
            flex-direction: column;
            gap: 4px;
            font-size: .78rem;
            color: var(--muted);
        }

        .test-card-meta i {
            width: 16px;
            text-align: center;
        }

        .test-card-stats {
            display: flex;
            gap: 14px;
            font-size: .78rem;
            font-weight: 600;
            color: var(--text);
        }

        .test-card-btn {
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

        .table-wrap {
            overflow-x: auto;
        }

        .attempts-table {
            width: 100%;
            border-collapse: collapse;
            font-size: .86rem;
        }

        .attempts-table th,
        .attempts-table td {
            padding: 12px 14px;
            text-align: left;
            white-space: nowrap;
        }

        .attempts-table th {
            color: var(--muted);
            font-size: .74rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
            border-bottom: 1.5px solid var(--line);
        }

        .attempts-table tbody tr {
            border-bottom: 1px solid var(--line);
        }

        .attempts-table tbody tr:last-child {
            border-bottom: none;
        }

        .attempt-title-cell {
            font-weight: 700;
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

        .text-end {
            text-align: right;
        }

        @media (max-width:767px) {
            .page-head h1 {
                font-size: 1.25rem;
            }

            .test-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .test-card {
                padding: 16px;
            }

            .card {
                padding: 16px;
            }
        }

        @media (max-width:640px) {
            .attempts-table thead {
                display: none;
            }

            .attempts-table,
            .attempts-table tbody,
            .attempts-table tr,
            .attempts-table td {
                display: block;
                width: 100%;
            }

            .attempts-table tr {
                border: 1px solid var(--line);
                border-radius: 12px;
                padding: 10px 14px;
                margin-bottom: 10px;
            }

            .attempts-table tr:last-child {
                margin-bottom: 0;
            }

            .attempts-table td {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                border: none;
                padding: 7px 0;
                white-space: normal;
                text-align: right;
            }

            .attempts-table td::before {
                content: attr(data-label);
                font-size: .7rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: .03em;
                color: var(--muted);
                text-align: left;
                flex-shrink: 0;
            }

            .attempt-title-cell {
                font-size: .92rem;
            }

            .text-end {
                text-align: right;
            }
        }
    </style>
@endsection
