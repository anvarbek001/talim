@extends('layouts.university')
@section('body')
    @php
        $pager = function ($p, $fragment) {
            if (!$p->hasPages()) {
                return '';
            }
            $p->fragment($fragment);

            $cur = $p->currentPage();
            $last = $p->lastPage();
            $pages = collect([1, $last])
                ->merge(range(max(1, $cur - 1), min($last, $cur + 1)))
                ->unique()
                ->sort()
                ->values();

            $html =
                '<div class="pager-wrap"><div class="pager-info">' .
                $p->firstItem() .
                '–' .
                $p->lastItem() .
                ' / ' .
                $p->total() .
                '</div><ul class="pager">';

            $html .= $p->onFirstPage()
                ? '<li class="disabled"><span class="pg"><i class="bi bi-chevron-left"></i></span></li>'
                : '<li><a href="' .
                    e($p->previousPageUrl()) .
                    '" aria-label="Oldingi"><i class="bi bi-chevron-left"></i></a></li>';

            $prev = null;
            foreach ($pages as $n) {
                if ($prev !== null && $n - $prev > 1) {
                    $html .= '<li class="dots"><span class="pg">…</span></li>';
                }
                $html .=
                    $n == $cur
                        ? '<li class="active"><span class="pg">' . $n . '</span></li>'
                        : '<li class="hide-sm"><a href="' . e($p->url($n)) . '">' . $n . '</a></li>';
                $prev = $n;
            }

            $html .= $p->hasMorePages()
                ? '<li><a href="' .
                    e($p->nextPageUrl()) .
                    '" aria-label="Keyingi"><i class="bi bi-chevron-right"></i></a></li>'
                : '<li class="disabled"><span class="pg"><i class="bi bi-chevron-right"></i></span></li>';

            return $html . '</ul></div>';
        };
    @endphp

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

        .creator-layout {
            display: grid;
            grid-template-columns: 340px 1fr;
            gap: 18px;
            align-items: start;
        }

        @media (max-width: 991px) {
            .creator-layout {
                grid-template-columns: 1fr;
            }
        }

        .field {
            margin-bottom: 14px;
        }

        .field label {
            display: block;
            font-size: .78rem;
            font-weight: 700;
            color: var(--muted);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .field input,
        .field textarea {
            width: 100%;
            padding: 11px 14px;
            border-radius: 12px;
            border: 1.5px solid var(--line);
            background: var(--bg-soft);
            color: var(--text);
            font-family: inherit;
            font-size: .9rem;
            outline: none;
            transition: .2s;
        }

        .field textarea {
            resize: vertical;
            min-height: 84px;
        }

        .field input:focus,
        .field textarea:focus {
            border-color: var(--primary);
            background: var(--card);
            box-shadow: 0 0 0 4px var(--primary-soft);
        }

        .field .help {
            font-size: .76rem;
            color: var(--muted);
            margin-top: 5px;
        }

        .seg {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .seg input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .seg label {
            margin: 0;
            padding: 11px;
            text-align: center;
            border-radius: 12px;
            border: 1.5px solid var(--line);
            background: var(--bg-soft);
            color: var(--muted);
            font-size: .88rem;
            font-weight: 700;
            text-transform: none;
            letter-spacing: 0;
            cursor: pointer;
            transition: .2s;
        }

        .seg input[value="1"]:checked+label {
            background: var(--mint-soft);
            border-color: var(--mint);
            color: var(--mint);
        }

        .seg input[value="0"]:checked+label {
            background: var(--coral-soft);
            border-color: var(--coral);
            color: var(--coral);
        }

        .btn-main {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 11px 18px;
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

        .btn-main.block {
            width: 100%;
        }

        .btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 11px 18px;
            border-radius: 12px;
            border: 1px solid var(--line);
            background: var(--card);
            color: var(--text);
            font-weight: 700;
            font-size: .88rem;
        }

        .row-actions {
            display: flex;
            gap: 6px;
        }

        .act-btn {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            border: 1px solid var(--line);
            background: var(--card);
            color: var(--muted);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: .2s;
        }

        .act-btn:hover {
            color: var(--primary);
            border-color: var(--primary);
            background: var(--primary-soft);
        }

        .act-btn.danger:hover {
            color: var(--coral);
            border-color: var(--coral);
            background: var(--coral-soft);
        }

        .badge-yes,
        .badge-no {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 11px;
            border-radius: 20px;
            font-size: .74rem;
            font-weight: 700;
        }

        .badge-yes {
            background: var(--mint-soft);
            color: var(--mint);
        }

        .badge-no {
            background: var(--coral-soft);
            color: var(--coral);
        }

        .statement-cell {
            max-width: 380px;
            font-weight: 600;
            line-height: 1.4;
        }

        .empty {
            text-align: center;
            padding: 40px 10px;
            color: var(--muted);
        }

        .empty i {
            font-size: 2.2rem;
            display: block;
            margin-bottom: 10px;
            color: var(--mint);
        }

        .pager-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid var(--line);
        }

        .pager-info {
            font-size: .8rem;
            color: var(--muted);
            font-weight: 600;
        }

        .pager {
            display: flex;
            align-items: center;
            gap: 6px;
            list-style: none;
            margin: 0;
            padding: 0;
            flex-wrap: wrap;
        }

        .pager a,
        .pager span.pg {
            min-width: 36px;
            height: 36px;
            padding: 0 10px;
            border-radius: 10px;
            border: 1px solid var(--line);
            background: var(--card);
            color: var(--text);
            font-size: .84rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: .2s;
        }

        .pager a:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary-soft);
            transform: translateY(-2px);
        }

        .pager .active span.pg {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
            box-shadow: 0 8px 16px -8px rgba(108, 92, 231, .6);
        }

        .pager .disabled span.pg {
            opacity: .4;
            cursor: default;
        }

        .pager .dots span.pg {
            border: none;
            background: transparent;
            color: var(--muted);
            min-width: 22px;
            padding: 0;
        }

        @media (max-width: 575px) {
            .pager-wrap {
                justify-content: center;
            }

            .pager .hide-sm {
                display: none;
            }
        }

        .modal-content {
            background: var(--card);
            color: var(--text);
            border: 1px solid var(--line);
            border-radius: 20px;
        }

        .modal-header,
        .modal-footer {
            border-color: var(--line);
        }

        .modal-title {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
        }

        [data-theme="dark"] .btn-close {
            filter: invert(1);
        }
    </style>

    <div class="page fade-up">
        <a href="{{ route('interesting.index') }}" class="back-link">
            <i class="bi bi-arrow-left"></i> Barcha o'yinlar
        </a>

        <div class="page-head">
            <div>
                <div class="greet-eyebrow">Ha/Yo'q o'yini</div>
                <h1>Ha / Yo'q</h1>
                <p class="page-sub">Tasdiqlarni yozing va to'g'ri javobni belgilang. Talabalar ularning to'g'ri yoki
                    noto'g'riligini aniqlaydi.</p>
            </div>
        </div>

        {{-- QR kod --}}
        <div class="card">
            <div class="card-head">
                <div>
                    <div class="card-title">Talabalar uchun QR kod</div>
                    <div class="card-sub-text">Skanerlab, ism kiritib o'ynashadi</div>
                </div>
            </div>

            <div style="display:flex; gap:22px; align-items:center; flex-wrap:wrap;">
                <div id="qrBox"
                    style="background:#fff; padding:12px; border-radius:16px; border:1px solid var(--line);"></div>

                <div style="flex:1; min-width:240px;">
                    <div class="field">
                        <label>Havola</label>
                        <input type="text" id="playUrl" value="{{ $playUrl }}" readonly>
                    </div>
                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        <button type="button" class="btn-ghost" id="copyBtn"><i class="bi bi-clipboard"></i>
                            Nusxalash</button>
                        <button type="button" class="btn-ghost" id="downloadQr"><i class="bi bi-download"></i> QR yuklab
                            olish</button>
                        <a href="{{ $playUrl }}" target="_blank" class="btn-ghost"><i
                                class="bi bi-box-arrow-up-right"></i> Ochish</a>
                        <form action="{{ route('yesno.regenerate') }}" method="POST"
                            onsubmit="return confirm('Eski QR kod ishlamay qoladi. Davom etasizmi?')">
                            @csrf
                            <button type="submit" class="btn-ghost"><i class="bi bi-arrow-repeat"></i> Yangilash</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="creator-layout">

            {{-- Qo'shish formasi --}}
            <div class="card">
                <div class="card-head">
                    <div>
                        <div class="card-title">Yangi savol</div>
                        <div class="card-sub-text">Tasdiq va to'g'ri javob</div>
                    </div>
                </div>

                <form action="{{ route('yesno.store') }}" method="POST" autocomplete="off">
                    @csrf
                    <div class="field">
                        <label for="statement">Tasdiq</label>
                        <textarea id="statement" name="statement" maxlength="500" required
                            placeholder="Python kompilyatsiya qilinadigan dasturlash tili">{{ old('statement') }}</textarea>
                    </div>

                    <div class="field">
                        <label>To'g'ri javob</label>
                        <div class="seg">
                            <input type="radio" name="answer" id="ansYes" value="1" @checked(old('answer') === '1')
                                required>
                            <label for="ansYes"><i class="bi bi-check-lg"></i> Ha</label>
                            <input type="radio" name="answer" id="ansNo" value="0" @checked(old('answer') === '0')>
                            <label for="ansNo"><i class="bi bi-x-lg"></i> Yo'q</label>
                        </div>
                    </div>

                    <div class="field">
                        <label for="explanation">Izoh (ixtiyoriy)</label>
                        <textarea id="explanation" name="explanation" maxlength="500" placeholder="Python interpretatsiya qilinadigan til.">{{ old('explanation') }}</textarea>
                        <div class="help">Talaba javob bergandan keyin ko'rinadi.</div>
                    </div>

                    <button type="submit" class="btn-main block"><i class="bi bi-plus-lg"></i> Qo'shish</button>
                </form>
            </div>

            {{-- Savollar ro'yxati --}}
            <div class="card" id="questions">
                <div class="card-head">
                    <div>
                        <div class="card-title">Savollar ro'yxati</div>
                        <div class="card-sub-text">Jami: {{ $questions->total() }} ta savol</div>
                    </div>
                </div>

                @if ($questions->isEmpty())
                    <div class="empty">
                        <i class="bi bi-patch-question"></i>
                        Hali savol qo'shilmagan. Chapdagi formadan birinchi savolni qo'shing.
                    </div>
                @else
                    <div class="table-wrap">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tasdiq</th>
                                    <th>Javob</th>
                                    <th style="width:100px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($questions as $i => $item)
                                    <tr>
                                        <td class="cell-muted">{{ $questions->firstItem() + $i }}</td>
                                        <td class="statement-cell">
                                            {{ $item->statement }}
                                            @if ($item->explanation)
                                                <div class="cell-muted" style="font-weight:500; margin-top:3px;">
                                                    <i class="bi bi-info-circle"></i> {{ $item->explanation }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->answer)
                                                <span class="badge-yes"><i class="bi bi-check-lg"></i> Ha</span>
                                            @else
                                                <span class="badge-no"><i class="bi bi-x-lg"></i> Yo'q</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="row-actions">
                                                <button type="button" class="act-btn edit-btn" title="Tahrirlash"
                                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                                    data-action="{{ route('yesno.update', $item->id) }}"
                                                    data-statement="{{ $item->statement }}"
                                                    data-answer="{{ $item->answer ? 1 : 0 }}"
                                                    data-explanation="{{ $item->explanation }}">
                                                    <i class="bi bi-pencil"></i>
                                                </button>

                                                <form action="{{ route('yesno.destroy', $item->id) }}" method="POST"
                                                    onsubmit="return confirm('Bu savolni o\'chirmoqchimisiz?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="act-btn danger" title="O'chirish">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {!! $pager($questions, 'questions') !!}
                @endif
            </div>
        </div>

        {{-- Natijalar --}}
        <div class="card" id="results">
            <div class="card-head">
                <div>
                    <div class="card-title">Talabalar natijalari</div>
                    <div class="card-sub-text">Ball bo'yicha tartiblangan, jami: {{ $results->total() }} ta natija</div>
                </div>
            </div>

            @if ($results->isEmpty())
                <div class="empty">
                    <i class="bi bi-trophy"></i>
                    Hali hech kim o'ynamagan. QR kodni talabalarga ko'rsating.
                </div>
            @else
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Ism</th>
                                <th>Ball</th>
                                <th>To'g'ri javob</th>
                                <th>Sana</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($results as $i => $r)
                                <tr>
                                    <td class="cell-muted">{{ $results->firstItem() + $i }}</td>
                                    <td class="cell-strong">{{ $r->player_name }}</td>
                                    <td><span class="badge-role">{{ $r->score }}</span></td>
                                    <td>{{ $r->correct }} / {{ $r->total }}</td>
                                    <td class="cell-muted">{{ $r->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {!! $pager($results, 'results') !!}
            @endif
        </div>
    </div>

    {{-- Tahrirlash oynasi --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="editForm" method="POST" autocomplete="off">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Savolni tahrirlash</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Yopish"></button>
                    </div>
                    <div class="modal-body">
                        <div class="field">
                            <label for="editStatement">Tasdiq</label>
                            <textarea id="editStatement" name="statement" maxlength="500" required></textarea>
                        </div>
                        <div class="field">
                            <label>To'g'ri javob</label>
                            <div class="seg">
                                <input type="radio" name="answer" id="editYes" value="1" required>
                                <label for="editYes"><i class="bi bi-check-lg"></i> Ha</label>
                                <input type="radio" name="answer" id="editNo" value="0">
                                <label for="editNo"><i class="bi bi-x-lg"></i> Yo'q</label>
                            </div>
                        </div>
                        <div class="field" style="margin-bottom:0;">
                            <label for="editExplanation">Izoh</label>
                            <textarea id="editExplanation" name="explanation" maxlength="500"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-ghost" data-bs-dismiss="modal">Bekor qilish</button>
                        <button type="submit" class="btn-main"><i class="bi bi-check-lg"></i> Saqlash</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        const url = document.getElementById('playUrl').value;
        new QRCode(document.getElementById('qrBox'), {
            text: url,
            width: 180,
            height: 180,
            colorDark: '#17171D',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.M
        });

        document.getElementById('copyBtn').addEventListener('click', () => {
            navigator.clipboard.writeText(url).then(() => showToast('success', 'Havola nusxalandi'));
        });

        document.getElementById('downloadQr').addEventListener('click', () => {
            const canvas = document.querySelector('#qrBox canvas');
            const a = document.createElement('a');
            a.href = canvas.toDataURL('image/png');
            a.download = 'yesno-qr.png';
            a.click();
        });

        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('editForm').action = btn.dataset.action;
                document.getElementById('editStatement').value = btn.dataset.statement;
                document.getElementById('editExplanation').value = btn.dataset.explanation || '';
                document.getElementById(btn.dataset.answer === '1' ? 'editYes' : 'editNo').checked = true;
            });
        });
    </script>
@endpush
