@extends('layouts.admin')

@section('content')
    <div class="page">

        <div class="page-head fade-up">
            <h1>Statistika</h1>
            <p class="page-sub">Butun DarsQil platformasi bo'yicha umumiy ko'rsatkichlar. Har bir sotilgan materialdan
                {{ number_format(\App\Services\AdminStatisticsService::PLATFORM_COMMISSION_RATE * 100, 0) }}% platforma
                komissiyasi hisoblanadi.</p>
        </div>

        <div class="stats-grid fade-up" style="animation-delay:.05s;">
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--primary-soft);color:var(--primary);"><i class="bi bi-person-workspace"></i></div>
                <div class="stat-num">{{ number_format($overview['teachers_count'], 0, '.', ' ') }}</div>
                <div class="stat-lbl">O'qituvchilar</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--mint-soft);color:var(--mint);"><i class="bi bi-mortarboard"></i></div>
                <div class="stat-num">{{ number_format($overview['students_count'], 0, '.', ' ') }}</div>
                <div class="stat-lbl">O'quvchilar</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--amber-soft);color:#8A6100;"><i class="bi bi-cash-stack"></i></div>
                <div class="stat-num">{{ number_format($overview['platform_profit'], 0, '.', ' ') }}</div>
                <div class="stat-lbl">Platforma foydasi (so'm)</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--coral-soft);color:var(--coral);"><i class="bi bi-wallet2"></i></div>
                <div class="stat-num">{{ number_format($overview['teacher_payouts'], 0, '.', ' ') }}</div>
                <div class="stat-lbl">O'qituvchilarga to'langan (so'm)</div>
            </div>
        </div>

        <div class="stats-grid stats-grid-secondary fade-up" style="animation-delay:.08s;">
            <div class="stat-card stat-card-sm">
                <div class="stat-num-sm">{{ number_format($overview['lessons_count'], 0, '.', ' ') }}</div>
                <div class="stat-lbl">Video darslar</div>
            </div>
            <div class="stat-card stat-card-sm">
                <div class="stat-num-sm">{{ number_format($overview['tests_count'], 0, '.', ' ') }}</div>
                <div class="stat-lbl">Testlar</div>
            </div>
            <div class="stat-card stat-card-sm">
                <div class="stat-num-sm">{{ number_format($overview['books_count'], 0, '.', ' ') }}</div>
                <div class="stat-lbl">Kitoblar</div>
            </div>
            <div class="stat-card stat-card-sm">
                <div class="stat-num-sm">{{ number_format($overview['purchases_count'], 0, '.', ' ') }}</div>
                <div class="stat-lbl">Amalga oshirilgan xaridlar</div>
            </div>
        </div>

        <div class="card fade-up viz-root" style="animation-delay:.09s;">
            <div class="card-head">
                <div>
                    <div class="card-title">Oylik statistika</div>
                    <div class="card-sub-text">So'nggi {{ $monthlyRevenue->count() }} oy — tushum, platforma foydasi va o'qituvchilarga to'lovlar</div>
                </div>
                <div class="viz-legend">
                    <span class="viz-legend-item"><i style="background:var(--series-1);"></i> Platforma foydasi</span>
                    <span class="viz-legend-item"><i style="background:var(--series-2);"></i> O'qituvchilarga to'langan</span>
                </div>
            </div>

            @php
                $maxGross = max(1, (float) $monthlyRevenue->max('gross_revenue'));
            @endphp

            @if ($monthlyRevenue->sum('gross_revenue') == 0)
                <div class="empty-hint"><i class="bi bi-info-circle"></i> Hozircha bu davrda xarid amalga oshirilmagan.</div>
            @else
                <div class="viz-chart">
                    <div class="viz-gridlines">
                        @foreach ([1, 0.75, 0.5, 0.25, 0] as $step)
                            <div class="viz-gridline">
                                <span>{{ number_format($maxGross * $step, 0, '.', ' ') }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="viz-bars">
                        @foreach ($monthlyRevenue as $row)
                            @php
                                $profitPct = $maxGross > 0 ? ($row['platform_profit'] / $maxGross) * 100 : 0;
                                $payoutPct = $maxGross > 0 ? ($row['teacher_payouts'] / $maxGross) * 100 : 0;
                            @endphp
                            <div class="viz-col" tabindex="0">
                                <div class="viz-col-total">{{ number_format($row['gross_revenue'], 0, '.', ' ') }}</div>
                                <div class="viz-stack">
                                    <div class="viz-seg viz-seg-1" style="height:{{ $profitPct }}%;"></div>
                                    <div class="viz-seg viz-seg-2" style="height:{{ $payoutPct }}%;"></div>
                                </div>
                                <div class="viz-col-label">{{ $row['label'] }}</div>
                                <div class="viz-tooltip">
                                    <strong>{{ $row['label'] }}</strong>
                                    <div>Jami tushum: {{ number_format($row['gross_revenue'], 0, '.', ' ') }} so'm</div>
                                    <div>Platforma foydasi: {{ number_format($row['platform_profit'], 0, '.', ' ') }} so'm</div>
                                    <div>O'qituvchilarga: {{ number_format($row['teacher_payouts'], 0, '.', ' ') }} so'm</div>
                                    <div>Xaridlar: {{ $row['purchases_count'] }} ta</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="table-wrap viz-table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Oy</th>
                                <th>Jami tushum</th>
                                <th>Platforma foydasi</th>
                                <th>O'qituvchilarga to'langan</th>
                                <th>Xaridlar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($monthlyRevenue as $row)
                                <tr>
                                    <td class="cell-strong" data-label="Oy">{{ $row['label'] }}</td>
                                    <td data-label="Jami tushum">{{ number_format($row['gross_revenue'], 0, '.', ' ') }} so'm</td>
                                    <td data-label="Platforma foydasi">{{ number_format($row['platform_profit'], 0, '.', ' ') }} so'm</td>
                                    <td data-label="O'qituvchilarga">{{ number_format($row['teacher_payouts'], 0, '.', ' ') }} so'm</td>
                                    <td class="cell-muted" data-label="Xaridlar">{{ $row['purchases_count'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="card fade-up" style="animation-delay:.1s;">
            <div class="card-head">
                <div class="card-title">Eng faol o'qituvchilar</div>
            </div>
            <div class="top-grid">
                <div class="top-card">
                    <div class="top-card-icon"><i class="bi bi-file-earmark-pdf"></i></div>
                    <div class="top-card-label">Eng ko'p kitob joylagan</div>
                    @if ($topBooks->isEmpty())
                        <div class="top-card-empty">Hali kitob joylanmagan</div>
                    @else
                        <div class="top-card-name">{{ $topBooks->first()->name }}</div>
                        <div class="top-card-count">{{ $topBooks->first()->books_count }} ta kitob</div>
                    @endif
                </div>
                <div class="top-card">
                    <div class="top-card-icon"><i class="bi bi-camera-reels"></i></div>
                    <div class="top-card-label">Eng ko'p video dars joylagan</div>
                    @if ($topLessons->isEmpty())
                        <div class="top-card-empty">Hali video dars joylanmagan</div>
                    @else
                        <div class="top-card-name">{{ $topLessons->first()->name }}</div>
                        <div class="top-card-count">{{ $topLessons->first()->lessons_count }} ta dars</div>
                    @endif
                </div>
                <div class="top-card">
                    <div class="top-card-icon"><i class="bi bi-patch-question"></i></div>
                    <div class="top-card-label">Eng ko'p test joylagan</div>
                    @if ($topTests->isEmpty())
                        <div class="top-card-empty">Hali test joylanmagan</div>
                    @else
                        <div class="top-card-name">{{ $topTests->first()['teacher']->name }}</div>
                        <div class="top-card-count">{{ $topTests->first()['tests_count'] }} ta test</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card fade-up" style="animation-delay:.14s;">
            <div class="card-head">
                <div class="card-title">O'qituvchilar reytingi</div>
                <div class="card-sub-text">Daromad bo'yicha kamayish tartibida</div>
            </div>

            @if ($teacherRevenue->isEmpty())
                <div class="empty-hint">
                    <i class="bi bi-info-circle"></i>
                    Hozircha hech qanday xarid amalga oshirilmagan.
                </div>
            @else
                <div class="table-wrap">
                    <table class="revenue-table">
                        <thead>
                            <tr>
                                <th>O'qituvchi</th>
                                <th>Darslar</th>
                                <th>Testlar</th>
                                <th>Kitoblar</th>
                                <th>Sotib olingan</th>
                                <th>Jami tushum</th>
                                <th>O'qituvchi ulushi (80%)</th>
                                <th>Platforma ulushi (20%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($teacherRevenue as $row)
                                <tr>
                                    <td class="teacher-cell" data-label="O'qituvchi">{{ $row['teacher']->name }}</td>
                                    <td data-label="Darslar">{{ $row['lessons_count'] }}</td>
                                    <td data-label="Testlar">{{ $row['tests_count'] }}</td>
                                    <td data-label="Kitoblar">{{ $row['books_count'] }}</td>
                                    <td data-label="Sotib olingan">{{ $row['purchases_count'] }}</td>
                                    <td data-label="Jami tushum">{{ number_format($row['gross'], 0, '.', ' ') }} so'm</td>
                                    <td data-label="O'qituvchi ulushi" class="earning-cell">{{ number_format($row['earning'], 0, '.', ' ') }} so'm</td>
                                    <td data-label="Platforma ulushi">{{ number_format($row['platform_cut'], 0, '.', ' ') }} so'm</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <style>
        .page-head { margin-bottom: 22px; }
        .page-head h1 { font-size: 1.5rem; margin: 4px 0 6px; }
        .page-sub { color: var(--muted); font-size: .88rem; margin: 0; max-width: 680px; }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 18px;
        }

        .stat-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 20px;
            box-shadow: var(--shadow-sm);
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .stat-card:hover { transform: translateY(-4px); box-shadow: var(--shadow); }

        .stat-icon {
            width: 42px; height: 42px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; margin-bottom: 14px;
        }

        .stat-num { font-family: 'Sora', sans-serif; font-weight: 800; font-size: 1.6rem; font-variant-numeric: tabular-nums; }
        .stat-lbl { color: var(--muted); font-size: .82rem; margin-top: 2px; }

        .stat-card-sm { padding: 16px 18px; }
        .stat-num-sm { font-family: 'Sora', sans-serif; font-weight: 700; font-size: 1.2rem; font-variant-numeric: tabular-nums; color: var(--text); }
        .stats-grid-secondary { margin-bottom: 22px; }

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 22px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 18px;
        }

        .card-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .card-title { font-weight: 700; font-size: 1rem; }
        .card-sub-text { font-size: .82rem; color: var(--muted); margin-top: 2px; }

        .top-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .top-card {
            background: var(--bg-soft);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 18px;
        }

        .top-card-icon {
            width: 38px; height: 38px; border-radius: 10px;
            background: var(--primary-soft); color: var(--primary);
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; margin-bottom: 12px;
        }

        .top-card-label { font-size: .78rem; color: var(--muted); font-weight: 600; margin-bottom: 6px; }
        .top-card-name { font-weight: 700; font-size: .98rem; }
        .top-card-count { font-size: .82rem; color: var(--muted); margin-top: 2px; }
        .top-card-empty { font-size: .84rem; color: var(--muted); font-style: italic; }

        .empty-hint {
            display: flex; align-items: center; gap: 8px;
            padding: 11px 13px; border-radius: 10px;
            background: var(--amber-soft); color: #8A6100;
            font-size: .84rem; border: 1px dashed #E0B24C;
        }

        .table-wrap { overflow-x: auto; }

        .revenue-table {
            width: 100%;
            min-width: 780px;
            border-collapse: collapse;
            font-size: .86rem;
        }

        .revenue-table th, .revenue-table td {
            padding: 12px 14px;
            text-align: left;
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }

        .revenue-table th {
            color: var(--muted);
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
            border-bottom: 1.5px solid var(--line);
            font-variant-numeric: normal;
        }

        .revenue-table tbody tr { border-bottom: 1px solid var(--line); }
        .revenue-table tbody tr:last-child { border-bottom: none; }
        .teacher-cell { font-weight: 700; }
        .earning-cell { color: var(--mint); font-weight: 700; }

        @media (max-width:1100px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .top-grid { grid-template-columns: 1fr; }
        }

        @media (max-width:767px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            .stat-card { padding: 15px; }
            .stat-num { font-size: 1.3rem; }
            .card { padding: 16px; }
        }

        /* Oylik statistika — stacked column chart. Palette: validated categorical
           slots 1 (blue) & 2 (orange) from the dataviz reference palette. */
        .viz-root {
            --series-1: #2a78d6;
            --series-2: #eb6834;
        }

        [data-theme="dark"] .viz-root {
            --series-1: #3987e5;
            --series-2: #d95926;
        }

        .viz-legend {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .viz-legend-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: .78rem;
            font-weight: 600;
            color: var(--muted);
            white-space: nowrap;
        }

        .viz-legend-item i {
            width: 10px;
            height: 10px;
            border-radius: 3px;
            display: inline-block;
        }

        .viz-chart {
            display: flex;
            gap: 12px;
            margin: 8px 0 20px;
        }

        .viz-gridlines {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 220px;
            padding-bottom: 34px;
            flex-shrink: 0;
        }

        .viz-gridline {
            text-align: right;
        }

        .viz-gridline span {
            font-size: .7rem;
            color: var(--muted);
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }

        .viz-bars {
            flex: 1;
            display: flex;
            align-items: flex-end;
            gap: 4px;
            height: 220px;
            border-bottom: 1px solid var(--line);
            background-image: repeating-linear-gradient(to top, var(--line) 0, var(--line) 1px, transparent 1px, transparent 25%);
            position: relative;
        }

        .viz-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100%;
            justify-content: flex-end;
            position: relative;
            min-width: 0;
            outline: none;
        }

        .viz-col-total {
            font-size: .72rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 4px;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }

        .viz-stack {
            width: 100%;
            max-width: 24px;
            display: flex;
            flex-direction: column;
            flex: 1;
            gap: 2px;
        }

        .viz-seg {
            width: 100%;
        }

        .viz-seg-1 {
            background: var(--series-1);
            border-radius: 4px 4px 0 0;
        }

        .viz-seg-2 {
            background: var(--series-2);
            border-radius: 0;
        }

        .viz-col-label {
            font-size: .68rem;
            color: var(--muted);
            margin-top: 8px;
            white-space: nowrap;
            font-weight: 600;
        }

        .viz-tooltip {
            position: absolute;
            bottom: calc(100% + 8px);
            left: 50%;
            transform: translateX(-50%);
            background: var(--text);
            color: var(--card);
            padding: 10px 12px;
            border-radius: 10px;
            font-size: .74rem;
            line-height: 1.5;
            white-space: nowrap;
            box-shadow: var(--shadow);
            opacity: 0;
            visibility: hidden;
            transition: opacity .15s;
            z-index: 5;
            pointer-events: none;
        }

        .viz-tooltip strong {
            display: block;
            margin-bottom: 4px;
        }

        .viz-col:hover .viz-tooltip,
        .viz-col:focus .viz-tooltip {
            opacity: 1;
            visibility: visible;
        }

        .viz-table-wrap { margin-top: 4px; }

        @media (max-width:767px) {
            .viz-gridlines { display: none; }
            .viz-col-total { font-size: .64rem; }
            .viz-col-label { font-size: .6rem; }
        }
    </style>
@endsection
