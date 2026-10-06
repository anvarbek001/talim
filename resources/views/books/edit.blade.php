@extends('layouts.teacher')

@section('content')
    <div class="page">
        <a href="{{ route('books.mine') }}" class="be-back fade-up">
            <i class="bi bi-arrow-left"></i> Mening kitoblarim
        </a>

        <div class="page-head fade-up">
            <h1>Kitobni tahrirlash</h1>
            <p class="be-sub">Ma'lumotlarni yangilang, yangi PDF qo'shing yoki keraksiz fayllarni olib tashlang.</p>
        </div>

        <form action="{{ route('books.update', $book) }}" method="POST" enctype="multipart/form-data" class="be-grid fade-up">
            @csrf
            @method('PUT')

            {{-- CHAP: asosiy ma'lumotlar --}}
            <section class="be-card">
                <div class="be-card-title"><i class="bi bi-journal-text"></i> Asosiy ma'lumotlar</div>

                <div class="be-field">
                    <label class="be-label" for="title">Kitob nomi</label>
                    <input type="text" id="title" name="title" class="be-input" maxlength="255"
                        value="{{ old('title', $book->title) }}" placeholder="Masalan: Algebra asoslari" required>
                    @error('title')
                        <div class="be-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="be-field">
                    <div class="be-label-row">
                        <label class="be-label" for="description">Tavsif</label>
                        <span class="be-counter"><span id="descCount">0</span> / 1000</span>
                    </div>
                    <textarea id="description" name="description" class="be-input" rows="6" maxlength="1000"
                        placeholder="Kitob haqida qisqacha ma'lumot...">{{ old('description', $book->description) }}</textarea>
                    @error('description')
                        <div class="be-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="be-field">
                    <label class="be-label" for="price">Narx</label>
                    <div class="be-input-group">
                        <input type="number" id="price" name="price" class="be-input" min="0" step="1"
                            value="{{ old('price', $book->price) }}" required>
                        <span class="be-suffix">so'm</span>
                    </div>
                    @error('price')
                        <div class="be-error">{{ $message }}</div>
                    @enderror
                </div>
            </section>

            {{-- O'NG: fayllar --}}
            <section class="be-card">
                <div class="be-card-title"><i class="bi bi-files"></i> PDF fayllar</div>

                @if ($book->files->count())
                    <div class="be-label">Mavjud fayllar ({{ $book->files->count() }})</div>
                    <div class="be-files">
                        @foreach ($book->files as $file)
                            <div class="be-file" data-file>
                                <div class="be-file-icon"><i class="bi bi-file-earmark-pdf"></i></div>
                                <div class="be-file-info">
                                    <div class="be-file-name" title="{{ $file->original_name }}">{{ $file->original_name }}
                                    </div>
                                    <a href="{{ route('books.view', $book) }}" target="_blank" class="be-file-link">
                                        <i class="bi bi-eye"></i> Ko'rish
                                    </a>
                                </div>
                                <label class="be-del" title="O'chirish uchun belgilang">
                                    <input type="checkbox" name="delete_file_ids[]" value="{{ $file->id }}"
                                        @checked(is_array(old('delete_file_ids')) && in_array($file->id, old('delete_file_ids')))>
                                    <span><i class="bi bi-trash3"></i> O'chirish</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="be-empty"><i class="bi bi-inbox"></i> Hozircha fayl yo'q</div>
                @endif

                <div class="be-label" style="margin-top:18px">Yangi fayl qo'shish</div>
                <label class="be-drop" id="dropzone" for="bookInputEdit">
                    <i class="bi bi-cloud-arrow-up"></i>
                    <div class="be-drop-text">Fayllarni shu yerga tashlang yoki <span>tanlash uchun bosing</span></div>
                    <div class="be-drop-hint">Faqat PDF formatida, bir nechta fayl tanlash mumkin</div>
                    <input type="file" name="book_files[]" id="bookInputEdit" accept="application/pdf" multiple hidden>
                </label>
                @error('book_files')
                    <div class="be-error">{{ $message }}</div>
                @enderror
                @error('book_files.*')
                    <div class="be-error">{{ $message }}</div>
                @enderror

                <div class="be-files" id="newFiles"></div>
            </section>

            {{-- Tugmalar --}}
            <div class="be-actions">
                <a href="{{ route('books.mine') }}" class="btn-ghost">Bekor qilish</a>
                <button type="submit" class="btn-primary"><i class="bi bi-check2-circle"></i> Saqlash</button>
            </div>
        </form>
    </div>

    <style>
        .be-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            font-weight: 600;
            font-size: .85rem;
            margin-bottom: 14px;
        }

        .be-back:hover {
            color: var(--primary);
        }

        .be-sub {
            color: var(--muted);
            font-size: .88rem;
            margin: 6px 0 0;
        }

        .be-grid {
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            gap: 20px;
            margin-top: 18px;
            align-items: start;
        }

        .be-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 22px;
            box-shadow: var(--shadow-sm);
        }

        .be-card-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 18px;
            color: var(--text);
        }

        .be-card-title i {
            color: var(--primary);
        }

        .be-field {
            margin-bottom: 16px;
        }

        .be-label {
            display: block;
            font-size: .82rem;
            font-weight: 600;
            color: var(--muted);
            margin-bottom: 6px;
        }

        .be-label-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .be-counter {
            font-size: .75rem;
            color: var(--muted);
        }

        .be-input {
            width: 100%;
            padding: 11px 14px;
            border-radius: 12px;
            border: 1px solid var(--line);
            background: transparent;
            color: var(--text);
            font: inherit;
            font-size: .92rem;
            transition: border-color .15s, box-shadow .15s;
        }

        .be-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary) 20%, transparent);
        }

        textarea.be-input {
            resize: vertical;
            min-height: 120px;
        }

        .be-input-group {
            position: relative;
        }

        .be-input-group .be-input {
            padding-right: 64px;
        }

        .be-suffix {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: .85rem;
            font-weight: 600;
            pointer-events: none;
        }

        .be-error {
            color: #ef4444;
            font-size: .78rem;
            margin-top: 6px;
        }

        .be-files {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .be-file {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border: 1px solid var(--line);
            border-radius: 14px;
            transition: all .15s;
        }

        .be-file-icon {
            width: 38px;
            height: 38px;
            flex: none;
            display: grid;
            place-items: center;
            border-radius: 10px;
            background: color-mix(in srgb, var(--coral) 15%, transparent);
            color: var(--coral);
            font-size: 1.15rem;
        }

        .be-file-info {
            flex: 1;
            min-width: 0;
        }

        .be-file-name {
            font-weight: 600;
            font-size: .88rem;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .be-file-link {
            font-size: .78rem;
            color: var(--primary);
        }

        .be-file-size {
            font-size: .78rem;
            color: var(--muted);
        }

        .be-del input {
            display: none;
        }

        .be-del span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 12px;
            border-radius: 10px;
            font-size: .8rem;
            font-weight: 600;
            cursor: pointer;
            color: var(--muted);
            border: 1px solid var(--line);
            transition: all .15s;
            user-select: none;
        }

        .be-del span:hover {
            color: #ef4444;
            border-color: #ef4444;
        }

        .be-del input:checked+span {
            background: #ef4444;
            border-color: #ef4444;
            color: #fff;
        }

        .be-file:has(input:checked) {
            border-color: #ef4444;
            opacity: .65;
        }

        .be-file:has(input:checked) .be-file-name {
            text-decoration: line-through;
        }

        .be-empty {
            text-align: center;
            padding: 18px;
            color: var(--muted);
            font-size: .88rem;
            border: 1px dashed var(--line);
            border-radius: 14px;
        }

        .be-drop {
            display: block;
            text-align: center;
            padding: 26px 16px;
            cursor: pointer;
            border: 2px dashed var(--line);
            border-radius: 16px;
            transition: all .15s;
        }

        .be-drop>i {
            font-size: 2rem;
            color: var(--primary);
        }

        .be-drop:hover,
        .be-drop.drag {
            border-color: var(--primary);
            background: color-mix(in srgb, var(--primary) 7%, transparent);
        }

        .be-drop-text {
            font-weight: 600;
            font-size: .9rem;
            margin-top: 6px;
            color: var(--text);
        }

        .be-drop-text span {
            color: var(--primary);
        }

        .be-drop-hint {
            font-size: .78rem;
            color: var(--muted);
            margin-top: 4px;
        }

        #newFiles {
            margin-top: 12px;
        }

        .be-file.new {
            border-color: var(--primary);
        }

        .be-file.new .be-file-icon {
            background: color-mix(in srgb, var(--primary) 15%, transparent);
            color: var(--primary);
        }

        .be-actions {
            grid-column: 1 / -1;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding-top: 4px;
        }

        .be-actions .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        @media (max-width:900px) {
            .be-grid {
                grid-template-columns: 1fr;
            }

            .be-actions {
                position: sticky;
                bottom: 0;
                padding: 12px 0;
                background: var(--bg, transparent);
                backdrop-filter: blur(8px);
            }

            .be-actions>* {
                flex: 1;
                text-align: center;
                justify-content: center;
            }
        }
    </style>

    <script>
        (function() {
            const input = document.getElementById('bookInputEdit');
            const drop = document.getElementById('dropzone');
            const list = document.getElementById('newFiles');
            const desc = document.getElementById('description');
            const count = document.getElementById('descCount');

            // Tavsif hisoblagichi
            const updateCount = () => count.textContent = desc.value.length;
            desc.addEventListener('input', updateCount);
            updateCount();

            const fmt = b => b > 1048576 ? (b / 1048576).toFixed(1) + ' MB' : Math.max(1, Math.round(b / 1024)) + ' KB';
            const esc = s => s.replace(/[&<>"']/g, c => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            } [c]));

            function renderNew() {
                list.innerHTML = '';
                Array.from(input.files).forEach(f => {
                    const el = document.createElement('div');
                    el.className = 'be-file new';
                    el.innerHTML =
                        '<div class="be-file-icon"><i class="bi bi-file-earmark-plus"></i></div>' +
                        '<div class="be-file-info"><div class="be-file-name">' + esc(f.name) + '</div>' +
                        '<div class="be-file-size">' + fmt(f.size) + ' · yangi</div></div>';
                    list.appendChild(el);
                });
            }

            input.addEventListener('change', renderNew);

            ['dragenter', 'dragover'].forEach(ev => drop.addEventListener(ev, e => {
                e.preventDefault();
                drop.classList.add('drag');
            }));
            ['dragleave', 'drop'].forEach(ev => drop.addEventListener(ev, e => {
                e.preventDefault();
                drop.classList.remove('drag');
            }));
            drop.addEventListener('drop', e => {
                const dt = new DataTransfer();
                Array.from(e.dataTransfer.files)
                    .filter(f => f.type === 'application/pdf')
                    .forEach(f => dt.items.add(f));
                input.files = dt.files;
                renderNew();
            });
        })();
    </script>
@endsection
