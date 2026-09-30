@extends('layouts.university')
@section('body')
    @php
        $scale = [2, 3, 4, 5];
        $studentCount = $students->count();
        $activeGraded = $activeDars
            ? $students->filter(fn($s) => $matrix->get($s->id)?->get($activeDars->id)?->baho)->count()
            : 0;
    @endphp

    @php
        $open = $activeDars ? $activeDars->isGradable() : false;
        if ($open) {
            $left = (int) floor(now()->diffInMinutes($activeDars->gradingDeadline(), false));
            $leftH = intdiv($left, 60);
            $leftM = $left % 60;
        }
    @endphp

    @php
        // talaba_id => o'rtacha baho (baho bo'lmasa null)
        $averages = $students->mapWithKeys(function ($s) use ($matrix) {
            $vals = ($matrix->get($s->id) ?? collect())->pluck('baho')->filter();
            return [$s->id => $vals->count() ? round($vals->avg(), 1) : null];
        });

        $groupAvg = $averages->filter()->count() ? round($averages->filter()->avg(), 1) : null;

        // o'rtacha ballga mos rang klassi (2..5)
$avgClass = fn($a) => $a === null ? '' : 'g-' . max(2, min(5, (int) round($a)));
    @endphp

    <style>
        .detail-card,
        .students-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 24px;
        }

        .students-card {
            margin-top: 20px;
        }

        .detail-head {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 18px;
        }

        .detail-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: var(--primary-soft);
            color: var(--primary);
            display: grid;
            place-items: center;
            font-size: 1.6rem;
        }

        .detail-head h1 {
            margin: 0;
            font-size: 1.5rem;
            color: var(--text);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
            margin-top: 18px;
        }

        .info-item {
            background: var(--bg-soft);
            border: 1px dashed var(--line);
            border-radius: 12px;
            padding: 12px 14px;
        }

        .info-item small {
            display: block;
            color: var(--muted);
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .info-item b {
            color: var(--text);
        }

        .detail-desc {
            color: var(--muted);
            margin: 0;
        }

        .sec-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        .sec-head h2 {
            margin: 0;
            font-size: 1.15rem;
            color: var(--text);
        }

        .pill {
            background: var(--primary-soft);
            color: var(--primary);
            padding: 4px 12px;
            border-radius: 999px;
            font-size: .82rem;
            font-weight: 700;
        }

        /* ===== JURNAL JADVALI ===== */
        .journal-wrap {
            overflow-x: auto;
            border: 1px solid var(--line);
            border-radius: 12px;
        }

        .journal {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: .9rem;
        }

        .journal th,
        .journal td {
            padding: 9px 10px;
            border-bottom: 1px solid var(--line);
            text-align: center;
            white-space: nowrap;
            color: var(--text);
        }

        .journal tr:last-child td {
            border-bottom: none;
        }

        .journal thead th {
            background: var(--bg-soft);
            font-size: .78rem;
            color: var(--muted);
            font-weight: 700;
        }

        .journal thead th a {
            color: inherit;
            text-decoration: none;
            display: block;
        }

        .journal thead th a:hover {
            color: var(--primary);
        }

        .journal th.active,
        .journal td.active {
            background: var(--primary-soft);
        }

        .journal thead th.active a {
            color: var(--primary);
        }

        .journal .name {
            position: sticky;
            left: 0;
            z-index: 1;
            text-align: left;
            background: var(--card);
            min-width: 190px;
            max-width: 240px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .journal thead .name {
            background: var(--bg-soft);
        }

        .journal .avg {
            font-weight: 700;
            background: var(--bg-soft);
        }

        .journal .name .n {
            color: var(--muted);
            font-size: .78rem;
            margin-right: 8px;
        }

        .gb {
            display: inline-grid;
            place-items: center;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            color: #fff;
            font-weight: 700;
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

        .dash {
            color: var(--muted);
            opacity: .5;
        }

        /* ===== BAHOLASH ===== */
        .grade-box {
            margin-top: 20px;
            padding-top: 18px;
            border-top: 1px dashed var(--line);
        }

        .grade-box h3 {
            font-size: 1rem;
            margin: 0 0 4px;
            color: var(--text);
        }

        .grade-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            padding: 10px 12px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: var(--bg-soft);
        }

        .grade-row+.grade-row {
            margin-top: 8px;
        }

        .grade-student {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .grade-student .num {
            width: 22px;
            color: var(--muted);
            font-size: .8rem;
            text-align: right;
        }

        .student-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }

        .student-avatar.ph {
            background: var(--primary-soft);
            color: var(--primary);
            display: grid;
            place-items: center;
            font-weight: 700;
        }

        .grade-student b {
            color: var(--text);
            font-size: .93rem;
        }

        .grade-opts {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .grade-opts input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .grade-opts label {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border-radius: 10px;
            border: 1px solid var(--line);
            background: var(--card);
            color: var(--muted);
            font-weight: 700;
            cursor: pointer;
            transition: .12s;
            user-select: none;
        }

        .grade-opts label:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .grade-opts input:focus-visible+label {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        .grade-opts input:checked+label {
            color: #fff;
            border-color: transparent;
        }

        .grade-opts input:checked+label.g-5 {
            background: #16a34a;
        }

        .grade-opts input:checked+label.g-4 {
            background: #2563eb;
        }

        .grade-opts input:checked+label.g-3 {
            background: #d97706;
        }

        .grade-opts input:checked+label.g-2 {
            background: #dc2626;
        }

        .grade-opts label.clear {
            width: 34px;
            font-weight: 400;
            font-size: 1.1rem;
        }

        .grade-opts input:checked+label.clear {
            background: var(--line);
            color: var(--text);
        }

        .save-bar {
            position: sticky;
            bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 16px;
            padding: 12px 16px;
            border-radius: 14px;
            background: var(--card);
            border: 1px solid var(--line);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .12);
        }

        .save-bar span {
            color: var(--muted);
            font-size: .85rem;
        }

        .empty-box {
            text-align: center;
            padding: 34px 16px;
            color: var(--muted);
            border: 1px dashed var(--line);
            border-radius: 12px;
        }

        .empty-box i {
            font-size: 2rem;
            color: var(--primary);
            display: block;
            margin-bottom: 6px;
        }

        /* ===== MODAL ===== */
        .modal-content {
            background: var(--card);
            color: var(--text);
            border: 1px solid var(--line);
            border-radius: 16px;
        }

        .modal-header,
        .modal-footer {
            border-color: var(--line);
        }

        .modal-title {
            color: var(--text);
            font-weight: 700;
        }

        .form-label {
            color: var(--muted);
            font-weight: 600;
            font-size: .85rem;
        }

        .form-control {
            background: var(--bg-soft);
            color: var(--text);
            border-color: var(--line);
        }

        .form-control::placeholder {
            color: var(--muted);
        }

        .form-control:focus {
            background: var(--bg-soft);
            color: var(--text);
            border-color: var(--primary);
            box-shadow: 0 0 0 .2rem var(--primary-soft);
        }

        [data-theme="dark"] .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        [data-theme="dark"] .btn-light {
            background: var(--bg-soft);
            color: var(--text);
            border-color: var(--line);
        }

        .time-box {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 12px;
            margin-bottom: 12px;
            font-size: .88rem;
            font-weight: 600;
        }

        .time-box.open {
            background: #dcfce7;
            color: #166534;
        }

        .time-box.closed {
            background: #fee2e2;
            color: #991b1b;
        }

        .grade-opts input:disabled+label {
            cursor: not-allowed;
            opacity: .55;
        }

        .grade-opts input:disabled:checked+label {
            opacity: .9;
        }

        .journal thead th .bi-lock-fill {
            font-size: .7rem;
            margin-left: 3px;
            opacity: .6;
        }

        .gb.avg-b {
            width: auto;
            min-width: 38px;
            padding: 0 9px;
        }

        .avg-tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 9px;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 700;
            color: #fff;
            margin-left: 8px;
            white-space: nowrap;
        }

        .avg-tag.g-5 {
            background: #16a34a;
        }

        .avg-tag.g-4 {
            background: #2563eb;
        }

        .avg-tag.g-3 {
            background: #d97706;
        }

        .avg-tag.g-2 {
            background: #dc2626;
        }

        .avg-tag.none {
            background: var(--line);
            color: var(--muted);
        }
    </style>

    <div class="page fade-up">
        <div class="page-head">
            <div>
                <h1>Fan haqida</h1>
                <p class="page-sub">Fan, darslar va talabalar baholari.</p>
            </div>
            <a href="{{ route('darses.index') }}" class="btn btn-light rounded-3">
                <i class="bi bi-arrow-left"></i> Orqaga
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        {{-- ===== FAN MA'LUMOTI ===== --}}
        <div class="detail-card">
            <div class="detail-head">
                <div class="detail-icon"><i class="bi bi-book"></i></div>
                <h1>{{ $fan->title }}</h1>
            </div>
            <p class="detail-desc">{{ $fan->desc ?: 'Izoh kiritilmagan' }}</p>
            <div class="info-grid">
                <div class="info-item"><small>Guruh</small><b>{{ $fan->guruh->title ?? '—' }}</b></div>
                <div class="info-item"><small>Kurs</small><b>{{ $fan->guruh->course->title ?? '—' }}</b></div>
                <div class="info-item"><small>Qo'shgan</small><b>{{ $fan->user->name ?? '—' }}</b></div>
                <div class="info-item"><small>Darslar soni</small><b>{{ $darslar->count() }} ta</b></div>
            </div>
        </div>

        {{-- ===== JURNAL ===== --}}
        <div class="students-card">
            <div class="sec-head">
                <h2><i class="bi bi-table"></i> Talabalar jurnali</h2>
                <div class="d-flex gap-2 align-items-center">
                    <span class="pill">{{ $studentCount }} ta talaba</span>
                    @if ($groupAvg)
                        <span class="pill">Guruh o'rtacha bahosi: {{ $groupAvg }}</span>
                    @endif
                    <button type="button" class="btn btn-primary btn-sm rounded-3" data-bs-toggle="modal"
                        data-bs-target="#addDarsModal">
                        <i class="bi bi-plus-lg"></i> Dars qo'shish
                    </button>
                </div>
            </div>

            @if ($students->isEmpty())
                <div class="empty-box">
                    <i class="bi bi-people"></i>
                    Bu guruhda hali talaba yo'q.
                </div>
            @else
                <div class="journal-wrap">
                    <table class="journal">
                        <thead>
                            <tr>
                                <th class="name">Talaba</th>
                                @foreach ($darslar as $d)
                                    <th class="{{ $activeDars && $activeDars->id === $d->id ? 'active' : '' }}">
                                        <a href="{{ route('fans.show', ['fan' => $fan->id, 'dars' => $d->id]) }}"
                                            title="{{ $d->title }}">
                                            {{ $loop->iteration }}-dars @unless ($d->isGradable())
                                                <i class="bi bi-lock-fill"></i>
                                            @endunless
                                            <small class="d-block fw-normal">{{ $d->created_at->format('d.m') }}</small>
                                        </a>
                                    </th>
                                @endforeach
                                <th class="avg">O'rtacha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $student)
                                @php
                                    $row = $matrix->get($student->id);
                                    $avg = $averages[$student->id];
                                @endphp
                                <tr>
                                    <td class="name" title="{{ $student->name }}">
                                        <span class="n">{{ $loop->iteration }}</span>{{ $student->name }}
                                    </td>
                                    @foreach ($darslar as $d)
                                        @php $b = $row?->get($d->id)?->baho; @endphp
                                        <td class="{{ $activeDars && $activeDars->id === $d->id ? 'active' : '' }}">
                                            @if ($b)
                                                <span class="gb g-{{ $b }}">{{ $b }}</span>
                                            @else
                                                <span class="dash">–</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="avg">
                                        @if ($avg)
                                            <span class="gb avg-b {{ $avgClass($avg) }}">{{ $avg }}</span>
                                        @else
                                            <span class="dash">–</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($darslar->isEmpty())
                    <div class="empty-box mt-3">
                        <i class="bi bi-journal-plus"></i>
                        Hali dars yo'q. "Dars qo'shish" tugmasini bosing, keyin baho qo'yishingiz mumkin.
                    </div>
                @endif
            @endif

            {{-- ===== TANLANGAN DARS UCHUN BAHOLASH ===== --}}
            @if ($activeDars && $students->isNotEmpty())
                <div class="grade-box">
                    <div class="sec-head">
                        <div>
                            <h3>{{ $darslar->search(fn($x) => $x->id === $activeDars->id) + 1 }}-dars:
                                {{ $activeDars->title }}</h3>
                            @if ($activeDars->desc)
                                <small class="text-muted">{{ $activeDars->desc }}</small>
                            @endif
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <span class="pill">Baholangan: {{ $activeGraded }}/{{ $studentCount }}</span>
                            <form action="{{ route('darslar.destroy', $activeDars->id) }}" method="POST"
                                onsubmit="return confirm('Dars va uning barcha baholari o\'chiriladi. Davom etasizmi?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" title="Darsni o'chirish"><i
                                        class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>

                    @if ($open)
                        <div class="time-box open">
                            <i class="bi bi-clock"></i>
                            Baholash {{ $activeDars->gradingDeadline()->format('d.m.Y H:i') }} gacha ochiq
                            (qoldi: {{ $leftH }} soat {{ $leftM }} daqiqa)
                        </div>
                    @else
                        <div class="time-box closed">
                            <i class="bi bi-lock-fill"></i>
                            Baholash vaqti tugagan ({{ $activeDars->gradingDeadline()->format('d.m.Y H:i') }}). Baholarni
                            faqat ko'rish mumkin.
                        </div>
                    @endif

                    <form action="{{ route('fans.bahos.store', $fan->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="dars_id" value="{{ $activeDars->id }}">

                        @foreach ($students as $student)
                            @php $current = $matrix->get($student->id)?->get($activeDars->id)?->baho; @endphp
                            <div class="grade-row">
                                <div class="grade-student">
                                    <span class="num">{{ $loop->iteration }}</span>
                                    @if ($student->photo)
                                        <img src="{{ asset('storage/' . $student->photo) }}" class="student-avatar"
                                            alt="">
                                    @else
                                        <div class="student-avatar ph">{{ mb_strtoupper(mb_substr($student->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <b>{{ $student->name }}</b>
                                    @php $sa = $averages[$student->id]; @endphp
                                    <span class="avg-tag {{ $sa ? $avgClass($sa) : 'none' }}"
                                        title="Talabaning o'rtacha bahosi">
                                        <i class="bi bi-graph-up"></i> {{ $sa ?? '–' }}
                                    </span>
                                </div>

                                <div class="grade-opts">
                                    @foreach ($scale as $g)
                                        <input type="radio" name="bahos[{{ $student->id }}]"
                                            id="b{{ $student->id }}_{{ $g }}" value="{{ $g }}"
                                            @checked($current === $g) @disabled(!$open)>
                                        <label for="b{{ $student->id }}_{{ $g }}"
                                            class="g-{{ $g }}">{{ $g }}</label>
                                    @endforeach
                                    <input type="radio" name="bahos[{{ $student->id }}]" id="b{{ $student->id }}_x"
                                        value="" @checked($current === null) @disabled(!$open)>
                                    <label for="b{{ $student->id }}_x" class="clear"
                                        title="Bahoni olib tashlash">×</label>
                                </div>
                            </div>
                        @endforeach

                        @if ($open)
                            <div class="save-bar">
                                <span>Baholarni tanlang va saqlang</span>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check2-circle"></i> Saqlash
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            @endif
        </div>
    </div>

    {{-- ===== DARS QO'SHISH MODAL ===== --}}
    <div class="modal fade" id="addDarsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('fans.darslar.store', $fan->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Dars qo'shish</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Dars mavzusi</label>
                            <input type="text" name="title" class="form-control"
                                placeholder="Masalan: Gulli o'simliklar" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Izoh <small>(Maydon ixtiyoriy)</small></label>
                            <textarea class="form-control" name="desc" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Bekor qilish</button>
                        <button type="submit" class="btn btn-primary">Saqlash</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // .fade-up ichidagi transform modalni backdrop ortida qoldirmasligi uchun
        document.querySelectorAll('.modal').forEach(m => document.body.appendChild(m));
    </script>
@endsection
