@extends('layouts.university')
@section('body')
    @php
        $gc = fn($a) => $a === null ? 'none' : 'g-' . max(2, min(5, (int) round($a)));
        $cards = [
            ['Guruhlar', $totals['guruhs'], $thisMonth['guruhs'], 'bi-diagram-3', '--primary', '--primary-soft'],
            ['Fanlar', $totals['fans'], $thisMonth['fans'], 'bi-book', '--mint', '--mint-soft'],
            ['Darslar', $totals['darslar'], $thisMonth['darslar'], 'bi-journal-text', '--amber', '--amber-soft'],
            ['Talabalar', $totals['students'], $thisMonth['students'], 'bi-people', '--coral', '--coral-soft'],
            [
                'Qo\'yilgan baholar',
                $totals['bahos'],
                $thisMonth['bahos'],
                'bi-clipboard2-check',
                '--primary',
                '--primary-soft',
            ],
        ];
    @endphp

    <style>
        .gb {
            display: inline-grid;
            place-items: center;
            min-width: 30px;
            height: 30px;
            padding: 0 8px;
            border-radius: 8px;
            color: #fff;
            font-weight: 700;
            font-size: .85rem;
        }

        .gb.g-5 {
            background: #16a34a;
        }

        .gb.g-4 {
            background: #2563eb;
        }

        .gb.g-3 {
            background: #d97706;
        }

        .gb.g-2 {
            background: #dc2626;
        }

        .gb.none {
            background: var(--line);
            color: var(--muted);
        }

        .prog {
            height: 8px;
            border-radius: 999px;
            background: var(--line);
            overflow: hidden;
            min-width: 90px;
        }

        .prog>span {
            display: block;
            height: 100%;
            background: var(--primary);
            border-radius: 999px;
        }

        .prog.done>span {
            background: #16a34a;
        }

        .empty-mini {
            text-align: center;
            padding: 22px 10px;
            color: var(--muted);
            font-size: .9rem;
        }

        .rank-n {
            color: var(--muted);
            width: 24px;
        }
    </style>

    <div class="page fade-up">
        <div class="page-head">
            <div>
                <h1>Statistika</h1>
                <p class="page-sub">Sizning guruhlaringiz, darslaringiz va talabalaringiz ko'rsatkichlari.</p>
            </div>
        </div>

        {{-- ===== KARTALAR ===== --}}
        <div class="row g-3 mb-3">
            @foreach ($cards as [$label, $value, $month, $icon, $c, $soft])
                <div class="col-6 col-lg-4 col-xl-2">
                    <div class="card mb-0 stat-card">
                        <div class="stat-top">
                            <span class="cell-muted">{{ $label }}</span>
                            <div class="stat-icon"
                                style="background:var({{ $soft }});color:var({{ $c }});">
                                <i class="bi {{ $icon }}"></i>
                            </div>
                        </div>
                        <div class="stat-value">{{ number_format($value) }}</div>
                        <div class="stat-delta">+{{ $month }} shu oy</div>
                    </div>
                </div>
            @endforeach

            <div class="col-6 col-lg-4 col-xl-2">
                <div class="card mb-0 stat-card">
                    <div class="stat-top">
                        <span class="cell-muted">O'rtacha baho</span>
                        <div class="stat-icon" style="background:var(--amber-soft);color:var(--amber);"><i
                                class="bi bi-graph-up"></i></div>
                    </div>
                    <div class="stat-value">{{ $totals['avg'] ?? '–' }}</div>
                    <div class="stat-delta">{{ $ungradedStudents }} ta talaba baholanmagan</div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-7">
                {{-- ===== OXIRGI 7 KUN ===== --}}
                <div class="card">
                    <div class="card-head">
                        <div>
                            <div class="card-title">Qo'yilgan baholar</div>
                            <div class="card-sub-text">So'nggi 7 kun</div>
                        </div>
                    </div>
                    <canvas id="weekChart" height="110"></canvas>
                </div>

                {{-- ===== BUGUNGI DARSLAR ===== --}}
                <div class="card">
                    <div class="card-head">
                        <div>
                            <div class="card-title">Bugungi darslar</div>
                            <div class="card-sub-text">Baholash bugun 24:00 gacha ochiq</div>
                        </div>
                    </div>
                    @if ($today->isEmpty())
                        <div class="empty-mini">Bugun dars qo'shilmagan.</div>
                    @else
                        <div class="table-wrap">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Dars</th>
                                        <th>Guruh</th>
                                        <th>Baholangan</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($today as $d)
                                        @php $pct = $d['total'] ? round($d['graded'] / $d['total'] * 100) : 0; @endphp
                                        <tr>
                                            <td class="cell-strong">{{ $d['title'] }}<div class="cell-muted small">
                                                    {{ $d['fan'] }}</div>
                                            </td>
                                            <td class="cell-muted">{{ $d['guruh'] }}</td>
                                            <td style="min-width:150px">
                                                <div class="prog {{ $pct === 100 ? 'done' : '' }}"><span
                                                        style="width:{{ $pct }}%"></span></div>
                                                <small class="cell-muted">{{ $d['graded'] }}/{{ $d['total'] }}</small>
                                            </td>
                                            <td><a href="{{ route('fans.show', ['fan' => $d['fan_id'], 'dars' => $d['id']]) }}"
                                                    class="btn btn-outline-primary btn-sm">Baholash</a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- ===== GURUHLAR ===== --}}
                <div class="card">
                    <div class="card-head">
                        <div class="card-title">Guruhlar bo'yicha</div>
                    </div>
                    @if ($guruhs->isEmpty())
                        <div class="empty-mini">Hali guruh yo'q.</div>
                    @else
                        <div class="table-wrap">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Guruh</th>
                                        <th>Kurs</th>
                                        <th>Talabalar</th>
                                        <th>Baholar</th>
                                        <th>O'rtacha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($guruhs as $g)
                                        <tr>
                                            <td class="cell-strong">{{ $g['title'] }}</td>
                                            <td class="cell-muted">{{ $g['course'] }}</td>
                                            <td class="cell-muted">{{ $g['students'] }}</td>
                                            <td class="cell-muted">{{ $g['count'] }}</td>
                                            <td><span class="gb {{ $gc($g['avg']) }}">{{ $g['avg'] ?? '–' }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- ===== FANLAR ===== --}}
                <div class="card">
                    <div class="card-head">
                        <div class="card-title">Fanlar bo'yicha</div>
                    </div>
                    @if ($fans->isEmpty())
                        <div class="empty-mini">Hali fan yo'q.</div>
                    @else
                        <div class="table-wrap">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Fan</th>
                                        <th>Guruh</th>
                                        <th>Darslar</th>
                                        <th>Baholar</th>
                                        <th>O'rtacha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($fans as $f)
                                        <tr>
                                            <td class="cell-strong">{{ $f['title'] }}</td>
                                            <td class="cell-muted">{{ $f['guruh'] }}</td>
                                            <td class="cell-muted">{{ $f['darslar'] }}</td>
                                            <td class="cell-muted">{{ $f['count'] }}</td>
                                            <td><span class="gb {{ $gc($f['avg']) }}">{{ $f['avg'] ?? '–' }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-5">
                {{-- ===== TAQSIMOT ===== --}}
                <div class="card">
                    <div class="card-head">
                        <div class="card-title">Baholar taqsimoti</div>
                    </div>
                    @if (array_sum($distribution) === 0)
                        <div class="empty-mini">Hali baho qo'yilmagan.</div>
                    @else
                        <canvas id="distChart" height="200"></canvas>
                    @endif
                </div>

                {{-- ===== ENG YAXSHI ===== --}}
                <div class="card">
                    <div class="card-head">
                        <div class="card-title"><i class="bi bi-trophy"></i> Eng yaxshi talabalar</div>
                    </div>
                    @if ($topStudents->isEmpty())
                        <div class="empty-mini">Ma'lumot yo'q.</div>
                    @else
                        <div class="table-wrap">
                            <table class="data-table" style="min-width:0;">
                                <tbody>
                                    @foreach ($topStudents as $s)
                                        <tr>
                                            <td class="rank-n">{{ $loop->iteration }}</td>
                                            <td class="cell-strong">{{ $s['name'] }}<div class="cell-muted small">
                                                    {{ $s['guruh'] }}</div>
                                            </td>
                                            <td class="cell-muted small">{{ $s['count'] }} ta</td>
                                            <td><span class="gb {{ $gc($s['avg']) }}">{{ $s['avg'] }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- ===== E'TIBOR KERAK ===== --}}
                <div class="card">
                    <div class="card-head">
                        <div class="card-title"><i class="bi bi-exclamation-triangle"></i> E'tibor kerak</div>
                    </div>
                    @if ($weakStudents->isEmpty())
                        <div class="empty-mini">Ma'lumot yo'q.</div>
                    @else
                        <div class="table-wrap">
                            <table class="data-table" style="min-width:0;">
                                <tbody>
                                    @foreach ($weakStudents as $s)
                                        <tr>
                                            <td class="rank-n">{{ $loop->iteration }}</td>
                                            <td class="cell-strong">{{ $s['name'] }}<div class="cell-muted small">
                                                    {{ $s['guruh'] }}</div>
                                            </td>
                                            <td class="cell-muted small">{{ $s['count'] }} ta</td>
                                            <td><span class="gb {{ $gc($s['avg']) }}">{{ $s['avg'] }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    @if ($ungradedStudents)
                        <div class="cell-muted small mt-2">Yana {{ $ungradedStudents }} ta talabaga hali baho qo'yilmagan.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const css = getComputedStyle(document.documentElement);
        const primary = css.getPropertyValue('--primary').trim() || '#2563eb';
        const muted = css.getPropertyValue('--muted').trim() || '#888';
        const line = css.getPropertyValue('--line').trim() || '#ddd';

        new Chart(document.getElementById('weekChart'), {
            type: 'bar',
            data: {
                labels: @json($weekLabels),
                datasets: [{
                    label: 'Baholar',
                    data: @json($weekValues),
                    backgroundColor: primary,
                    borderRadius: 8
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: muted
                        },
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: muted,
                            precision: 0
                        },
                        grid: {
                            color: line
                        }
                    }
                }
            }
        });

        const dist = document.getElementById('distChart');
        if (dist) {
            new Chart(dist, {
                type: 'doughnut',
                data: {
                    labels: ['2', '3', '4', '5'],
                    datasets: [{
                        data: @json($distribution),
                        backgroundColor: ['#dc2626', '#d97706', '#2563eb', '#16a34a'],
                        borderWidth: 0
                    }]
                },
                options: {
                    cutout: '62%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: muted
                            }
                        }
                    }
                }
            });
        }
    </script>
@endpush
