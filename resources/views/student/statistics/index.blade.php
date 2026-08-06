@extends('layouts.student')

@php
    $typeChip = fn ($class) => match ($class) {
        \App\Models\TopicTest::class => ['Mavzu', 'var(--primary)'],
        \App\Models\DtmTest::class => ['DTM', 'var(--amber)'],
        \App\Models\SertifikatTest::class => ['Sertifikat', 'var(--mint)'],
        \App\Models\LanguageExamTest::class => ['Til imtihoni', 'var(--coral)'],
        default => ['Test', 'var(--muted)'],
    };
@endphp

@section('content')
    <div class="page">
        <div class="page-head fade-up">
            <div>
                <h1>Progressim</h1>
                <p class="page-sub">Testlar bo'yicha natijalaringiz, sertifikatlaringiz va reytingingiz shu yerda.</p>
            </div>
        </div>

        <div class="progress-tabs fade-up" style="animation-delay:.02s;">
            <button type="button" class="progress-tab is-active" data-tab-btn="umumiy">
                <i class="bi bi-graph-up"></i> Umumiy
            </button>
            <button type="button" class="progress-tab" data-tab-btn="sertifikat">
                <i class="bi bi-award"></i> Sertifikatlarim
            </button>
            <button type="button" class="progress-tab" data-tab-btn="reyting">
                <i class="bi bi-trophy"></i> Reyting
            </button>
        </div>

        <div data-tab-panel="umumiy">
            <div class="stats-grid fade-up" style="animation-delay:.04s;">
                <div class="stat-card">
                    <div class="stat-icon" style="background:var(--primary-soft);color:var(--primary);"><i class="bi bi-patch-question"></i></div>
                    <div class="stat-num">{{ $stats['attempts_submitted'] }}</div>
                    <div class="stat-lbl">Topshirilgan testlar</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:var(--mint-soft);color:var(--mint);"><i class="bi bi-graph-up-arrow"></i></div>
                    <div class="stat-num">{{ $stats['average_percent'] }}%</div>
                    <div class="stat-lbl">O'rtacha ball</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:var(--coral-soft);color:var(--coral);"><i class="bi bi-bookmark-heart"></i></div>
                    <div class="stat-num">{{ $stats['saved_lessons_count'] }}</div>
                    <div class="stat-lbl">Saqlangan darslar</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:var(--amber-soft);color:#8A6100;"><i class="bi bi-camera-reels"></i></div>
                    <div class="stat-num">{{ $stats['total_lessons'] }}</div>
                    <div class="stat-lbl">Platformadagi darslar</div>
                </div>
            </div>

            <div class="card fade-up" style="animation-delay:.1s;">
                <div class="card-head">
                    <div class="card-title">Test turlari bo'yicha</div>
                </div>
                @forelse ($stats['by_type'] as $row)
                    <div class="type-row">
                        <div class="type-row-label">{{ $row['label'] }}</div>
                        <div class="type-row-track">
                            <div class="type-row-fill" style="width:{{ $row['average_percent'] }}%;"></div>
                        </div>
                        <div class="type-row-val">{{ $row['average_percent'] }}%</div>
                        <div class="type-row-count">{{ $row['count'] }} ta</div>
                    </div>
                @empty
                    <div class="stats-empty">Hali birorta test topshirmagansiz.</div>
                @endforelse
            </div>

            <div class="card fade-up" style="animation-delay:.16s;">
                <div class="card-head">
                    <div class="card-title">So'nggi urinishlar</div>
                    <a href="{{ route('student-tests.index') }}" class="card-link">Barcha testlar</a>
                </div>
                @forelse ($stats['recent_attempts'] as $attempt)
                    @php [$label, $color] = $typeChip($attempt->testable_type); @endphp
                    <div class="attempt-row">
                        <span class="attempt-chip" style="background:{{ $color }}1A;color:{{ $color }};">{{ $label }}</span>
                        <div class="attempt-title">{{ $attempt->testable->title ?? "O'chirilgan test" }}</div>
                        <div class="attempt-score">
                            @if ($attempt->hasPendingGrading())
                                <span class="pending-badge">Baholanmoqda</span>
                            @else
                                {{ $attempt->score }} / {{ $attempt->max_score }}
                            @endif
                        </div>
                        <div class="attempt-date">{{ $attempt->submitted_at?->format('d.m.Y') }}</div>
                    </div>
                @empty
                    <div class="stats-empty">Hali birorta test topshirmagansiz.</div>
                @endforelse
            </div>
        </div>

        <div data-tab-panel="sertifikat" hidden>
            <div class="card fade-up certs-empty">
                <div class="certs-empty-icon"><i class="bi bi-award"></i></div>
                <div class="certs-empty-title">Hali sertifikat yo'q</div>
                <div class="certs-empty-sub">Testlarni yakunlab, birinchi sertifikatingizga ega bo'ling!</div>
            </div>
        </div>

        <div data-tab-panel="reyting" hidden>
            <div class="card fade-up">
                <div class="card-head">
                    <div class="card-title">Reyting</div>
                </div>
                @forelse ($leaderboard as $index => $row)
                    <div class="lb-row {{ $row['user']->id === auth()->id() ? 'me' : '' }}">
                        <div class="lb-rank">{{ $index + 1 }}</div>
                        <div class="lb-avatar" style="background:var(--primary-soft);color:var(--primary);">
                            {{ mb_substr($row['user']->name, 0, 1) }}
                        </div>
                        <div class="lb-name">
                            {{ $row['user']->id === auth()->id() ? 'Siz — '.$row['user']->name : $row['user']->name }}
                        </div>
                        <div class="lb-pts">{{ $row['total_score'] }}</div>
                    </div>
                @empty
                    <div class="stats-empty">Hali reyting uchun ma'lumot yo'q.</div>
                @endforelse
            </div>
        </div>
    </div>

    <style>
        .page-head h1 {
            font-size: 1.5rem;
            margin: 4px 0 6px;
        }

        .page-sub {
            color: var(--muted);
            font-size: .88rem;
            margin: 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 22px;
        }

        @media (max-width:1100px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .stat-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 20px;
            box-shadow: var(--shadow-sm);
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

        .progress-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 18px;
            overflow-x: auto;
        }

        .progress-tab {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid var(--line);
            background: var(--card);
            color: var(--muted);
            border-radius: 20px;
            padding: 9px 16px;
            font-size: .84rem;
            font-weight: 700;
            white-space: nowrap;
            cursor: pointer;
            transition: background .15s, color .15s, border-color .15s;
        }

        .progress-tab.is-active {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }

        .certs-empty {
            text-align: center;
            padding: 56px 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }

        .certs-empty-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: var(--amber-soft);
            color: #8A6100;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 8px;
        }

        .certs-empty-title {
            font-weight: 700;
            font-size: 1.05rem;
        }

        .certs-empty-sub {
            color: var(--muted);
            font-size: .86rem;
            max-width: 360px;
        }

        .type-row {
            display: grid;
            grid-template-columns: 120px 1fr 44px 50px;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
        }

        .type-row-label {
            font-size: .84rem;
            font-weight: 700;
        }

        .type-row-track {
            height: 8px;
            background: var(--bg-soft);
            border-radius: 20px;
            overflow: hidden;
        }

        .type-row-fill {
            height: 100%;
            border-radius: 20px;
            background: linear-gradient(90deg, var(--mint), #33D6A0);
        }

        .type-row-val {
            font-size: .78rem;
            font-weight: 700;
            text-align: right;
        }

        .type-row-count {
            font-size: .72rem;
            color: var(--muted);
            text-align: right;
        }

        .stats-empty {
            font-size: .84rem;
            color: var(--muted);
            padding: 8px 0;
        }

        .attempt-row {
            display: grid;
            grid-template-columns: 90px 1fr 90px 80px;
            align-items: center;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid var(--line);
        }

        .attempt-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .attempt-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .68rem;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 20px;
        }

        .attempt-title {
            font-size: .84rem;
            font-weight: 600;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .attempt-score {
            font-size: .8rem;
            font-weight: 700;
            text-align: right;
        }

        .pending-badge {
            color: var(--amber);
            font-weight: 700;
            font-size: .74rem;
        }

        .attempt-date {
            font-size: .72rem;
            color: var(--muted);
            text-align: right;
        }

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
            font-weight: 700;
            font-size: .78rem;
            font-family: 'Sora', sans-serif;
        }

        .lb-name {
            font-weight: 600;
            font-size: .85rem;
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .lb-pts {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: .82rem;
            color: var(--muted);
        }

        @media (max-width:767px) {
            .type-row {
                grid-template-columns: 90px 1fr 36px;
            }

            .type-row-count {
                display: none;
            }

            .attempt-row {
                grid-template-columns: 70px 1fr 70px;
            }

            .attempt-date {
                display: none;
            }
        }
    </style>
@endsection

@section('scripts')
    <script>
        document.querySelectorAll('[data-tab-btn]').forEach(btn => {
            btn.addEventListener('click', () => {
                const tab = btn.dataset.tabBtn;

                document.querySelectorAll('[data-tab-btn]').forEach(b => b.classList.toggle('is-active', b === btn));
                document.querySelectorAll('[data-tab-panel]').forEach(panel => {
                    panel.hidden = panel.dataset.tabPanel !== tab;
                });
            });
        });
    </script>
@endsection
