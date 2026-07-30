@extends('layouts.teacher')

@section('content')
    <div class="page">

        <div class="page-head fade-up">
            <div>
                <h1>Testlarim</h1>
                <p class="page-sub">Mavzu, DTM va sertifikat testlarini shu yerdan yarating hamda boshqaring.</p>
            </div>
            <a href="{{ route('tests.grading') }}" class="btn-ghost grading-link">
                <i class="bi bi-pencil-square"></i> Yozma javoblarni baholash
                @if ($pendingGradingCount > 0)
                    <span class="grading-badge">{{ $pendingGradingCount }}</span>
                @endif
            </a>
        </div>

        {{-- ========================================================= --}}
        {{-- TAB TUGMALARI --}}
        {{-- ========================================================= --}}
        <div class="test-tabs fade-up" style="animation-delay:.04s;">
            <button type="button" class="test-tab active" data-tab="topic"><i class="bi bi-bookmark"></i> Mavzu testi</button>
            <button type="button" class="test-tab" data-tab="dtm"><i class="bi bi-mortarboard"></i> DTM testi</button>
            <button type="button" class="test-tab" data-tab="sertifikat"><i class="bi bi-patch-check"></i> Sertifikat testi</button>
        </div>

        {{-- ========================================================= --}}
        {{-- MAVZU TESTI FORMASI --}}
        {{-- ========================================================= --}}
        <form action="{{ route('topic-tests.store') }}" method="POST" enctype="multipart/form-data" class="card fade-up qb-form" id="topicTestForm"
            data-tab-panel="topic" data-qb-auto style="animation-delay:.08s;">
            @csrf
            <div class="card-head">
                <div class="step-badge"><i class="bi bi-bookmark"></i></div>
                <div>
                    <div class="card-title">Mavzu testi yaratish</div>
                    <div class="card-sub-text">Tanlangan mavzu bo'yicha savol-javoblardan iborat test tuzing</div>
                </div>
            </div>

            <div class="form-grid form-grid-2 mb-16">
                <div class="field">
                    <label class="field-label">Mavzu</label>
                    @if ($topics->count())
                        <select name="topic_id" class="select-control" id="topic_test_topic_id" required>
                            <option value="" disabled selected>Mavzu tanlang</option>
                            @foreach ($topics as $topic)
                                <option value="{{ $topic->id }}" data-science="{{ $topic->science_id }}"
                                    data-grade="{{ $topic->grade_id }}" data-section="{{ $topic->section_id }}">
                                    {{ $topic->science->title }} | {{ $topic->grade->title }} |
                                    {{ $topic->section->title }} | {{ $topic->title }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="science_id" id="topic_test_science_id">
                        <input type="hidden" name="grade_id" id="topic_test_grade_id">
                        <input type="hidden" name="section_id" id="topic_test_section_id">
                    @else
                        <div class="empty-hint">
                            <i class="bi bi-info-circle"></i>
                            Hali mavzu yo'q — <a href="{{ route('lesson') }}">avval mavzu yarating</a>
                        </div>
                    @endif
                </div>
                <div class="field">
                    <label class="field-label">Davomiylik (daqiqa)</label>
                    <input type="number" name="duration_minutes" class="text-control" min="1" max="180" value="20"
                        required>
                </div>
            </div>

            <div class="form-grid mb-16">
                <div class="field mb-12">
                    <label class="field-label">Test nomi</label>
                    <input type="text" name="title" class="text-control" placeholder="Masalan: Kvadrat tenglamalar — 1-nazorat"
                        required>
                </div>
                <div class="field">
                    <label class="field-label">Tavsif <span class="optional-tag">ixtiyoriy</span></label>
                    <textarea name="description" class="text-control" rows="2" placeholder="Test haqida qisqacha..."></textarea>
                </div>
            </div>

            @include('tests.partials.questions-mode', ['prefix' => 'topic'])

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="bi bi-check-circle"></i> Mavzu testini saqlash
                </button>
            </div>
        </form>

        {{-- ========================================================= --}}
        {{-- DTM TESTI FORMASI --}}
        {{-- ========================================================= --}}
        <form action="{{ route('dtm-tests.store') }}" method="POST" enctype="multipart/form-data" class="card fade-up qb-form" id="dtmTestForm"
            data-tab-panel="dtm" data-qb-auto data-qb-with-blocks="1" style="display:none;animation-delay:.08s;">
            @csrf
            <div class="card-head">
                <div class="step-badge step-badge-alt"><i class="bi bi-mortarboard"></i></div>
                <div>
                    <div class="card-title">DTM testi yaratish</div>
                    <div class="card-sub-text">3 blokdan iborat DTM uslubidagi test tuzing — 11-sinf uchun avtomatik</div>
                </div>
            </div>

            <div class="form-grid form-grid-3 mb-16">
                <div class="field">
                    <label class="field-label">1-blok fani <span class="optional-tag">3.1 ball, 30 savol</span></label>
                    <select name="block1_science_id" class="select-control" required>
                        <option value="" disabled selected>Fan tanlang</option>
                        @foreach ($sciences as $science)
                            <option value="{{ $science->id }}">{{ $science->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label class="field-label">2-blok fani <span class="optional-tag">2.1 ball, 30 savol</span></label>
                    <select name="block2_science_id" class="select-control" required>
                        <option value="" disabled selected>Fan tanlang</option>
                        @foreach ($sciences as $science)
                            <option value="{{ $science->id }}">{{ $science->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label class="field-label">Davomiylik (daqiqa)</label>
                    <input type="number" name="duration_minutes" class="text-control" min="1" max="180" value="30"
                        required>
                </div>
            </div>

            <div class="empty-hint mb-16">
                <i class="bi bi-info-circle"></i>
                3-blok majburiy fanlar (Ona tili, Matematika, Tarix) avtomatik qo'shiladi — har biridan 10 tadan savol, 1.1 balldan.
            </div>

            <div class="form-grid mb-16">
                <div class="field mb-12">
                    <label class="field-label">Test nomi</label>
                    <input type="text" name="title" class="text-control" placeholder="Masalan: DTM — 2026-yil, 1-variant"
                        required>
                </div>
                <div class="field">
                    <label class="field-label">Tavsif <span class="optional-tag">ixtiyoriy</span></label>
                    <textarea name="description" class="text-control" rows="2" placeholder="Test haqida qisqacha..."></textarea>
                </div>
            </div>

            @include('tests.partials.questions-mode', ['prefix' => 'dtm', 'withBlocks' => true])

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="bi bi-check-circle"></i> DTM testini saqlash
                </button>
            </div>
        </form>

        {{-- ========================================================= --}}
        {{-- SERTIFIKAT TESTI FORMASI --}}
        {{-- ========================================================= --}}
        <form action="{{ route('sertifikat-tests.store') }}" method="POST" enctype="multipart/form-data" class="card fade-up qb-form"
            id="sertifikatTestForm" data-tab-panel="sertifikat" data-qb-auto data-wq-auto style="display:none;animation-delay:.08s;">
            @csrf
            <div class="card-head">
                <div class="step-badge step-badge-alt"><i class="bi bi-patch-check"></i></div>
                <div>
                    <div class="card-title">Sertifikat testi yaratish</div>
                    <div class="card-sub-text">Fan va daraja bo'yicha sertifikat testi tuzing</div>
                </div>
            </div>

            <div class="form-grid form-grid-3 mb-16">
                <div class="field">
                    <label class="field-label">Fan</label>
                    <select name="science_id" class="select-control" required>
                        <option value="" disabled selected>Fan tanlang</option>
                        @foreach ($sciences as $science)
                            <option value="{{ $science->id }}">{{ $science->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label class="field-label">Daraja <span class="optional-tag">ixtiyoriy</span></label>
                    <input type="text" name="level" class="text-control" placeholder="Masalan: B1, B2">
                </div>
                <div class="field">
                    <label class="field-label">Davomiylik (daqiqa)</label>
                    <input type="number" name="duration_minutes" class="text-control" min="1" max="180" value="40"
                        required>
                </div>
            </div>

            <div class="form-grid mb-16">
                <div class="field mb-12">
                    <label class="field-label">Test nomi</label>
                    <input type="text" name="title" class="text-control" placeholder="Masalan: Ingliz tili sertifikati — B1"
                        required>
                </div>
                <div class="field">
                    <label class="field-label">Tavsif <span class="optional-tag">ixtiyoriy</span></label>
                    <textarea name="description" class="text-control" rows="2" placeholder="Test haqida qisqacha..."></textarea>
                </div>
            </div>

            @include('tests.partials.questions-mode', ['prefix' => 'sertifikat'])

            <div class="wq-section">
                <div class="wq-section-head">
                    <div>
                        <div class="wq-section-title"><i class="bi bi-pencil-square"></i> Yozma qism <span class="optional-tag">ixtiyoriy</span></div>
                        <div class="card-sub-text">Erkin javob talab qiladigan savollar — o'quvchi javobini o'qituvchi qo'lda baholaydi</div>
                    </div>
                </div>
                <div class="wq-list"></div>
                <button type="button" class="btn-ghost wq-add-question mb-16">
                    <i class="bi bi-plus-lg"></i> Yozma savol qo'shish
                </button>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="bi bi-check-circle"></i> Sertifikat testini saqlash
                </button>
            </div>
        </form>
    </div>

    {{-- ========================================================= --}}
    {{-- MENING TESTLARIM RO'YXATI --}}
    {{-- ========================================================= --}}
    <div class="page">
        <div class="card fade-up" style="animation-delay:.15s;">
            <div class="card-head">
                <div class="step-badge step-badge-alt"><i class="bi bi-list-check"></i></div>
                <div>
                    <div class="card-title">Mening testlarim</div>
                    <div class="card-sub-text">Yaratilgan barcha testlaringiz shu yerda to'planadi</div>
                </div>
            </div>

            @php
                $mandatorySlugByScienceId = collect(\App\Models\DtmTest::MANDATORY_SUBJECTS)
                    ->mapWithKeys(fn ($title, $slug) => [(string) $sciences->firstWhere('title', $title)?->id => $slug])
                    ->filter(fn ($slug, $id) => $id !== '');

                $questionsPayload = fn ($test) => $test->questions->map(function ($q) use ($mandatorySlugByScienceId) {
                    $item = [
                        'text' => $q->question,
                        'options' => $q->options->map(fn ($o) => ['text' => $o->option_text])->values()->all(),
                        'correct' => (int) ($q->options->search(fn ($o) => $o->is_correct) ?: 0),
                    ];

                    if ($q instanceof \App\Models\DtmTestQuestion) {
                        $item['block'] = $q->block;
                        $item['subject'] = $q->block === 3 ? ($mandatorySlugByScienceId[(string) $q->science_id] ?? null) : null;
                    }

                    return $item;
                })->values()->all();

                $allTests = collect()
                    ->concat($topicTests->map(fn ($t) => [
                        'type' => 'Mavzu', 'kind' => 'topic', 'route' => 'topic-tests.destroy', 'updateRoute' => 'topic-tests.update',
                        'model' => $t, 'science' => $t->science, 'extra' => $t->section->title.' | '.$t->topic->title,
                        'edit' => [
                            'title' => $t->title, 'description' => $t->description, 'duration_minutes' => $t->duration_minutes,
                            'topic_id' => $t->topic_id, 'science_id' => $t->science_id, 'grade_id' => $t->grade_id, 'section_id' => $t->section_id,
                            'questions' => $questionsPayload($t),
                        ],
                    ]))
                    ->concat($dtmTests->map(fn ($t) => [
                        'type' => 'DTM', 'kind' => 'dtm', 'route' => 'dtm-tests.destroy', 'updateRoute' => 'dtm-tests.update',
                        'model' => $t, 'science' => $t->block1Science, 'extra' => '2-blok: '.$t->block2Science->title,
                        'edit' => [
                            'title' => $t->title, 'description' => $t->description, 'duration_minutes' => $t->duration_minutes,
                            'block1_science_id' => $t->block1_science_id, 'block2_science_id' => $t->block2_science_id,
                            'questions' => $questionsPayload($t),
                        ],
                    ]))
                    ->concat($sertifikatTests->map(fn ($t) => [
                        'type' => 'Sertifikat', 'kind' => 'sertifikat', 'route' => 'sertifikat-tests.destroy', 'updateRoute' => 'sertifikat-tests.update',
                        'model' => $t, 'science' => $t->science, 'extra' => $t->level ?? '—',
                        'edit' => [
                            'title' => $t->title, 'description' => $t->description, 'duration_minutes' => $t->duration_minutes,
                            'science_id' => $t->science_id, 'level' => $t->level,
                            'questions' => $questionsPayload($t),
                            'written_questions' => $t->writtenQuestions->map(fn ($w) => ['text' => $w->question, 'max_score' => $w->max_score])->values()->all(),
                        ],
                    ]))
                    ->sortByDesc(fn ($row) => $row['model']->created_at);
            @endphp

            @if ($allTests->isEmpty())
                <div class="empty-hint">
                    <i class="bi bi-info-circle"></i>
                    Hali test yaratilmagan — yuqoridagi formalardan birini to'ldiring
                </div>
            @else
                <div class="table-wrap">
                    <table class="topics-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Turi</th>
                                <th>Nomi</th>
                                <th>Fan</th>
                                <th>Tafsilot</th>
                                <th>Savollar</th>
                                <th>Davomiylik</th>
                                <th class="text-end">Amallar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($allTests as $row)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="topic-chip">{{ $row['type'] }}</span></td>
                                    <td class="topic-title-cell">{{ $row['model']->title }}</td>
                                    <td>{{ $row['science']->title ?? '—' }}</td>
                                    <td>{{ $row['extra'] }}</td>
                                    <td>{{ $row['model']->questions->count() }} ta</td>
                                    <td>{{ $row['model']->duration_minutes }} daq</td>
                                    <td class="text-end">
                                        <div class="row-actions">
                                            <button type="button" class="icon-btn-edit" title="Tahrirlash"
                                                data-action="edit-test" data-kind="{{ $row['kind'] }}"
                                                data-update-url="{{ route($row['updateRoute'], $row['model']) }}"
                                                data-payload="{{ json_encode($row['edit']) }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <form action="{{ route($row['route'], $row['model']) }}" method="POST"
                                                onsubmit="return confirm('Test o\'chirilsinmi?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="icon-btn icon-btn-delete" title="O'chirish">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- TESTNI TAHRIRLASH MODALLARI --}}
    {{-- ========================================================= --}}
    <div class="modal-overlay" id="editTopicTestModal">
        <div class="modal-box">
            <form action="" method="POST" class="qb-form" id="editTopicTestForm">
                @csrf
                @method('PUT')
                <div class="modal-head">
                    <div class="modal-title"><i class="bi bi-bookmark"></i> Mavzu testini tahrirlash</div>
                    <button type="button" class="modal-close" data-modal-close="editTopicTestModal"><i class="bi bi-x-lg"></i></button>
                </div>
                <div class="modal-body">
                    <div class="form-grid form-grid-2 mb-16">
                        <div class="field">
                            <label class="field-label">Mavzu</label>
                            <select name="topic_id" class="select-control" id="edit_topic_test_topic_id" required>
                                @foreach ($topics as $topic)
                                    <option value="{{ $topic->id }}" data-science="{{ $topic->science_id }}"
                                        data-grade="{{ $topic->grade_id }}" data-section="{{ $topic->section_id }}">
                                        {{ $topic->science->title }} | {{ $topic->grade->title }} |
                                        {{ $topic->section->title }} | {{ $topic->title }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="science_id" id="edit_topic_test_science_id">
                            <input type="hidden" name="grade_id" id="edit_topic_test_grade_id">
                            <input type="hidden" name="section_id" id="edit_topic_test_section_id">
                        </div>
                        <div class="field">
                            <label class="field-label">Davomiylik (daqiqa)</label>
                            <input type="number" name="duration_minutes" class="text-control" min="1" max="180" required
                                id="edit_topic_test_duration">
                        </div>
                    </div>
                    <div class="form-grid mb-16">
                        <div class="field mb-12">
                            <label class="field-label">Test nomi</label>
                            <input type="text" name="title" class="text-control" required id="edit_topic_test_title">
                        </div>
                        <div class="field">
                            <label class="field-label">Tavsif <span class="optional-tag">ixtiyoriy</span></label>
                            <textarea name="description" class="text-control" rows="2" id="edit_topic_test_description"></textarea>
                        </div>
                    </div>
                    <div class="qb-list"></div>
                    <button type="button" class="btn-ghost qb-add-question">
                        <i class="bi bi-plus-lg"></i> Savol qo'shish
                    </button>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-ghost" data-modal-close="editTopicTestModal">Bekor qilish</button>
                    <button type="submit" class="btn-primary"><i class="bi bi-check-circle"></i> Saqlash</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="editDtmTestModal">
        <div class="modal-box">
            <form action="" method="POST" class="qb-form" id="editDtmTestForm">
                @csrf
                @method('PUT')
                <div class="modal-head">
                    <div class="modal-title"><i class="bi bi-mortarboard"></i> DTM testini tahrirlash</div>
                    <button type="button" class="modal-close" data-modal-close="editDtmTestModal"><i class="bi bi-x-lg"></i></button>
                </div>
                <div class="modal-body">
                    <div class="form-grid form-grid-3 mb-16">
                        <div class="field">
                            <label class="field-label">1-blok fani <span class="optional-tag">3.1 ball</span></label>
                            <select name="block1_science_id" class="select-control" required id="edit_dtm_test_block1_science_id">
                                @foreach ($sciences as $science)
                                    <option value="{{ $science->id }}">{{ $science->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label class="field-label">2-blok fani <span class="optional-tag">2.1 ball</span></label>
                            <select name="block2_science_id" class="select-control" required id="edit_dtm_test_block2_science_id">
                                @foreach ($sciences as $science)
                                    <option value="{{ $science->id }}">{{ $science->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label class="field-label">Davomiylik (daqiqa)</label>
                            <input type="number" name="duration_minutes" class="text-control" min="1" max="180" required
                                id="edit_dtm_test_duration">
                        </div>
                    </div>
                    <div class="form-grid mb-16">
                        <div class="field mb-12">
                            <label class="field-label">Test nomi</label>
                            <input type="text" name="title" class="text-control" required id="edit_dtm_test_title">
                        </div>
                        <div class="field">
                            <label class="field-label">Tavsif <span class="optional-tag">ixtiyoriy</span></label>
                            <textarea name="description" class="text-control" rows="2" id="edit_dtm_test_description"></textarea>
                        </div>
                    </div>
                    <div class="qb-block-counts" data-qb-block-counts>
                        <span data-count="block1">1-blok: 0/30</span>
                        <span data-count="block2">2-blok: 0/30</span>
                        <span data-count="ona_tili">Ona tili: 0/10</span>
                        <span data-count="matematika">Matematika: 0/10</span>
                        <span data-count="tarix">Tarix: 0/10</span>
                    </div>
                    <div class="qb-list"></div>
                    <button type="button" class="btn-ghost qb-add-question">
                        <i class="bi bi-plus-lg"></i> Savol qo'shish
                    </button>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-ghost" data-modal-close="editDtmTestModal">Bekor qilish</button>
                    <button type="submit" class="btn-primary"><i class="bi bi-check-circle"></i> Saqlash</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="editSertifikatTestModal">
        <div class="modal-box">
            <form action="" method="POST" class="qb-form" id="editSertifikatTestForm">
                @csrf
                @method('PUT')
                <div class="modal-head">
                    <div class="modal-title"><i class="bi bi-patch-check"></i> Sertifikat testini tahrirlash</div>
                    <button type="button" class="modal-close" data-modal-close="editSertifikatTestModal"><i class="bi bi-x-lg"></i></button>
                </div>
                <div class="modal-body">
                    <div class="form-grid form-grid-3 mb-16">
                        <div class="field">
                            <label class="field-label">Fan</label>
                            <select name="science_id" class="select-control" required id="edit_sertifikat_test_science_id">
                                @foreach ($sciences as $science)
                                    <option value="{{ $science->id }}">{{ $science->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label class="field-label">Daraja <span class="optional-tag">ixtiyoriy</span></label>
                            <input type="text" name="level" class="text-control" id="edit_sertifikat_test_level">
                        </div>
                        <div class="field">
                            <label class="field-label">Davomiylik (daqiqa)</label>
                            <input type="number" name="duration_minutes" class="text-control" min="1" max="180" required
                                id="edit_sertifikat_test_duration">
                        </div>
                    </div>
                    <div class="form-grid mb-16">
                        <div class="field mb-12">
                            <label class="field-label">Test nomi</label>
                            <input type="text" name="title" class="text-control" required id="edit_sertifikat_test_title">
                        </div>
                        <div class="field">
                            <label class="field-label">Tavsif <span class="optional-tag">ixtiyoriy</span></label>
                            <textarea name="description" class="text-control" rows="2" id="edit_sertifikat_test_description"></textarea>
                        </div>
                    </div>
                    <div class="qb-list"></div>
                    <button type="button" class="btn-ghost qb-add-question mb-16">
                        <i class="bi bi-plus-lg"></i> Savol qo'shish
                    </button>
                    <div class="wq-section">
                        <div class="wq-section-head">
                            <div class="wq-section-title"><i class="bi bi-pencil-square"></i> Yozma qism <span class="optional-tag">ixtiyoriy</span></div>
                        </div>
                        <div class="wq-list"></div>
                        <button type="button" class="btn-ghost wq-add-question">
                            <i class="bi bi-plus-lg"></i> Yozma savol qo'shish
                        </button>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-ghost" data-modal-close="editSertifikatTestModal">Bekor qilish</button>
                    <button type="submit" class="btn-primary"><i class="bi bi-check-circle"></i> Saqlash</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- SAVOL/VARIANT SHABLONLARI (question/option builder) --}}
    {{-- ========================================================= --}}
    <template id="qbQuestionTemplate">
        <div class="qb-question">
            <div class="qb-question-head">
                <span class="qb-question-num">Savol</span>
                <button type="button" class="qb-remove-question" title="Savolni o'chirish"><i class="bi bi-trash3"></i></button>
            </div>
            <div class="field mb-12">
                <textarea class="text-control qb-question-text" rows="2" placeholder="Savol matnini kiriting" required></textarea>
            </div>
            <div class="qb-options"></div>
            <button type="button" class="qb-add-option"><i class="bi bi-plus-lg"></i> Variant qo'shish</button>
        </div>
    </template>

    <template id="qbOptionTemplate">
        <div class="qb-option">
            <input type="radio" class="qb-option-radio" required>
            <input type="text" class="text-control qb-option-text" placeholder="Variant matni" required>
            <button type="button" class="qb-remove-option" title="Variantni o'chirish"><i class="bi bi-x-lg"></i></button>
        </div>
    </template>

    <template id="wqQuestionTemplate">
        <div class="wq-question">
            <div class="wq-question-head">
                <span class="wq-question-num">Yozma savol</span>
                <button type="button" class="wq-remove-question" title="Savolni o'chirish"><i class="bi bi-trash3"></i></button>
            </div>
            <div class="form-grid form-grid-wq">
                <div class="field mb-12">
                    <textarea class="text-control wq-question-text" rows="2" placeholder="Yozma savol matnini kiriting" required></textarea>
                </div>
                <div class="field">
                    <label class="field-label">Maksimal ball</label>
                    <input type="number" class="text-control wq-question-score" min="1" max="100" value="10">
                </div>
            </div>
        </div>
    </template>

    <style>
        .page-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .grading-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .grading-badge {
            background: var(--coral);
            color: #fff;
            font-size: .7rem;
            font-weight: 800;
            padding: 1px 7px;
            border-radius: 20px;
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

        .optional-tag {
            font-size: .7rem;
            font-weight: 600;
            color: var(--muted);
            margin-left: 4px;
        }

        .mb-12 {
            margin-bottom: 12px;
        }

        .mb-16 {
            margin-bottom: 16px;
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

        .empty-hint a {
            color: #8A6100;
            font-weight: 700;
            text-decoration: underline;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 20px;
            border-radius: 10px;
            border: 1.5px solid var(--line);
            background: var(--card);
            color: var(--muted);
            font-weight: 600;
            font-size: .88rem;
        }

        .btn-ghost:hover {
            border-color: var(--primary);
            color: var(--primary);
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
        }

        .btn-primary:hover {
            background: #5A4BD6;
        }

        /* ===== TABS ===== */
        .test-tabs {
            display: inline-flex;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 4px;
            gap: 2px;
            margin-bottom: 18px;
        }

        .test-tab {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            background: none;
            padding: 9px 16px;
            border-radius: 9px;
            font-size: .85rem;
            font-weight: 600;
            color: var(--muted);
        }

        .test-tab.active {
            background: var(--primary-soft);
            color: var(--primary);
        }

        /* ===== SAVOLLAR REJIMI TOGGLE (qo'lda / excel) ===== */
        .qmode-toggle {
            display: inline-flex;
            background: var(--bg-soft);
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 3px;
            gap: 2px;
            margin-bottom: 16px;
        }

        .mode-btn {
            border: none;
            background: none;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: .8rem;
            font-weight: 600;
            color: var(--muted);
        }

        .mode-btn.active {
            background: var(--primary-soft);
            color: var(--primary);
        }

        .field-hint {
            font-size: .78rem;
            color: var(--muted);
            margin-top: 8px;
        }

        .field-hint a {
            color: var(--primary);
            font-weight: 600;
        }

        /* ===== EXCEL DROPZONE ===== */
        .template-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: .82rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .template-link:hover {
            text-decoration: underline;
        }

        .dropzone {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            border: 1.5px dashed var(--line);
            border-radius: 14px;
            padding: 26px 16px;
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
            background: var(--card);
            border: 1px solid var(--line);
            font-size: .82rem;
        }

        .file-chip .file-icon {
            color: var(--mint);
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

        /* ===== QUESTION BUILDER ===== */
        .qb-question {
            background: var(--bg-soft);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 14px;
        }

        .qb-question-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .qb-question-num {
            font-weight: 700;
            font-size: .86rem;
            color: var(--primary);
        }

        .qb-remove-question {
            border: none;
            background: none;
            color: var(--muted);
            font-size: .95rem;
        }

        .qb-remove-question:hover {
            color: var(--coral);
        }

        .qb-options {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 10px;
        }

        .qb-block-meta {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 12px;
        }

        .qb-block-hint {
            display: flex;
            flex-direction: column;
            gap: 4px;
            background: var(--primary-soft);
            border-radius: 12px;
            padding: 12px 14px;
            font-size: .8rem;
            color: var(--text);
            margin-bottom: 12px;
        }

        .qb-block-counts {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 14px;
        }

        .qb-block-counts span {
            padding: 4px 10px;
            border-radius: 20px;
            background: var(--bg-soft);
            border: 1px solid var(--line);
            font-size: .76rem;
            font-weight: 700;
            color: var(--muted);
        }

        .qb-block-counts span.qb-count-ok {
            color: #1f7a4d;
            border-color: #1f7a4d;
            background: #eafaf1;
        }

        .qb-option {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .qb-option-radio {
            flex-shrink: 0;
            width: 18px;
            height: 18px;
            accent-color: var(--mint);
        }

        .qb-remove-option {
            flex-shrink: 0;
            border: none;
            background: none;
            color: var(--muted);
            font-size: .9rem;
        }

        .qb-remove-option:hover {
            color: var(--coral);
        }

        .qb-add-option {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px dashed var(--line);
            background: none;
            color: var(--muted);
            font-size: .78rem;
            font-weight: 600;
            padding: 6px 10px;
            border-radius: 8px;
        }

        .qb-add-option:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        /* ===== YOZMA QISM (written questions) ===== */
        .wq-section {
            border-top: 1px dashed var(--line);
            padding-top: 16px;
            margin-bottom: 4px;
        }

        .wq-section-head {
            margin-bottom: 12px;
        }

        .wq-section-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            font-size: .92rem;
            color: var(--text);
        }

        .wq-section-title i {
            color: var(--amber);
        }

        .wq-question {
            background: var(--bg-soft);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 14px;
        }

        .wq-question-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .wq-question-num {
            font-weight: 700;
            font-size: .86rem;
            color: #8A6100;
        }

        .wq-remove-question {
            border: none;
            background: none;
            color: var(--muted);
            font-size: .95rem;
        }

        .wq-remove-question:hover {
            color: var(--coral);
        }

        .form-grid-wq {
            grid-template-columns: 1fr 140px;
            align-items: start;
        }

        /* ===== TABLE ===== */
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
        }

        .topics-table tbody tr:last-child {
            border-bottom: none;
        }

        .topics-table tbody tr:hover {
            background: var(--bg-soft);
        }

        .topic-title-cell {
            font-weight: 700;
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

        .icon-btn-edit,
        .icon-btn-delete {
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
        }

        .icon-btn-delete:hover {
            border-color: var(--coral);
            color: var(--coral);
            background: var(--coral-soft);
        }

        .icon-btn-edit:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary-soft);
        }

        /* ===== TAHRIRLASH MODALLARI ===== */
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
            max-width: 680px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 20px 22px;
            border-bottom: 1px solid var(--line);
            position: sticky;
            top: 0;
            background: var(--card);
            z-index: 2;
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
            position: sticky;
            bottom: 0;
            background: var(--card);
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

        /* Toast */
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

        @media (max-width:900px) {
            .form-grid-3 {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width:640px) {
            .form-grid-2,
            .form-grid-3,
            .form-grid-wq {
                grid-template-columns: 1fr;
            }

            .test-tabs {
                width: 100%;
                overflow-x: auto;
            }
        }
    </style>

    <script>
        // ============================================================
        // MAVZU SELECT — tanlangan mavzudan fan/sinf/bo'limni avtomatik
        // to'ldiradi (bo'lim/sinf alohida tanlanmaydi).
        // ============================================================
        (function() {
            const select = document.getElementById('topic_test_topic_id');
            if (!select) return;
            const scienceInput = document.getElementById('topic_test_science_id');
            const gradeInput = document.getElementById('topic_test_grade_id');
            const sectionInput = document.getElementById('topic_test_section_id');

            function sync() {
                const opt = select.options[select.selectedIndex];
                scienceInput.value = opt?.dataset.science || '';
                gradeInput.value = opt?.dataset.grade || '';
                sectionInput.value = opt?.dataset.section || '';
            }
            select.addEventListener('change', sync);
            sync();
        })();
    </script>

    <script>
        // ============================================================
        // TAB ALMASHTIRISH
        // ============================================================
        (function() {
            const tabs = document.querySelectorAll('.test-tab');
            const panels = document.querySelectorAll('[data-tab-panel]');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    tabs.forEach(t => t.classList.toggle('active', t === tab));
                    panels.forEach(p => {
                        p.style.display = p.dataset.tabPanel === tab.dataset.tab ? '' : 'none';
                    });
                });
            });
        })();
    </script>

    <script>
        // ============================================================
        // SAVOLLAR REJIMI — "Qo'lda kiritish" / "Excel yuklash" toggle.
        // Faol bo'lmagan panel ichidagi maydonlar "disabled" qilinadi —
        // aks holda ular yashirin holda ham forma bilan birga yuboriladi
        // va serverdagi validatsiyani buzadi (masalan bo'sh "savol" matni).
        // ============================================================
        (function() {
            function setPanelState(panel, isActive) {
                panel.style.display = isActive ? '' : 'none';
                panel.querySelectorAll('input, textarea, select').forEach(el => {
                    el.disabled = !isActive;
                });
            }

            document.querySelectorAll('[data-qmode-toggle]').forEach(toggle => {
                const form = toggle.closest('form');
                const panels = form.querySelectorAll('[data-qmode-panel]');

                // Boshlang'ich holat — faqat aktiv (odatda "qo'lda kiritish")
                // panel yoqilgan, qolganlari o'chirilgan bo'lishi kerak.
                panels.forEach(p => setPanelState(p, p.dataset.qmodePanel === 'manual'));

                toggle.querySelectorAll('.mode-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        toggle.querySelectorAll('.mode-btn').forEach(b => b.classList.toggle('active', b === btn));
                        panels.forEach(p => setPanelState(p, p.dataset.qmodePanel === btn.dataset.mode));
                    });
                });
            });
        })();
    </script>

    <script>
        // ============================================================
        // EXCEL DROPZONE — savollar faylini sudrab tashlash / tanlash
        // orqali yuklash, tanlangan faylni chiroyli ko'rinishda ko'rsatish.
        // ============================================================
        window.initQuestionsDropzone = function(prefix) {
            const dropzone = document.getElementById(prefix + '_dropzone');
            const input = document.getElementById(prefix + '_questions_file');
            const list = document.getElementById(prefix + '_file_list');
            if (!dropzone || !input || !list) return;

            function humanSize(bytes) {
                if (bytes < 1024) return bytes + ' B';
                if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
                return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
            }

            function render() {
                list.innerHTML = '';
                const file = input.files[0];
                if (!file) return;
                list.insertAdjacentHTML('beforeend', `
                    <div class="file-chip">
                        <i class="bi bi-file-earmark-spreadsheet file-icon"></i>
                        <span class="file-name">${file.name}</span>
                        <span class="file-size">${humanSize(file.size)}</span>
                        <i class="bi bi-x-lg remove-file" role="button"></i>
                    </div>
                `);
                list.querySelector('.remove-file').addEventListener('click', () => {
                    input.value = '';
                    render();
                });
            }

            input.addEventListener('change', render);

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
                if (e.dataTransfer.files.length) {
                    input.files = e.dataTransfer.files;
                    render();
                }
            });
        };
    </script>

    <script>
        // ============================================================
        // SAVOL/VARIANT QURUVCHISI — har bir "qb-form" ichida mustaqil
        // ishlaydi, name atributlarini questions[N][...] shaklida yozadi.
        // window.TestQuestionBuilder.initForm(form, initialQuestions) — tahrirlash
        // modallarini mavjud savollar bilan oldindan to'ldirish uchun ham ishlatiladi.
        // ============================================================
        (function() {
            const questionTpl = document.getElementById('qbQuestionTemplate');
            const optionTpl = document.getElementById('qbOptionTemplate');

            function buildOption(questionEl, qIndex, initialOption, isCorrect) {
                const oIndex = questionEl.dataset.optCounter ? parseInt(questionEl.dataset.optCounter, 10) : 0;
                questionEl.dataset.optCounter = oIndex + 1;

                const optEl = optionTpl.content.firstElementChild.cloneNode(true);
                const radio = optEl.querySelector('.qb-option-radio');
                const text = optEl.querySelector('.qb-option-text');
                radio.name = `questions[${qIndex}][correct]`;
                radio.value = oIndex;
                if (initialOption) {
                    text.value = initialOption.text;
                    radio.checked = !!isCorrect;
                } else if (oIndex === 0) {
                    radio.checked = true;
                }
                text.name = `questions[${qIndex}][options][${oIndex}][text]`;

                optEl.querySelector('.qb-remove-option').addEventListener('click', () => {
                    const options = questionEl.querySelectorAll('.qb-option');
                    if (options.length <= 2) {
                        showToast("Kamida 2 ta variant bo'lishi kerak", 'error');
                        return;
                    }
                    const wasChecked = radio.checked;
                    optEl.remove();
                    if (wasChecked) {
                        const first = questionEl.querySelector('.qb-option-radio');
                        if (first) first.checked = true;
                    }
                });

                questionEl.querySelector('.qb-options').appendChild(optEl);
            }

            function renumber(list) {
                list.querySelectorAll('.qb-question').forEach((q, i) => {
                    q.querySelector('.qb-question-num').textContent = (i + 1) + '-savol';
                });
            }

            function initForm(form, initialQuestions, opts) {
                opts = opts || {};
                const list = form.querySelector('.qb-list');
                const addQuestionBtn = form.querySelector('.qb-add-question');
                const countsEl = form.querySelector('[data-qb-block-counts]');
                list.innerHTML = '';
                let qCounter = 0;

                const blockTotals = { block1: 30, block2: 30, ona_tili: 10, matematika: 10, tarix: 10 };
                const blockLabels = { block1: '1-blok', block2: '2-blok', ona_tili: 'Ona tili', matematika: 'Matematika', tarix: 'Tarix' };

                function updateCounts() {
                    if (!countsEl) return;
                    const counts = { block1: 0, block2: 0, ona_tili: 0, matematika: 0, tarix: 0 };
                    list.querySelectorAll('.qb-question').forEach(q => {
                        const blockSelect = q.querySelector('.qb-question-block');
                        if (!blockSelect) return;
                        if (blockSelect.value === '1') counts.block1++;
                        else if (blockSelect.value === '2') counts.block2++;
                        else if (blockSelect.value === '3') {
                            const subject = q.querySelector('.qb-question-subject')?.value;
                            if (subject && counts[subject] !== undefined) counts[subject]++;
                        }
                    });
                    Object.keys(blockTotals).forEach(key => {
                        const el = countsEl.querySelector(`[data-count="${key}"]`);
                        if (!el) return;
                        el.textContent = `${blockLabels[key]}: ${counts[key]}/${blockTotals[key]}`;
                        el.classList.toggle('qb-count-ok', counts[key] === blockTotals[key]);
                    });
                }

                function buildQuestion(initial) {
                    const qIndex = qCounter++;
                    const qEl = questionTpl.content.firstElementChild.cloneNode(true);
                    const textarea = qEl.querySelector('.qb-question-text');
                    textarea.name = `questions[${qIndex}][text]`;
                    if (initial) textarea.value = initial.text;

                    if (opts.withBlocks) {
                        const meta = document.createElement('div');
                        meta.className = 'qb-block-meta';
                        meta.innerHTML = `
                            <div class="field">
                                <label class="field-label">Blok</label>
                                <select class="select-control qb-question-block">
                                    <option value="1">1-blok — asosiy fan (3.1 ball)</option>
                                    <option value="2">2-blok — qo'shimcha fan (2.1 ball)</option>
                                    <option value="3">3-blok — majburiy fan (1.1 ball)</option>
                                </select>
                            </div>
                            <div class="field qb-subject-field" style="display:none;">
                                <label class="field-label">Majburiy fan</label>
                                <select class="select-control qb-question-subject">
                                    <option value="ona_tili">Ona tili</option>
                                    <option value="matematika">Matematika</option>
                                    <option value="tarix">Tarix</option>
                                </select>
                            </div>
                        `;
                        const blockSelect = meta.querySelector('.qb-question-block');
                        const subjectField = meta.querySelector('.qb-subject-field');
                        const subjectSelect = meta.querySelector('.qb-question-subject');
                        blockSelect.name = `questions[${qIndex}][block]`;
                        subjectSelect.name = `questions[${qIndex}][subject]`;

                        function syncSubject() {
                            const isBlock3 = blockSelect.value === '3';
                            subjectField.style.display = isBlock3 ? '' : 'none';
                            subjectSelect.disabled = !isBlock3;
                        }

                        blockSelect.addEventListener('change', () => {
                            syncSubject();
                            updateCounts();
                        });
                        subjectSelect.addEventListener('change', updateCounts);

                        if (initial) {
                            blockSelect.value = initial.block || 1;
                            if (initial.subject) subjectSelect.value = initial.subject;
                        }
                        syncSubject();

                        qEl.querySelector('.qb-question-head').insertAdjacentElement('afterend', meta);
                    }

                    qEl.querySelector('.qb-remove-question').addEventListener('click', () => {
                        if (list.querySelectorAll('.qb-question').length <= 1) {
                            showToast('Kamida 1 ta savol bo\'lishi kerak', 'error');
                            return;
                        }
                        qEl.remove();
                        renumber(list);
                        updateCounts();
                    });

                    qEl.querySelector('.qb-add-option').addEventListener('click', () => buildOption(qEl, qIndex));

                    list.appendChild(qEl);

                    if (initial && Array.isArray(initial.options) && initial.options.length) {
                        initial.options.forEach((opt, i) => buildOption(qEl, qIndex, opt, i === initial.correct));
                    } else {
                        buildOption(qEl, qIndex);
                        buildOption(qEl, qIndex);
                    }
                    renumber(list);
                    updateCounts();
                }

                addQuestionBtn.onclick = () => buildQuestion();

                if (Array.isArray(initialQuestions) && initialQuestions.length) {
                    initialQuestions.forEach(q => buildQuestion(q));
                } else {
                    buildQuestion();
                }
            }

            window.TestQuestionBuilder = {
                initForm
            };

            document.querySelectorAll('.qb-form[data-qb-auto]').forEach(form => {
                initForm(form, [], { withBlocks: form.dataset.qbWithBlocks === '1' });
            });
        })();
    </script>

    <script>
        // ============================================================
        // YOZMA SAVOLLAR QURUVCHISI (Sertifikat testi) — written_questions[N][...]
        // window.WrittenQuestionBuilder.initForm(form, initialWritten) — tahrirlash
        // modalini mavjud yozma savollar bilan oldindan to'ldirish uchun ham ishlatiladi.
        // ============================================================
        (function() {
            const questionTpl = document.getElementById('wqQuestionTemplate');
            if (!questionTpl) return;

            function renumber(list) {
                list.querySelectorAll('.wq-question').forEach((q, i) => {
                    q.querySelector('.wq-question-num').textContent = (i + 1) + '-yozma savol';
                });
            }

            function initForm(form, initialWritten) {
                const list = form.querySelector('.wq-list');
                const addBtn = form.querySelector('.wq-add-question');
                if (!list || !addBtn) return;
                list.innerHTML = '';
                let wIndex = 0;

                function buildQuestion(initial) {
                    const index = wIndex++;
                    const qEl = questionTpl.content.firstElementChild.cloneNode(true);
                    const textarea = qEl.querySelector('.wq-question-text');
                    const scoreInput = qEl.querySelector('.wq-question-score');
                    textarea.name = `written_questions[${index}][text]`;
                    scoreInput.name = `written_questions[${index}][max_score]`;
                    if (initial) {
                        textarea.value = initial.text;
                        scoreInput.value = initial.max_score || 10;
                    }

                    qEl.querySelector('.wq-remove-question').addEventListener('click', () => {
                        qEl.remove();
                        renumber(list);
                    });

                    list.appendChild(qEl);
                    renumber(list);
                }

                addBtn.onclick = () => buildQuestion();

                if (Array.isArray(initialWritten)) {
                    initialWritten.forEach(w => buildQuestion(w));
                }
            }

            window.WrittenQuestionBuilder = {
                initForm
            };

            document.querySelectorAll('.qb-form[data-wq-auto]').forEach(form => initForm(form, []));
        })();
    </script>

    <script>
        // ============================================================
        // TESTNI TAHRIRLASH MODALLARI — "Tahrirlash" tugmasi bosilganda
        // mos modalni ochib, mavjud ma'lumotlar bilan to'ldiradi.
        // ============================================================
        (function() {
            const modals = {
                topic: document.getElementById('editTopicTestModal'),
                dtm: document.getElementById('editDtmTestModal'),
                sertifikat: document.getElementById('editSertifikatTestModal'),
            };

            function openModal(el) {
                el.classList.add('show');
            }

            function closeModal(el) {
                el.classList.remove('show');
            }

            document.querySelectorAll('[data-modal-close]').forEach(btn => {
                btn.addEventListener('click', () => closeModal(document.getElementById(btn.dataset.modalClose)));
            });

            Object.values(modals).forEach(modal => {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) closeModal(modal);
                });
            });

            document.addEventListener('keydown', (e) => {
                if (e.key !== 'Escape') return;
                Object.values(modals).forEach(modal => {
                    if (modal.classList.contains('show')) closeModal(modal);
                });
            });

            function fillTopicModal(payload) {
                const form = document.getElementById('editTopicTestForm');
                document.getElementById('edit_topic_test_topic_id').value = payload.topic_id;
                document.getElementById('edit_topic_test_science_id').value = payload.science_id;
                document.getElementById('edit_topic_test_grade_id').value = payload.grade_id;
                document.getElementById('edit_topic_test_section_id').value = payload.section_id;
                document.getElementById('edit_topic_test_duration').value = payload.duration_minutes;
                document.getElementById('edit_topic_test_title').value = payload.title;
                document.getElementById('edit_topic_test_description').value = payload.description || '';
                window.TestQuestionBuilder.initForm(form, payload.questions);
            }

            function fillDtmModal(payload) {
                const form = document.getElementById('editDtmTestForm');
                document.getElementById('edit_dtm_test_block1_science_id').value = payload.block1_science_id;
                document.getElementById('edit_dtm_test_block2_science_id').value = payload.block2_science_id;
                document.getElementById('edit_dtm_test_duration').value = payload.duration_minutes;
                document.getElementById('edit_dtm_test_title').value = payload.title;
                document.getElementById('edit_dtm_test_description').value = payload.description || '';
                window.TestQuestionBuilder.initForm(form, payload.questions, {
                    withBlocks: true
                });
            }

            function fillSertifikatModal(payload) {
                const form = document.getElementById('editSertifikatTestForm');
                document.getElementById('edit_sertifikat_test_science_id').value = payload.science_id;
                document.getElementById('edit_sertifikat_test_level').value = payload.level || '';
                document.getElementById('edit_sertifikat_test_duration').value = payload.duration_minutes;
                document.getElementById('edit_sertifikat_test_title').value = payload.title;
                document.getElementById('edit_sertifikat_test_description').value = payload.description || '';
                window.TestQuestionBuilder.initForm(form, payload.questions);
                window.WrittenQuestionBuilder.initForm(form, payload.written_questions || []);
            }

            const editTopicSelect = document.getElementById('edit_topic_test_topic_id');
            editTopicSelect?.addEventListener('change', () => {
                const opt = editTopicSelect.options[editTopicSelect.selectedIndex];
                document.getElementById('edit_topic_test_science_id').value = opt?.dataset.science || '';
                document.getElementById('edit_topic_test_grade_id').value = opt?.dataset.grade || '';
                document.getElementById('edit_topic_test_section_id').value = opt?.dataset.section || '';
            });

            document.querySelectorAll('[data-action="edit-test"]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const kind = btn.dataset.kind;
                    const payload = JSON.parse(btn.dataset.payload);
                    const modal = modals[kind];
                    if (!modal) return;

                    const form = modal.querySelector('form');
                    form.action = btn.dataset.updateUrl;

                    if (kind === 'topic') fillTopicModal(payload);
                    else if (kind === 'dtm') fillDtmModal(payload);
                    else if (kind === 'sertifikat') fillSertifikatModal(payload);

                    openModal(modal);
                });
            });
        })();
    </script>

    <script>
        // ============================================================
        // FLASH XABARLAR
        // ============================================================
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
            @endif
        });
    </script>
@endsection
