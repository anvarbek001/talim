@extends('layouts.university')
@section('body')
    @php
        $gc = fn($a) => $a === null ? 'none' : 'g-' . max(2, min(5, (int) round($a)));
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

        .t-head {
            display: flex;
            align-items: center;
            gap: 14px;
            width: 100%;
            padding: 16px 18px;
            background: none;
            border: 0;
            text-align: left;
            color: var(--text);
            cursor: pointer;
        }

        .t-ava {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--primary-soft);
            color: var(--primary);
            display: grid;
            place-items: center;
            font-weight: 700;
            flex-shrink: 0;
        }

        .t-meta {
            color: var(--muted);
            font-size: .82rem;
        }

        .t-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .t-body {
            padding: 0 18px 18px;
        }

        .g-box {
            border: 1px solid var(--line);
            border-radius: 12px;
            background: var(--bg-soft);
            margin-top: 10px;
        }

        .g-head {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            cursor: pointer;
        }

        .g-head b {
            color: var(--text);
        }

        .g-body {
            padding: 0 14px 14px;
        }

        .s-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 10px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: var(--card);
            cursor: pointer;
        }

        .s-row+.s-row {
            margin-top: 6px;
        }

        .s-row:hover {
            border-color: var(--primary);
        }

        .s-ava {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }

        .s-ava.ph {
            background: var(--primary-soft);
            color: var(--primary);
            display: grid;
            place-items: center;
            font-weight: 700;
        }

        .s-fans {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            margin-left: auto;
        }

        .chip {
            font-size: .72rem;
            padding: 2px 8px;
            border-radius: 999px;
            border: 1px solid var(--line);
            color: var(--muted);
            background: var(--bg-soft);
            white-space: nowrap;
        }

        .chip b {
            color: var(--text);
        }

        .empty-mini {
            text-align: center;
            padding: 20px 10px;
            color: var(--muted);
            font-size: .9rem;
        }

        .sg-top {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 16px;
        }

        .sg-top h5 {
            margin: 0;
            color: var(--text);
        }

        .sg-total {
            margin-left: auto;
            text-align: center;
        }

        .sg-total small {
            display: block;
            color: var(--muted);
            font-size: .7rem;
            text-transform: uppercase;
        }

        .sg-fan {
            border: 1px solid var(--line);
            border-radius: 12px;
            background: var(--bg-soft);
            padding: 12px 14px;
        }

        .sg-fan+.sg-fan {
            margin-top: 10px;
        }

        .sg-fan-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 8px;
        }

        .sg-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .sg-item {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 3px 8px 3px 3px;
            border: 1px solid var(--line);
            border-radius: 999px;
            background: var(--card);
            font-size: .78rem;
            color: var(--muted);
        }

        .sg-item .gb {
            min-width: 24px;
            height: 24px;
            border-radius: 999px;
            font-size: .78rem;
        }
    </style>

    <div class="page fade-up">
        <div class="page-head">
            <div>
                <h1>Universitet</h1>
                <p class="page-sub">O'qituvchilar, ularning guruhlari va talabalar baholari.</p>
            </div>
        </div>

        {{-- ===== UMUMIY ===== --}}
        <div class="row g-3 mb-3">
            @foreach ([['O\'qituvchilar', $totals['teachers'], 'bi-person-workspace'], ['Guruhlar', $totals['guruhs'], 'bi-diagram-3'], ['Talabalar', $totals['students'], 'bi-people'], ['Baholar', $totals['bahos'], 'bi-clipboard2-check']] as [$label, $val, $icon])
                <div class="col-6 col-lg-3">
                    <div class="card mb-0 stat-card">
                        <div class="stat-top">
                            <span class="cell-muted">{{ $label }}</span>
                            <div class="stat-icon" style="background:var(--primary-soft);color:var(--primary);"><i
                                    class="bi {{ $icon }}"></i></div>
                        </div>
                        <div class="stat-value">{{ number_format($val) }}</div>
                    </div>
                </div>
            @endforeach
        </div>
        <p class="cell-muted">Universitet bo'yicha o'rtacha baho: <span
                class="gb {{ $gc($totals['avg']) }}">{{ $totals['avg'] ?? '–' }}</span></p>

        {{-- ===== O'QITUVCHILAR ===== --}}
        @forelse ($tree as $t)
            <div class="card">
                <button class="t-head" type="button" data-bs-toggle="collapse" data-bs-target="#t{{ $t['id'] }}">
                    <div class="t-ava">{{ mb_strtoupper(mb_substr($t['name'], 0, 1)) }}</div>
                    <div>
                        <div class="fw-bold">{{ $t['name'] }}</div>
                        <div class="t-meta">{{ $t['groups']->count() }} guruh · {{ $t['students'] }} talaba ·
                            {{ $t['count'] }} baho</div>
                    </div>
                    <div class="t-right">
                        <span class="t-meta">O'rtacha</span>
                        <span class="gb {{ $gc($t['avg']) }}">{{ $t['avg'] ?? '–' }}</span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                </button>

                <div id="t{{ $t['id'] }}" class="collapse">
                    <div class="t-body">
                        @forelse ($t['groups'] as $g)
                            @php $gid = $t['id'] . '_' . $loop->index; @endphp
                            <div class="g-box">
                                <div class="g-head" data-bs-toggle="collapse" data-bs-target="#g{{ $gid }}">
                                    <i class="bi bi-diagram-3 text-muted"></i>
                                    <div>
                                        <b>{{ $g['title'] }}</b>
                                        <div class="t-meta">{{ $g['course'] }} · {{ $g['students']->count() }} talaba ·
                                            {{ $g['count'] }} baho</div>
                                    </div>
                                    <div class="t-right">
                                        <span class="gb {{ $gc($g['avg']) }}"
                                            title="Guruh o'rtachasi">{{ $g['avg'] ?? '–' }}</span>
                                        <i class="bi bi-chevron-down"></i>
                                    </div>
                                </div>

                                <div id="g{{ $gid }}" class="collapse">
                                    <div class="g-body">
                                        @forelse ($g['students'] as $s)
                                            <div class="s-row student-open" data-student-id="{{ $s['id'] }}"
                                                data-student-name="{{ $s['name'] }}"
                                                data-student-photo="{{ $s['photo'] ? asset('storage/' . $s['photo']) : 'https://ui-avatars.com/api/?name=' . urlencode($s['name']) }}">
                                                @if ($s['photo'])
                                                    <img src="{{ asset('storage/' . $s['photo']) }}" class="s-ava"
                                                        alt="">
                                                @else
                                                    <div class="s-ava ph">{{ mb_strtoupper(mb_substr($s['name'], 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="fw-bold">{{ $s['name'] }}</div>
                                                    <div class="t-meta">
                                                        {{ $s['count'] ? $s['count'] . ' ta baho' : 'Baholanmagan' }}</div>
                                                </div>
                                                <div class="s-fans">
                                                    @foreach ($s['fans'] as $f)
                                                        <span class="chip">{{ $f['fan'] }}:
                                                            <b>{{ $f['avg'] }}</b></span>
                                                    @endforeach
                                                </div>
                                                <span class="gb {{ $gc($s['avg']) }}"
                                                    title="O'rtacha baho">{{ $s['avg'] ?? '–' }}</span>
                                            </div>
                                        @empty
                                            <div class="empty-mini">Bu guruhda talaba yo'q.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-mini">Bu o'qituvchining guruhi yo'q.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        @empty
            <div class="card">
                <div class="empty-mini">Universitetda hali o'qituvchi yo'q.</div>
            </div>
        @endforelse
    </div>

    {{-- ===== TALABA BAHOLARI MODAL ===== --}}
    <div class="modal fade" id="studentGradesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Talaba baholari</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="sgBody"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const gradeData = @json($studentInfo);

        const esc = s => {
            const d = document.createElement('div');
            d.textContent = s ?? '';
            return d.innerHTML;
        };
        const gClass = v => 'g-' + Math.max(2, Math.min(5, Math.round(v)));

        document.addEventListener('DOMContentLoaded', () => {
            const modalEl = document.getElementById('studentGradesModal');
            document.body.appendChild(modalEl);
            const body = document.getElementById('sgBody');

            document.querySelectorAll('.student-open').forEach(row => {
                row.addEventListener('click', () => {
                    const info = gradeData[row.dataset.studentId];

                    let html = `<div class="sg-top">
                        <img src="${esc(row.dataset.studentPhoto)}" class="s-ava" style="width:60px;height:60px" alt="">
                        <div><h5>${esc(row.dataset.studentName)}</h5>
                        <small class="text-muted">${info ? info.count + ' ta baho' : 'Baho yo\'q'}</small></div>
                        ${info ? `<div class="sg-total"><small>Umumiy o'rtacha</small><span class="gb ${gClass(info.avg)}">${info.avg}</span></div>` : ''}
                    </div>`;

                    if (!info) {
                        html += `<p class="text-muted mb-0">Bu talabaga hali baho qo'yilmagan.</p>`;
                    } else {
                        info.fans.forEach(f => {
                            html += `<div class="sg-fan">
                                <div class="sg-fan-head">
                                    <div><b>${esc(f.fan)}</b> <small class="text-muted">· ${f.count} ta baho</small></div>
                                    <span class="gb ${gClass(f.avg)}" title="Fan bo'yicha o'rtacha">${f.avg}</span>
                                </div>
                                <div class="sg-list">
                                    ${f.grades.map(g => `<span class="sg-item" title="${esc(g.dars)}">
                                            <span class="gb ${gClass(g.baho)}">${g.baho}</span>
                                            ${esc(g.dars)}${g.date ? ' · ' + esc(g.date) : ''}</span>`).join('')}
                                </div>
                            </div>`;
                        });
                    }

                    body.innerHTML = html;
                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                });
            });
        });
    </script>
@endpush
