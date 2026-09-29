@extends('layouts.university')
@section('body')
    <style>
        /* ===== MODAL — TEMAGA MOSLASHTIRISH ===== */
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

        .form-control,
        .form-select {
            background: var(--bg-soft);
            color: var(--text);
            border-color: var(--line);
        }

        .form-control::placeholder {
            color: var(--muted);
        }

        .form-control:focus,
        .form-select:focus {
            background: var(--bg-soft);
            color: var(--text);
            border-color: var(--primary);
            box-shadow: 0 0 0 .2rem var(--primary-soft);
        }

        .form-select option {
            color: #17171D;
            background: #FFFFFF;
        }

        [data-theme="dark"] .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        [data-theme="dark"] .btn-light {
            background: var(--bg-soft);
            color: var(--text);
            border-color: var(--line);
        }

        [data-theme="dark"] .btn-light:hover {
            background: var(--line);
        }

        /* ===== STATISTIKA ===== */
        .stat-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .stat-box {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 12px 18px;
            min-width: 170px;
        }

        .stat-box .ico {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--primary-soft);
            color: var(--primary);
            display: grid;
            place-items: center;
            font-size: 1.2rem;
        }

        .stat-box b {
            display: block;
            font-size: 1.35rem;
            line-height: 1.1;
            color: var(--text);
        }

        .stat-box span {
            font-size: .8rem;
            color: var(--muted);
        }

        /* ===== TOOLBAR ===== */
        .toolbar {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 20px;
        }

        .search-box {
            position: relative;
            max-width: 420px;
        }

        .search-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
        }

        .search-box input {
            padding-left: 40px;
            border-radius: 12px;
        }

        .chips {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .chip {
            border: 1px solid var(--line);
            background: var(--card);
            color: var(--muted);
            padding: 6px 14px;
            border-radius: 999px;
            font-size: .85rem;
            font-weight: 600;
            cursor: pointer;
            transition: .15s;
        }

        .chip:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .chip.active {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }

        /* ===== FAN KARTALARI ===== */
        .fan-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 16px;
        }

        .fan-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 18px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            transition: transform .15s, box-shadow .15s, border-color .15s;
        }

        .fan-card:hover {
            transform: translateY(-3px);
            border-color: var(--primary);
            box-shadow: 0 10px 24px rgba(0, 0, 0, .08);
        }

        .fan-top {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .fan-icon {
            width: 46px;
            height: 46px;
            flex-shrink: 0;
            border-radius: 12px;
            background: var(--primary-soft);
            color: var(--primary);
            display: grid;
            place-items: center;
            font-size: 1.3rem;
        }

        .fan-title {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text);
        }

        .fan-desc {
            margin: 0;
            color: var(--muted);
            font-size: .88rem;
            min-height: 40px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .fan-group {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--bg-soft);
            border: 1px dashed var(--line);
            border-radius: 12px;
            padding: 10px 12px;
        }

        .fan-group i {
            color: var(--primary);
            font-size: 1.1rem;
        }

        .fan-group small {
            display: block;
            color: var(--muted);
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .fan-group b {
            color: var(--text);
            font-size: .92rem;
        }

        .fan-group .course {
            color: var(--muted);
            font-size: .8rem;
        }

        .fan-foot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--muted);
            font-size: .8rem;
            padding-top: 10px;
            border-top: 1px solid var(--line);
        }

        .fan-foot i {
            margin-right: 4px;
        }

        /* ===== BO'SH HOLAT ===== */
        .empty {
            text-align: center;
            padding: 50px 20px;
            color: var(--muted);
            background: var(--card);
            border: 1px dashed var(--line);
            border-radius: 16px;
        }

        .empty i {
            font-size: 2.6rem;
            color: var(--primary);
            display: block;
            margin-bottom: 8px;
        }

        .empty h5 {
            color: var(--text);
        }

        #noResult {
            display: none;
        }

        /* ===== KARTA BOSILADIGAN ===== */
        .fan-card {
            position: relative;
            cursor: pointer;
        }

        .fan-title a {
            color: inherit;
            text-decoration: none;
        }

        .fan-card:hover .fan-title a {
            color: var(--primary);
        }

        /* tugmalar havolaning ustida turishi uchun */
        .fan-actions {
            position: relative;
            z-index: 2;
            display: flex;
            gap: 6px;
            margin-left: auto;
        }

        .fan-actions form {
            margin: 0;
        }

        .fan-open {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            color: var(--primary);
            font-weight: 600;
        }

        .fan-open i {
            transition: transform .15s;
            margin: 0;
        }

        .fan-card:hover .fan-open i {
            transform: translateX(4px);
        }
    </style>

    <div class="page fade-up">
        <div class="page-head">
            <div>
                <h1>Darslar</h1>
                <p class="page-sub">Darslar nazorati.</p>
            </div>
            <button type="button" class="btn btn-primary rounded-3" data-bs-toggle="modal" data-bs-target="#addFanModal">
                <i class="bi bi-plus-lg"></i> Fan
            </button>
        </div>

        {{-- Statistika --}}
        <div class="stat-row">
            <div class="stat-box">
                <div class="ico"><i class="bi bi-journal-bookmark"></i></div>
                <div><b>{{ $fans->count() }}</b><span>Jami fanlar</span></div>
            </div>
            <div class="stat-box">
                <div class="ico"><i class="bi bi-people"></i></div>
                <div><b>{{ $guruhs->count() }}</b><span>Guruhlar</span></div>
            </div>
        </div>

        @if ($fans->isEmpty())
            <div class="empty">
                <i class="bi bi-journal-plus"></i>
                <h5>Hali fan qo'shilmagan</h5>
                <p class="mb-3">Birinchi fanni qo'shib, uni guruhga biriktiring.</p>
                <button class="btn btn-primary rounded-3" data-bs-toggle="modal" data-bs-target="#addFanModal">
                    <i class="bi bi-plus-lg"></i> Fan qo'shish
                </button>
            </div>
        @else
            {{-- Qidiruv va filtr --}}
            <div class="toolbar">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" id="fanSearch" class="form-control" placeholder="Fan nomini qidiring...">
                </div>
                <div class="chips" id="guruhChips">
                    <button type="button" class="chip active" data-guruh="all">Barchasi ({{ $fans->count() }})</button>
                    @foreach ($guruhs as $guruh)
                        <button type="button" class="chip" data-guruh="{{ $guruh->id }}">
                            {{ $guruh->title }} ({{ $fans->where('guruh_id', $guruh->id)->count() }})
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Fanlar --}}
            <div class="fan-grid" id="fanGrid">
                @foreach ($fans as $fan)
                    <div class="fan-card" data-guruh="{{ $fan->guruh_id }}" data-title="{{ mb_strtolower($fan->title) }}">
                        <div class="fan-top">
                            <div class="fan-icon"><i class="bi bi-book"></i></div>
                            <h5 class="fan-title">
                                <a href="{{ route('fans.show', $fan->id) }}" class="stretched-link">{{ $fan->title }}</a>
                            </h5>

                            <div class="fan-actions">
                                <button type="button" class="btn btn-success btn-sm btn-edit" data-bs-toggle="modal"
                                    data-bs-target="#editFanModal" data-action="{{ route('fans.update', $fan->id) }}"
                                    data-guruh="{{ $fan->guruh_id }}" data-title="{{ $fan->title }}"
                                    data-desc="{{ $fan->desc }}" title="Tahrirlash"><i
                                        class="bi bi-pencil"></i></button>

                                <form action="{{ route('fans.destroy', $fan->id) }}" method="POST"
                                    onsubmit="return confirm('Fanni o\'chirishni xohlaysizmi?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="O'chirish"><i
                                            class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>

                        <p class="fan-desc">{{ $fan->desc ?: 'Izoh kiritilmagan' }}</p>

                        <div class="fan-group">
                            <i class="bi bi-diagram-3"></i>
                            <div>
                                <small>Biriktirilgan guruh</small>
                                <b>{{ $fan->guruh->title ?? '—' }}</b>
                                @if ($fan->guruh?->course)
                                    <div class="course">{{ $fan->guruh->course->title }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="fan-foot">
                            <span><i class="bi bi-person"></i>{{ $fan->user->name ?? '—' }}</span>
                            <span class="fan-open">Ochish <i class="bi bi-arrow-right"></i></span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="empty mt-3" id="noResult">
                <i class="bi bi-search"></i>
                <h5>Hech narsa topilmadi</h5>
                <p class="mb-0">Qidiruv yoki guruh filtrini o'zgartirib ko'ring.</p>
            </div>
        @endif
    </div>

    {{-- ===== FAN TAHRIRLASH MODAL ===== --}}
    <div class="modal fade" id="editFanModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="editFanForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Fanni tahrirlash</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Guruh</label>
                            <select name="guruh_id" id="editGuruh" class="form-select" required>
                                @foreach ($guruhs as $guruh)
                                    <option value="{{ $guruh->id }}">
                                        {{ $guruh->title }}@if ($guruh->course)
                                            — {{ $guruh->course->title }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fan nomi</label>
                            <input type="text" name="title" id="editTitle" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Izoh <small>(Maydon ixtiyoriy)</small></label>
                            <textarea class="form-control" name="desc" id="editDesc" rows="3"></textarea>
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

    {{-- ===== FAN QO'SHISH MODAL ===== --}}
    <div class="modal fade" id="addFanModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('fans.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Fan qo'shish</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Guruh</label>
                            <select name="guruh_id" class="form-select" required>
                                <option value="" disabled selected>Guruhni tanlang</option>
                                @foreach ($guruhs as $guruh)
                                    <option value="{{ $guruh->id }}">
                                        {{ $guruh->title }}@if ($guruh->course)
                                            — {{ $guruh->course->title }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fan nomi</label>
                            <input type="text" name="title" class="form-control" placeholder="Masalan: Botanika"
                                required>
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
        (function() {
            const search = document.getElementById('fanSearch');
            const chips = document.querySelectorAll('#guruhChips .chip');
            const cards = document.querySelectorAll('#fanGrid .fan-card');
            const noResult = document.getElementById('noResult');
            if (!search) return;

            let activeGuruh = 'all';

            function apply() {
                const q = search.value.trim().toLowerCase();
                let visible = 0;
                cards.forEach(card => {
                    const okGuruh = activeGuruh === 'all' || card.dataset.guruh === activeGuruh;
                    const okText = card.dataset.title.includes(q);
                    const show = okGuruh && okText;
                    card.style.display = show ? '' : 'none';
                    if (show) visible++;
                });
                noResult.style.display = visible ? 'none' : 'block';
            }

            chips.forEach(chip => chip.addEventListener('click', () => {
                chips.forEach(c => c.classList.remove('active'));
                chip.classList.add('active');
                activeGuruh = chip.dataset.guruh;
                apply();
            }));

            search.addEventListener('input', apply);
        })();
    </script>

    <script>
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('editFanForm').action = btn.dataset.action;
                document.getElementById('editGuruh').value = btn.dataset.guruh;
                document.getElementById('editTitle').value = btn.dataset.title;
                document.getElementById('editDesc').value = btn.dataset.desc || '';
            });
        });
    </script>
@endsection
