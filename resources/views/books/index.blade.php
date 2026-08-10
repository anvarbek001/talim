@extends('layouts.teacher')

@section('content')
    <div class="page">

        <div class="page-head fade-up">
            <h1>Kitob joylash</h1>
            <p class="page-sub">Kitob yoki qo'llanmangizni PDF formatida yuklang, narxini belgilang — bepul yoki pullik
                qilib joylashingiz mumkin.</p>
        </div>

        <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data" class="card fade-up"
            id="bookForm" style="animation-delay:.05s;">
            @csrf

            <div class="form-grid mb-16">
                <div class="field mb-16">
                    <label class="field-label">Kitob nomi</label>
                    <input type="text" name="title" class="text-control" placeholder="Masalan: Algebra masalalar to'plami"
                        value="{{ old('title') }}" required>
                    @error('title')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field mb-16">
                    <label class="field-label">Tavsif <span class="optional-tag">ixtiyoriy</span></label>
                    <textarea name="description" class="text-control" rows="3"
                        placeholder="Kitob haqida qisqacha...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field mb-16">
                    <label class="field-label">Narx turi</label>
                    <div class="mode-toggle" id="pricingModeToggle">
                        <button type="button" class="mode-btn active" data-pricing-mode="free">Bepul</button>
                        <button type="button" class="mode-btn" data-pricing-mode="paid">Pullik</button>
                    </div>
                    <div class="field-hint">Bu narx 1 oy uchun amal qiladi — muddat tugagach, o'quvchi qayta sotib olishi kerak bo'ladi.</div>
                </div>
                <div class="field" id="pricePanel" style="display:none;">
                    <label class="field-label">Narx (so'm) — 1 oylik</label>
                    <input type="number" name="price" id="price_input" class="text-control" min="1"
                        value="{{ old('price', 0) }}">
                    @error('price')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="field-label mb-8">PDF fayl(lar)</div>
            <label class="dropzone" id="bookDropzone" for="bookInput">
                <div class="dropzone-icon"><i class="bi bi-file-earmark-pdf"></i></div>
                <div class="dropzone-text">Fayllarni shu yerga tashlang yoki <span>tanlash uchun bosing</span></div>
                <div class="dropzone-hint">Faqat PDF — bir nechta fayl, maksimal 50 MB har biri</div>
                <input type="file" name="book_files[]" id="bookInput" accept="application/pdf" multiple hidden>
            </label>
            <div class="file-list" id="bookFileList"></div>
            @error('book_files')
                <div class="field-error">{{ $message }}</div>
            @enderror
            @error('book_files.*')
                <div class="field-error">{{ $message }}</div>
            @enderror

            <div class="form-actions">
                <a href="{{ route('books.mine') }}" class="btn-ghost">Bekor qilish</a>
                <button type="submit" class="btn-primary" id="submitBtn">
                    <i class="bi bi-cloud-upload"></i> Kitobni joylash
                </button>
            </div>
        </form>
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

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 24px;
            box-shadow: var(--shadow-sm);
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .field-label {
            font-size: .8rem;
            font-weight: 600;
            color: var(--muted);
        }

        .field-error {
            font-size: .76rem;
            color: var(--coral);
            margin-top: 2px;
        }

        .field-hint {
            font-size: .74rem;
            color: var(--muted);
            margin-top: 6px;
        }

        .optional-tag {
            font-size: .7rem;
            font-weight: 600;
            color: var(--muted);
        }

        .mb-8 {
            margin-bottom: 8px;
        }

        .mb-16 {
            margin-bottom: 16px;
        }

        .form-grid {
            display: grid;
            gap: 14px;
        }

        .select-control,
        .text-control {
            background: var(--bg-soft);
            border: 1.5px solid var(--line);
            border-radius: 10px;
            padding: 10px 12px;
            font-family: 'Inter', sans-serif;
            font-size: .88rem;
            color: var(--text);
            width: 100%;
            transition: .2s;
        }

        .select-control:focus,
        .text-control:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--card);
            box-shadow: 0 0 0 3px var(--primary-soft);
        }

        textarea.text-control {
            resize: vertical;
            font-family: inherit;
        }

        .input-invalid {
            border-color: var(--coral) !important;
            background: var(--coral-soft) !important;
        }

        .mode-toggle {
            display: inline-flex;
            background: var(--bg-soft);
            border: 1px solid var(--line);
            border-radius: 9px;
            padding: 3px;
            gap: 2px;
        }

        .mode-btn {
            border: none;
            background: none;
            padding: 6px 11px;
            border-radius: 7px;
            font-size: .74rem;
            font-weight: 600;
            color: var(--muted);
            transition: .15s;
        }

        .mode-btn.active {
            background: var(--primary-soft);
            color: var(--primary);
        }

        .dropzone {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            border: 1.5px dashed var(--line);
            border-radius: 14px;
            padding: 28px 16px;
            cursor: pointer;
            background: var(--bg-soft);
            transition: .2s;
        }

        .dropzone:hover,
        .dropzone.drag-over {
            border-color: var(--primary);
            background: var(--primary-soft);
        }

        .dropzone-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--primary-soft);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 12px;
        }

        .dropzone-text {
            font-weight: 600;
            font-size: .87rem;
        }

        .dropzone-text span {
            color: var(--primary);
        }

        .dropzone-hint {
            font-size: .74rem;
            color: var(--muted);
            margin-top: 4px;
        }

        .file-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 12px;
        }

        .file-chip {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 10px;
            background: var(--bg-soft);
            border: 1px solid var(--line);
            font-size: .82rem;
        }

        .file-chip .file-name {
            flex: 1;
            min-width: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 600;
        }

        .file-chip .file-size {
            color: var(--muted);
            font-size: .74rem;
            white-space: nowrap;
        }

        .file-chip .remove-file {
            color: var(--muted);
            font-size: 1rem;
            cursor: pointer;
            flex-shrink: 0;
        }

        .file-chip .remove-file:hover {
            color: var(--coral);
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-ghost {
            display: inline-flex;
            align-items: center;
            padding: 11px 20px;
            border-radius: 10px;
            border: 1.5px solid var(--line);
            background: var(--card);
            color: var(--muted);
            font-weight: 600;
            font-size: .88rem;
        }

        .btn-ghost:hover {
            border-color: var(--text);
            color: var(--text);
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
            transition: .2s;
        }

        .btn-primary:hover {
            background: #5A4BD6;
        }

        .btn-primary:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        @media (max-width:640px) {
            .card {
                padding: 18px;
            }

            .form-actions {
                flex-direction: column-reverse;
            }

            .btn-ghost,
            .btn-primary {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <script>
        (function() {
            const toggle = document.getElementById('pricingModeToggle');
            const pricePanel = document.getElementById('pricePanel');
            const priceInput = document.getElementById('price_input');

            function setPricingMode(mode) {
                toggle.querySelectorAll('.mode-btn').forEach(btn => {
                    btn.classList.toggle('active', btn.dataset.pricingMode === mode);
                });

                if (mode === 'free') {
                    pricePanel.style.display = 'none';
                    priceInput.value = 0;
                } else {
                    pricePanel.style.display = '';
                    if (!Number(priceInput.value)) priceInput.value = '';
                }
            }

            toggle.addEventListener('click', (e) => {
                const btn = e.target.closest('.mode-btn');
                if (btn) setPricingMode(btn.dataset.pricingMode);
            });

            setPricingMode(Number('{{ old('price', 0) }}') > 0 ? 'paid' : 'free');
        })();
    </script>

    <script>
        (function() {
            function humanSize(bytes) {
                if (bytes < 1024) return bytes + ' B';
                if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
                return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
            }

            const dropzone = document.getElementById('bookDropzone');
            const input = document.getElementById('bookInput');
            const list = document.getElementById('bookFileList');
            let dt = new DataTransfer();

            function render() {
                list.innerHTML = '';
                Array.from(input.files).forEach((file, idx) => {
                    list.insertAdjacentHTML('beforeend', `
                        <div class="file-chip">
                            <i class="bi bi-file-earmark-pdf file-icon"></i>
                            <span class="file-name">${file.name}</span>
                            <span class="file-size">${humanSize(file.size)}</span>
                            <i class="bi bi-x-lg remove-file" data-idx="${idx}"></i>
                        </div>
                    `);
                });
                list.querySelectorAll('.remove-file').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const idx = parseInt(btn.dataset.idx, 10);
                        dt = new DataTransfer();
                        Array.from(input.files).forEach((f, i) => {
                            if (i !== idx) dt.items.add(f);
                        });
                        input.files = dt.files;
                        render();
                    });
                });
            }

            function addFiles(fileList) {
                Array.from(fileList).forEach(f => dt.items.add(f));
                input.files = dt.files;
                render();
            }

            input.addEventListener('change', () => addFiles(input.files));

            ['dragover', 'dragenter'].forEach(evt => {
                dropzone.addEventListener(evt, (e) => {
                    e.preventDefault();
                    dropzone.classList.add('drag-over');
                });
            });
            ['dragleave', 'drop'].forEach(evt => {
                dropzone.addEventListener(evt, (e) => {
                    e.preventDefault();
                    dropzone.classList.remove('drag-over');
                });
            });
            dropzone.addEventListener('drop', (e) => {
                if (e.dataTransfer.files.length) addFiles(e.dataTransfer.files);
            });

            document.getElementById('bookForm').addEventListener('submit', function() {
                const btn = document.getElementById('submitBtn');
                btn.disabled = true;
                btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Yuklanmoqda...';
            });
        })();
    </script>
@endsection
