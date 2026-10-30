@extends('layouts.university')
@section('body')
    @php
        // Paginatsiya (alohida fayl kerak emas)
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

            // Oldingi
            $html .= $p->onFirstPage()
                ? '<li class="disabled"><span class="pg"><i class="bi bi-chevron-left"></i></span></li>'
                : '<li><a href="' .
                    e($p->previousPageUrl()) .
                    '" aria-label="Oldingi"><i class="bi bi-chevron-left"></i></a></li>';

            // Raqamlar
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

            // Keyingi
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

        .field input.word-input {
            text-transform: uppercase;
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            letter-spacing: .08em;
        }

        .field .help {
            font-size: .76rem;
            color: var(--muted);
            margin-top: 5px;
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

        .word-chip {
            display: inline-flex;
            gap: 4px;
            flex-wrap: wrap;
        }

        .word-chip span {
            width: 26px;
            height: 30px;
            border-radius: 7px;
            background: var(--primary-soft);
            color: var(--primary);
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: .85rem;
            display: flex;
            align-items: center;
            justify-content: center;
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
            color: var(--primary);
        }

        /* Paginatsiya */
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

        /* Modal */
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

        <a href="{{ route('anagramma.index') }}" class="back-link">
            <i class="bi bi-arrow-left"></i> Barcha guruhlar
        </a>

        <div class="page-head">
            <div>
                <div class="greet-eyebrow">So'z o'yini</div>
                <h1>
                    <h1>{{ $group->name }}</h1>
                </h1>
                <p class="page-sub">O'yin uchun so'z va maslahatlarni shu yerda qo'shing. Talabalar shu so'zlar bilan
                    o'ynaydi.</p>
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
                        <form action="{{ route('anagramma.regenerate', $group) }}" method="POST"
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
                        <div class="card-title">Yangi so'z</div>
                        <div class="card-sub-text">Anagramma uchun so'z qo'shing</div>
                    </div>
                </div>

                <form action="{{ route('anagramma.store', $group) }}" method="POST" autocomplete="off">
                    @csrf
                    <div class="field">
                        <label for="word">So'z</label>
                        <input type="text" id="word" name="word" class="word-input" value="{{ old('word') }}"
                            maxlength="30" placeholder="ALGORITM" required>
                        <div class="help">Faqat harflar, 3 dan 30 gacha. Bo'sh joy va tinish belgilarisiz.</div>
                    </div>

                    <div class="field">
                        <label for="hint">Maslahat</label>
                        <textarea id="hint" name="hint" maxlength="255" placeholder="Masalani yechish uchun qadamlar ketma-ketligi">{{ old('hint') }}</textarea>
                        <div class="help">O'quvchi so'zni topishi uchun ko'rsatma.</div>
                    </div>

                    <button type="submit" class="block btn-main"><i class="bi bi-plus-lg"></i> Qo'shish</button>
                </form>
            </div>

            {{-- So'zlar ro'yxati --}}
            <div class="card" id="words">
                <div class="card-head">
                    <div>
                        <div class="card-title">So'zlar ro'yxati</div>
                        <div class="card-sub-text">Jami: {{ $anagrams->total() }} ta so'z</div>
                    </div>
                </div>

                @if ($anagrams->isEmpty())
                    <div class="empty">
                        <i class="bi bi-shuffle"></i>
                        Hali so'z qo'shilmagan. Chapdagi formadan birinchi so'zni qo'shing.
                    </div>
                @else
                    <div class="table-wrap">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>So'z</th>
                                    <th>Maslahat</th>
                                    <th style="width:100px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($anagrams as $i => $item)
                                    <tr>
                                        <td class="cell-muted">{{ $anagrams->firstItem() + $i }}</td>
                                        <td>
                                            <div class="word-chip">
                                                @foreach (mb_str_split($item->word) as $ch)
                                                    <span>{{ $ch }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="cell-muted">{{ $item->hint ?: '—' }}</td>
                                        <td>
                                            <div class="row-actions">
                                                <button type="button" class="act-btn edit-btn" title="Tahrirlash"
                                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                                    data-action="{{ route('anagramma.update', $item->id) }}"
                                                    data-word="{{ $item->word }}" data-hint="{{ $item->hint }}">
                                                    <i class="bi bi-pencil"></i>
                                                </button>

                                                <form action="{{ route('anagramma.destroy', $item->id) }}" method="POST"
                                                    onsubmit="return confirm('Bu so\'zni o\'chirmoqchimisiz?')">
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
                    {!! $pager($anagrams, 'words') !!}
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
                                <th>Xatolar</th>
                                <th>Sana</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($results as $i => $r)
                                @php $wrong = $r->wrong_answers ?? []; @endphp
                                <tr>
                                    <td class="cell-muted">{{ $results->firstItem() + $i }}</td>
                                    <td class="cell-strong">{{ $r->player_name }}</td>
                                    <td><span class="badge-role">{{ $r->score }}</span></td>
                                    <td>{{ $r->correct }} / {{ $r->total }}</td>
                                    <td>
                                        @if (count($wrong))
                                            <button type="button" class="act-btn" style="width:auto; padding:0 10px; gap:5px;"
                                                data-bs-toggle="collapse" data-bs-target="#wrong-{{ $r->id }}">
                                                <i class="bi bi-x-circle"></i> {{ count($wrong) }}
                                            </button>
                                        @else
                                            <span class="cell-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="cell-muted">{{ $r->created_at->format('d.m.Y H:i') }}</td>
                                </tr>

                                @if (count($wrong))
                                    <tr>
                                        <td colspan="6" style="padding:0; border:0;">
                                            <div class="collapse" id="wrong-{{ $r->id }}">
                                                <div style="padding:12px 16px; background:var(--bg-soft); border-radius:12px; margin:6px 0 10px;">
                                                    <table class="data-table" style="margin:0;">
                                                        <thead>
                                                            <tr>
                                                                <th>Aralashgan</th>
                                                                <th>Yozgani</th>
                                                                <th>To'g'ri javob</th>
                                                                <th>Holat</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($wrong as $w)
                                                                <tr>
                                                                    <td class="cell-muted">{{ $w['scrambled'] }}</td>
                                                                    <td style="color:var(--coral); font-weight:700;">{{ $w['answer'] ?? '—' }}</td>
                                                                    <td style="color:var(--mint); font-weight:700;">{{ $w['correct'] }}</td>
                                                                    <td class="cell-muted">
                                                                        {{ ['wrong' => "Noto'g'ri", 'timeout' => 'Vaqt tugadi', 'skip' => "O'tkazildi"][$w['type']] ?? '' }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
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
                        <h5 class="modal-title">So'zni tahrirlash</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Yopish"></button>
                    </div>
                    <div class="modal-body">
                        <div class="field">
                            <label for="editWord">So'z</label>
                            <input type="text" id="editWord" name="word" class="word-input" maxlength="30"
                                required>
                        </div>
                        <div class="field" style="margin-bottom:0;">
                            <label for="editHint">Maslahat</label>
                            <textarea id="editHint" name="hint" maxlength="255"></textarea>
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
            a.download = 'anagramma-qr.png';
            a.click();
        });

        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('editForm').action = btn.dataset.action;
                document.getElementById('editWord').value = btn.dataset.word;
                document.getElementById('editHint').value = btn.dataset.hint || '';
            });
        });
    </script>
@endpush
