@extends('layouts.teacher')

@section('content')
    <div class="page">

        <div class="page-head fade-up">
            <h1>Bo'lim, mavzu va video darslar</h1>
            <p class="page-sub">Avval o'zingizga mos bo'lim va mavzu yarating, so'ng sinfni tanlab video dars va unga
                tegishli kitob/qo'llanmalarni yuklang.</p>
        </div>

        {{-- ========================================================= --}}
        {{-- FORM 1 — BO'LIM VA MAVZU YARATISH --}}
        {{-- ========================================================= --}}
        {{-- action="" ni o'zingizning bo'lim/mavzu saqlash manzilingiz bilan almashtiring, masalan: action="/sections" --}}
        <form action="{{ route('sections.store') }}" method="POST" class="card fade-up" id="sectionTopicForm"
            style="animation-delay:.05s;">
            @csrf
            <div class="card-head">
                <div class="step-badge">1</div>
                <div>
                    <div class="card-title">Bo'lim va mavzu yaratish</div>
                    <div class="card-sub-text">O'zingizga mos yangi bo'lim va mavzu qo'shing</div>
                </div>
            </div>

            <div class="form-grid form-grid-2 mb-16">
                <div class="field">
                    <label class="field-label">Fan</label>
                    <select name="science_id" id="science" class="select-control" required>
                        <option value="" disabled selected>Fan tanlang</option>
                        @foreach ($sciences as $science)
                            <option value="{{ $science->id }}" {{ old('science_id') == $science->id ? 'selected' : '' }}>
                                {{ $science->title }}</option>
                        @endforeach
                    </select>
                    @error('science_id')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label class="field-label">Sinf</label>
                    <select name="grade_id" id="grade" class="select-control" required>
                        <option value="" disabled selected>Sinf tanlang</option>
                        @foreach ($grades as $grade)
                            <option value="{{ $grade->id }}" {{ old('grade_id') == $grade->id ? 'selected' : '' }}>
                                {{ $grade->title }}</option>
                        @endforeach
                    </select>
                    @error('grade_id')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="split-row">
                <div class="split-col">
                    <div class="split-col-head">
                        <i class="bi bi-folder2"></i> Bo'lim
                        <div class="mode-toggle" id="sectionModeToggle">
                            <button type="button" class="mode-btn active" data-mode="new">Yangi yaratish</button>
                            <button type="button" class="mode-btn" data-mode="existing">Mavjudga qo'shish</button>
                        </div>
                    </div>

                    {{-- YANGI BO'LIM --}}
                    <div id="sectionNewPanel" class="mode-panel">
                        <div class="field mb-12">
                            <label class="field-label">Bo'lim nomi</label>
                            <input type="text" name="section_title" id="section_title" class="text-control"
                                placeholder="Masalan: Algebra" value="{{ old('section_title') }}">
                            @error('section_title')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="field">
                            <label class="field-label">Bo'lim tavsifi</label>
                            <textarea name="section_description" id="section_description" class="text-control" rows="3"
                                placeholder="Bo'lim haqida qisqacha...">{{ old('section_description') }}</textarea>
                            @error('section_description')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- MAVJUD BO'LIM --}}
                    <div id="sectionExistingPanel" class="mode-panel" style="display:none;">
                        <div class="field">
                            <label class="field-label">Mavjud bo'limni tanlang</label>
                            <select name="section_id" id="section_id_select" class="select-control">
                                <option value="" disabled selected>Bo'lim tanlang</option>
                                @foreach ($sections as $section)
                                    <option value="{{ $section->id }}" data-science="{{ $section->science_id }}"
                                        data-grade="{{ $section->grade_id }}"
                                        {{ old('section_id') == $section->id ? 'selected' : '' }}>
                                        {{ $section->science->title }} |
                                        {{ $section->grade->title }} | {{ $section->title }}</option>
                                @endforeach
                            </select>
                            @error('section_id')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                            <div class="field-hint" id="sectionExistingHint">Fan/sinf bilan mos bo'lmagan bo'limlar
                                ham ko'rinadi — to'g'ri bo'limni o'zingiz tanlang.</div>
                        </div>
                    </div>
                </div>

                <div class="split-divider"><i class="bi bi-arrow-right"></i></div>

                <div class="split-col">
                    <div class="split-col-head">
                        <i class="bi bi-bookmark"></i> Mavzu
                    </div>
                    <div class="field mb-12">
                        <label class="field-label">Mavzu nomi</label>
                        <input type="text" name="topic_title" class="text-control"
                            placeholder="Masalan: Kvadrat tenglamalar" id="topic_title" value="{{ old('topic_title') }}"
                            required>
                        @error('topic_title')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="field">
                        <label class="field-label">Mavzu tavsifi</label>
                        <textarea name="topic_description" class="text-control" id="topic_description" rows="3"
                            placeholder="Mavzu haqida qisqacha...">{{ old('topic_description') }}</textarea>
                        @error('topic_description')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" id="submit_data" class="btn-primary">
                    <i class="bi bi-check-circle"></i> Bo'lim va mavzuni saqlash
                </button>
            </div>
        </form>

        {{-- ========================================================= --}}
        {{-- FORM 2 — VIDEO DARS VA KITOB YUKLASH --}}
        {{-- ========================================================= --}}
        <form action="{{ route('lessons.store') }}" method="POST" enctype="multipart/form-data" class="card fade-up"
            style="animation-delay:.1s;" id="lessonForm">
            @csrf

            <div class="card-head">
                <div class="step-badge step-badge-alt">2</div>
                <div>
                    <div class="card-title">Video dars joylash</div>
                    <div class="card-sub-text">Sinfni tanlang, dars ma'lumotini kiriting, video va kitoblarni biriktiring
                    </div>
                </div>
            </div>

            <div class="form-grid form-grid-3 mb-16">
                <div class="field mb-16">
                    <label class="field-label">Mavzu</label>
                    @if ($topics->count())
                        <select name="topic_id" class="select-control" required>
                            <option value="" disabled selected>Mavzu tanlang</option>
                            @foreach ($topics as $topic)
                                <option value="{{ $topic->id }}">{{ $topic->science->title }} |
                                    {{ $topic->grade->title }} |
                                    {{ $topic->section->title }} | {{ $topic->title }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <div class="empty-hint">
                            <i class="bi bi-info-circle"></i>
                            Hali mavzu yo'q — <a href="#sectionTopicForm">avval yuqorida yarating</a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="upload-grid">
                <div class="upload-col">
                    <div class="field-label mb-8">Video dars</div>
                    <label class="dropzone" id="videoDropzone" for="videoInput">
                        <div class="dropzone-icon"><i class="bi bi-camera-reels"></i></div>
                        <div class="dropzone-text">Videoni shu yerga tashlang yoki <span>tanlash uchun bosing</span></div>
                        <div class="dropzone-hint">MP4, MOV — maksimal 1 GB</div>
                        <input type="file" name="lesson_files[]" id="videoInput" accept="video/*" hidden>
                    </label>
                    <div class="file-list" id="videoFileList"></div>
                    @error('lesson_files')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="upload-col">
                    <div class="field-label mb-8">Kitob / qo'llanma <span class="optional-tag">ixtiyoriy</span></div>
                    <label class="dropzone dropzone-alt" id="bookDropzone" for="bookInput">
                        <div class="dropzone-icon"><i class="bi bi-file-earmark-pdf"></i></div>
                        <div class="dropzone-text">Fayllarni tashlang yoki <span>tanlash uchun bosing</span></div>
                        <div class="dropzone-hint">PDF, DOC, DOCX, PPTX — bir nechta fayl</div>
                        <input type="file" name="lesson_files[]" id="bookInput" accept=".pdf,.doc,.docx,.ppt,.pptx"
                            multiple hidden>
                    </label>
                    <div class="file-list" id="bookFileList"></div>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-ghost" onclick="history.back()">Bekor qilish</button>
                <button type="submit" class="btn-primary" id="submitBtn">
                    <i class="bi bi-cloud-upload"></i> Darsni joylash
                </button>
            </div>
        </form>
    </div>

    <div class="page">
        {{-- ========================================================= --}}
        {{-- MAVZULAR RO'YXATI — tahrirlash/o'chirish tugmalari uchun    --}}
        {{-- backend (route/controller) qo'lda ulanadi.                 --}}
        {{-- ========================================================= --}}
        <div class="card fade-up" style="animation-delay:.15s;">
            <div class="card-head">
                <div class="step-badge step-badge-alt"><i class="bi bi-list-check"></i></div>
                <div>
                    <div class="card-title">Mavzular ro'yxati</div>
                    <div class="card-sub-text">Yaratilgan barcha bo'lim va mavzularni shu yerdan boshqaring</div>
                </div>
            </div>

            @if ($topics->count())
                <div class="table-wrap">
                    <table class="topics-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fan</th>
                                <th>Sinf</th>
                                <th>Bo'lim</th>
                                <th>Mavzu</th>
                                <th>Tavsif</th>
                                <th class="text-end">Amallar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topics as $topic)
                                <tr data-topic-id="{{ $topic->id }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="topic-chip">{{ $topic->science->title }}</span></td>
                                    <td>{{ $topic->grade->title }}</td>
                                    <td>{{ $topic->section->title }}</td>
                                    <td class="topic-title-cell">{{ $topic->title }}</td>
                                    <td class="topic-desc-cell">
                                        {{ $topic->description ? \Illuminate\Support\Str::limit($topic->description, 60) : '—' }}
                                    </td>
                                    <td class="text-end">
                                        <div class="row-actions">
                                            <button type="button" class="icon-btn icon-btn-edit" title="Tahrirlash"
                                                data-action="edit-topic" data-topic-id="{{ $topic->id }}"
                                                data-topic-title="{{ $topic->title }}"
                                                data-topic-description="{{ $topic->description }}"
                                                data-science-id="{{ $topic->science_id }}"
                                                data-grade-id="{{ $topic->grade_id }}"
                                                data-section-id="{{ $topic->section_id }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <form action="{{ route('topic.delete', $topic) }}"
                                                onsubmit="return confirm('Mavzu o\'chirilsinmi?')" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="icon-btn icon-btn-delete" title="O'chirish"
                                                    data-action="delete-topic" data-topic-id="{{ $topic->id }}">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                {{-- ========================================================= --}}
                                {{-- MAVZUNI TAHRIRLASH MODALI                                   --}}
                                {{-- Forma action/method hozircha qo'yilmagan — backend tayyor    --}}
                                {{-- bo'lgach shu formaning action="" qismiga route qo'ying va    --}}
                                {{-- pastdagi skriptdagi preventDefault() qatorini olib tashlang. --}}
                                {{-- ========================================================= --}}

                                <div class="modal-overlay" id="editTopicModal">
                                    <div class="modal-box">
                                        <form action="{{ route('topic.update', $topic->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="topic_id" id="edit_topic_id">

                                            <div class="modal-head">
                                                <div class="modal-title"><i class="bi bi-pencil-square"></i>
                                                    Mavzuni tahrirlash</div>
                                                <button type="button" class="modal-close" id="editTopicClose">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="form-grid form-grid-2 mb-16">
                                                    <div class="field">
                                                        <label class="field-label">Fan</label>
                                                        <select name="science_id" id="edit_science_id"
                                                            class="select-control" required>
                                                            @foreach ($sciences as $science)
                                                                <option value="{{ $science->id }}">
                                                                    {{ $science->title }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="field">
                                                        <label class="field-label">Sinf</label>
                                                        <select name="grade_id" id="edit_grade_id" class="select-control"
                                                            required>
                                                            @foreach ($grades as $grade)
                                                                <option value="{{ $grade->id }}">
                                                                    {{ $grade->title }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="field mb-16">
                                                    <label class="field-label">Bo'lim</label>
                                                    <select name="section_id" id="edit_section_id" class="select-control"
                                                        required>
                                                        @foreach ($sections as $section)
                                                            <option value="{{ $section->id }}">
                                                                {{ $section->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="field mb-16">
                                                    <label class="field-label">Mavzu nomi</label>
                                                    <input type="text" name="topic_title" id="edit_topic_title"
                                                        class="text-control" required>
                                                </div>

                                                <div class="field">
                                                    <label class="field-label">Mavzu tavsifi</label>
                                                    <textarea name="topic_description" id="edit_topic_description" class="text-control" rows="3"></textarea>
                                                </div>
                                            </div>

                                            <div class="modal-actions">
                                                <button type="button" class="btn-ghost" id="editTopicCancel">Bekor
                                                    qilish</button>
                                                <button type="submit" class="btn-primary">
                                                    <i class="bi bi-check-circle"></i> Saqlash
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-hint">
                    <i class="bi bi-info-circle"></i>
                    Hali mavzu yaratilmagan — <a href="#sectionTopicForm">yuqoridagi forma orqali qo'shing</a>
                </div>
            @endif
        </div>
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
            margin-bottom: 20px;
        }

        .card-head {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 22px;
        }

        .card-title {
            font-weight: 700;
            font-size: 1.05rem;
        }

        .card-sub-text {
            font-size: .82rem;
            color: var(--muted);
            margin-top: 2px;
        }

        .step-badge {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            flex-shrink: 0;
            background: var(--primary-soft);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-family: 'Sora', sans-serif;
            font-size: .95rem;
        }

        .step-badge-alt {
            background: var(--amber-soft);
            color: #8A6100;
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

        .empty-hint {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 11px 13px;
            border-radius: 10px;
            background: var(--amber-soft);
            color: #8A6100;
            font-size: .84rem;
            border: 1px dashed #E0B24C;
        }

        .empty-hint i {
            flex-shrink: 0;
        }

        .empty-hint a {
            color: #8A6100;
            font-weight: 700;
            text-decoration: underline;
        }

        .empty-hint a:hover {
            color: #6B4A00;
        }

        .optional-tag {
            font-size: .7rem;
            font-weight: 600;
            color: var(--muted);
            text-transform: none;
            margin-left: 4px;
        }

        .mb-8 {
            margin-bottom: 8px;
        }

        .mb-12 {
            margin-bottom: 12px;
        }

        .mb-16 {
            margin-bottom: 16px;
        }

        .mb-20 {
            margin-bottom: 20px;
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

        /* Xatolik holati — validatsiyadan o'tmagan maydonlar */
        .input-invalid {
            border-color: var(--coral) !important;
            background: var(--coral-soft) !important;
            animation: shake .3s ease;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-4px);
            }

            75% {
                transform: translateX(4px);
            }
        }

        .form-grid {
            display: grid;
            gap: 14px;
        }

        .form-grid-2 {
            grid-template-columns: repeat(2, 1fr);
        }

        .form-grid-3 {
            grid-template-columns: repeat(3, 1fr);
        }

        /* Split row — Bo'lim / Mavzu side by side */
        .split-row {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 18px;
            align-items: stretch;
        }

        .split-col {
            background: var(--bg-soft);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 16px;
        }

        .split-col-head {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            font-size: .88rem;
            margin-bottom: 14px;
            color: var(--text);
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .split-col-head i {
            color: var(--primary);
        }

        /* Mode toggle — "Yangi yaratish" / "Mavjudga qo'shish" */
        .mode-toggle {
            display: inline-flex;
            background: var(--card);
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

        .field-hint {
            font-size: .74rem;
            color: var(--muted);
            margin-top: 6px;
        }

        .split-divider {
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--line);
            font-size: 1.2rem;
        }

        /* Upload grid — video / book side by side */
        .upload-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 20px;
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
            height: 100%;
        }

        .dropzone:hover,
        .dropzone.drag-over {
            border-color: var(--primary);
            background: var(--primary-soft);
        }

        .dropzone-alt:hover,
        .dropzone-alt.drag-over {
            border-color: var(--amber);
            background: var(--amber-soft);
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

        .dropzone-alt .dropzone-icon {
            background: var(--amber-soft);
            color: #8A6100;
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
            animation: fadeUp .3s ease both;
        }

        .file-chip i.file-icon {
            color: var(--primary);
            font-size: 1rem;
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
        }

        .btn-ghost {
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

        /* Toast — xabar ko'rsatish (muvaffaqiyat / xatolik) */
        .toast-msg {
            position: fixed;
            left: 50%;
            bottom: 24px;
            transform: translate(-50%, 20px);
            background: var(--text);
            color: var(--bg);
            padding: 12px 20px;
            border-radius: 10px;
            font-size: .86rem;
            font-weight: 600;
            box-shadow: var(--shadow);
            opacity: 0;
            transition: opacity .25s ease, transform .25s ease;
            z-index: 999;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .toast-msg.show {
            opacity: 1;
            transform: translate(-50%, 0);
        }

        .toast-msg.toast-success {
            background: var(--mint);
            color: #fff;
        }

        .toast-msg.toast-error {
            background: var(--coral);
            color: #fff;
        }

        /* ===== MAVZULAR JADVALI ===== */
        .table-wrap {
            overflow-x: auto;
        }

        .topics-table {
            width: 100%;
            border-collapse: collapse;
            font-size: .86rem;
        }

        .topics-table th,
        .topics-table td {
            padding: 12px 14px;
            text-align: left;
            white-space: nowrap;
        }

        .topics-table th {
            color: var(--muted);
            font-size: .74rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
            border-bottom: 1.5px solid var(--line);
        }

        .topics-table tbody tr {
            border-bottom: 1px solid var(--line);
            transition: background .15s;
        }

        .topics-table tbody tr:last-child {
            border-bottom: none;
        }

        .topics-table tbody tr:hover {
            background: var(--bg-soft);
        }

        .topics-table td {
            color: var(--text);
        }

        .topic-title-cell {
            font-weight: 700;
        }

        .topic-desc-cell {
            color: var(--muted);
            white-space: normal;
            max-width: 260px;
        }

        .topic-chip {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            background: var(--primary-soft);
            color: var(--primary);
            font-size: .74rem;
            font-weight: 700;
        }

        .text-end {
            text-align: right;
        }

        .row-actions {
            display: inline-flex;
            gap: 6px;
            justify-content: flex-end;
        }

        .icon-btn {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            border: 1.5px solid var(--line);
            background: var(--card);
            color: var(--muted);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .95rem;
            transition: .15s;
        }

        .icon-btn-edit:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary-soft);
        }

        .icon-btn-delete:hover {
            border-color: var(--coral);
            color: var(--coral);
            background: var(--coral-soft);
        }

        /* ===== TAHRIRLASH MODALI ===== */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(20, 18, 35, .5);
            align-items: center;
            justify-content: center;
            padding: 20px;
            z-index: 1000;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-box {
            background: var(--card);
            border-radius: 18px;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 560px;
            max-height: 90vh;
            overflow-y: auto;
            animation: modalPop .2s ease;
        }

        @keyframes modalPop {
            from {
                transform: scale(.96);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .modal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 20px 22px;
            border-bottom: 1px solid var(--line);
        }

        .modal-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            font-size: 1.02rem;
        }

        .modal-title i {
            color: var(--primary);
        }

        .modal-close {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            border: none;
            background: var(--bg-soft);
            color: var(--muted);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .modal-close:hover {
            background: var(--coral-soft);
            color: var(--coral);
        }

        .modal-body {
            padding: 22px;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 18px 22px;
            border-top: 1px solid var(--line);
        }

        @media (max-width:640px) {
            .modal-actions {
                flex-direction: column-reverse;
            }

            .modal-actions .btn-ghost,
            .modal-actions .btn-primary {
                width: 100%;
                justify-content: center;
            }
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width:900px) {
            .form-grid-3 {
                grid-template-columns: repeat(2, 1fr);
            }

            .split-row {
                grid-template-columns: 1fr;
            }

            .split-divider {
                transform: rotate(90deg);
                padding: 4px 0;
            }
        }

        @media (max-width:640px) {
            .card {
                padding: 18px;
            }

            .form-grid-2 {
                grid-template-columns: 1fr;
            }

            .form-grid-3 {
                grid-template-columns: 1fr;
            }

            .upload-grid {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column-reverse;
            }

            .btn-ghost,
            .btn-primary {
                width: 100%;
                justify-content: center;
            }

            .card-head {
                gap: 10px;
            }

            .page-head h1 {
                font-size: 1.25rem;
            }
        }
    </style>


    <script>
        // ============================================================
        // FORM 1 — Bo'lim va mavzuni AJAX orqali, sahifani qayta
        // yuklamasdan backendga yuboramiz.
        // ESLATMA: bu skript hozircha O'CHIRILGAN, chunki controller
        // oddiy redirect()->with(...) orqali ishlayapti (AJAX emas).
        // Agar kelajakda AJAX'ga o'tsangiz, shu blokni yoqasiz.
        // ============================================================
        (function() {
            const sectionId = document.getElementById('section_id_select');
            let science = document.getElementById('science');
            let grade = document.getElementById('grade');
            let sectionTitle = document.getElementById('section_title');
            let sectionDesc = document.getElementById('section_description');
            let topicTitle = document.getElementById('topic_title');
            let topicDesc = document.getElementById('topic_description');
            const submitBtn = document.getElementById('submit_data');
            const token = document.querySelector('meta[name="csrf-token"]').content;
            const findUrl = "{{ route('section.find') }}";
            sectionId.addEventListener('change', async function(e) {
                e.preventDefault();
                const value = e.target.value;
                console.log(value);
                try {
                    const res = await fetch(findUrl, {
                        method: 'POST',
                        headers: {
                            "Content-Type": "application/json",
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token,
                        },
                        body: JSON.stringify({
                            section_id: value
                        }),
                    });

                    if (res.status === 419) {
                        // CSRF token muddati tugagan — sahifani yangilashni so'raymiz
                        throw new Error('Sessiya muddati tugagan. Sahifani yangilang.');
                    }
                    if (!res.ok) {
                        throw new Error('Server xatosi (' + res.status + ')');
                    }

                    if (res.ok) {
                        const data = await res.json();
                        console.log(data);
                        science.value = data.data.science.id
                        grade.value = data.data.grade.id
                    }

                } catch (err) {
                    console.error(err);
                    showToast(err.message || 'Xatolik yuz berdi, qayta urinib ko\'ring', 'error');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                }
            });
        })();
    </script>


    {{-- ============================================================ --}}
    {{-- BO'LIM REJIMI — "Yangi yaratish" / "Mavjudga qo'shish"         --}}
    {{-- ============================================================ --}}
    <script>
        (function() {
            const toggle = document.getElementById('sectionModeToggle');
            const newPanel = document.getElementById('sectionNewPanel');
            const existingPanel = document.getElementById('sectionExistingPanel');
            let science = document.getElementById('science');
            let grade = document.getElementById('grade');

            const sectionTitleInput = document.getElementById('section_title');
            const sectionIdSelect = document.getElementById('section_id_select');

            function setMode(mode) {
                toggle.querySelectorAll('.mode-btn').forEach(btn => {
                    btn.classList.toggle('active', btn.dataset.mode === mode);
                });

                if (mode === 'new') {
                    newPanel.style.display = '';
                    existingPanel.style.display = 'none';
                    sectionTitleInput.required = true;
                    sectionIdSelect.required = false;
                    science.value = '';
                    grade.value = '';
                    sectionIdSelect.value = '';
                } else {
                    newPanel.style.display = 'none';
                    existingPanel.style.display = '';
                    sectionTitleInput.required = false;
                    sectionIdSelect.required = true;
                    science.value = '';
                    grade.value = '';
                    sectionIdSelect.value = '';
                }
            }

            toggle.addEventListener('click', (e) => {
                const btn = e.target.closest('.mode-btn');
                if (btn) setMode(btn.dataset.mode);
            });

            // Boshlang'ich holat: agar oldingi validatsiyadan qaytgan
            // "section_id" tanlangan bo'lsa, "mavjud" rejimida ochiladi.
            setMode(sectionIdSelect.value ? 'existing' : 'new');
        })();
    </script>

    {{-- ============================================================ --}}
    {{-- FLASH XABARLAR — session('success'), session('error') va      --}}
    {{-- validatsiya xatolarini chiroyli toast ko'rinishida chiqaradi   --}}
    {{-- ============================================================ --}}
    <script>
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast-msg toast-${type}`;
            toast.innerHTML =
                `<i class="bi ${type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle'}"></i> <span>${message}</span>`;
            document.body.appendChild(toast);
            requestAnimationFrame(() => toast.classList.add('show'));
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }

        document.addEventListener('DOMContentLoaded', () => {
            let delay = 0;

            @if (session('success'))
                setTimeout(() => showToast(@json(session('success')), 'success'), delay);
                delay += 400;
            @endif

            @if (session('error'))
                setTimeout(() => showToast(@json(session('error')), 'error'), delay);
                delay += 400;
            @endif

            @if ($errors->any())
                setTimeout(() => showToast(@json($errors->first()), 'error'), delay);

                @foreach ($errors->keys() as $field)
                    document.querySelector('[name="{{ $field }}"]')?.classList.add('input-invalid');
                @endforeach
            @endif
        });
    </script>

    <script>
        (function() {
            function humanSize(bytes) {
                if (bytes < 1024) return bytes + ' B';
                if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
                return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
            }

            function setupDropzone(dropzoneId, inputId, listId, multiple, iconClass) {
                const dropzone = document.getElementById(dropzoneId);
                const input = document.getElementById(inputId);
                const list = document.getElementById(listId);
                let dt = new DataTransfer();

                function render() {
                    list.innerHTML = '';
                    Array.from(input.files).forEach((file, idx) => {
                        list.insertAdjacentHTML('beforeend', `
          <div class="file-chip">
            <i class="bi ${iconClass} file-icon"></i>
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
                    if (!multiple) dt = new DataTransfer();
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
            }

            setupDropzone('videoDropzone', 'videoInput', 'videoFileList', false, 'bi-camera-reels');
            setupDropzone('bookDropzone', 'bookInput', 'bookFileList', true, 'bi-file-earmark-pdf');

            document.getElementById('lessonForm').addEventListener('submit', function() {
                const btn = document.getElementById('submitBtn');
                btn.disabled = true;
                btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Yuklanmoqda...';
            });
        })();
    </script>

    {{-- ============================================================ --}}
    {{-- MAVZUNI TAHRIRLASH MODALI — ochish/yopish va maydonlarni       --}}
    {{-- to'ldirish. Forma yuborilishi hozircha to'xtatilgan (backend   --}}
    {{-- tayyor bo'lgach quyidagi preventDefault() qatorini o'chiring). --}}
    {{-- ============================================================ --}}
    <script>
        (function() {
            const modal = document.getElementById('editTopicModal');
            const form = document.getElementById('editTopicForm');
            const closeBtn = document.getElementById('editTopicClose');
            const cancelBtn = document.getElementById('editTopicCancel');

            function openModal(btn) {
                document.getElementById('edit_topic_id').value = btn.dataset.topicId;
                document.getElementById('edit_topic_title').value = btn.dataset.topicTitle;
                document.getElementById('edit_topic_description').value = btn.dataset.topicDescription;
                document.getElementById('edit_science_id').value = btn.dataset.scienceId;
                document.getElementById('edit_grade_id').value = btn.dataset.gradeId;
                document.getElementById('edit_section_id').value = btn.dataset.sectionId;

                modal.classList.add('show');
            }

            function closeModal() {
                modal.classList.remove('show');
                form.reset();
            }

            document.querySelectorAll('[data-action="edit-topic"]').forEach(btn => {
                btn.addEventListener('click', () => openModal(btn));
            });

            closeBtn.addEventListener('click', closeModal);
            cancelBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && modal.classList.contains('show')) closeModal();
            });

            // TODO: backend route tayyor bo'lgach, formaning action="" ni
            // route('topics.update', ':id') kabi to'g'ri manzilga o'zgartiring
            // va shu preventDefault() qatorini olib tashlang.
            form.addEventListener('submit', function(e) {
                showToast('Backend hali ulanmagan — saqlash funksiyasi tez orada qo\'shiladi', 'error');
            });
        })();
    </script>
@endsection
