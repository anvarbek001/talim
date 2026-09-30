@extends('layouts.university')
@section('body')

    <style>
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

        .form-control:focus {
            background: var(--bg-soft);
            color: var(--text);
            border-color: var(--primary);
            box-shadow: 0 0 0 .2rem var(--primary-soft);
        }

        .student-card {
            cursor: pointer;
            transition: transform .15s, border-color .15s, box-shadow .15s;
        }

        .student-card:hover {
            transform: translateY(-3px);
            border-color: var(--primary);
            box-shadow: 0 10px 24px rgba(0, 0, 0, .08);
        }

        .sg-top {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 16px;
        }

        .sg-top img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
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

        .sg-fan-head b {
            color: var(--text);
        }

        .sg-fan-head small {
            color: var(--muted);
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

        .gb {
            display: inline-grid;
            place-items: center;
            min-width: 30px;
            height: 30px;
            padding: 0 6px;
            border-radius: 8px;
            color: #fff;
            font-weight: 700;
        }

        .sg-item .gb {
            min-width: 24px;
            height: 24px;
            border-radius: 999px;
            font-size: .78rem;
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

        .student-card.graded {
            border-left: 4px solid #16a34a;
        }

        .student-card.ungraded {
            border-left: 4px solid var(--line);
        }

        .st-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 6px;
            padding: 2px 10px 2px 3px;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 600;
            background: var(--bg-soft);
            border: 1px solid var(--line);
            color: var(--muted);
        }

        .st-badge .gb {
            min-width: 24px;
            height: 24px;
            border-radius: 999px;
            font-size: .78rem;
        }

        .st-badge.none {
            padding: 2px 10px;
        }
    </style>

    <div class="page fade-up">
        <div class="page-head">
            <div>
                <h1>{{ $guruh->title }}</h1>
                <p class="page-sub">{{ $guruh->course->title ?? '' }} — o'quvchilar ro'yxati</p>
                <p>
                    Jami o'quvchilar: {{ $students->count() }} ta
                    · Baholangan: {{ $students->filter(fn($s) => isset($gradeData[$s->id]))->count() }}
                    · Baholanmagan: {{ $students->filter(fn($s) => !isset($gradeData[$s->id]))->count() }}
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('guruhs.index') }}" class="btn btn-outline-secondary rounded-3">
                    <i class="bi bi-arrow-left"></i> Orqaga
                </a>
                <button type="button" class="btn btn-outline-primary rounded-3" data-bs-toggle="modal"
                    data-bs-target="#importStudentModal">
                    <i class="bi bi-file-earmark-excel"></i> Excel'dan import
                </button>
                <button type="button" class="btn btn-primary rounded-3" data-bs-toggle="modal"
                    data-bs-target="#addStudentModal">
                    <i class="bi bi-person-plus"></i> O'quvchi qo'shish
                </button>
            </div>
        </div>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($students->isEmpty())
            <div class="card text-center py-5">
                <i class="bi bi-people fs-2 text-muted"></i>
                <p class="mt-3 mb-2 text-muted">Bu guruhda hali o'quvchi yo'q.</p>
                <div>
                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#addStudentModal">
                        O'quvchi qo'shish
                    </button>
                </div>
            </div>
        @else
            <div class="row g-3">
                @foreach ($students as $student)
                    @php
                        $info = $gradeData[$student->id] ?? null;
                        $cls = $info ? 'g-' . max(2, min(5, (int) round($info['avg']))) : '';
                    @endphp
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 student-card {{ $info ? 'graded' : 'ungraded' }}" role="button"
                            data-student-id="{{ $student->id }}" data-student-name="{{ $student->name }}"
                            data-student-photo="{{ $student->photo ? asset('storage/' . $student->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($student->name) }}">
                            <div class="card-body d-flex align-items-center gap-3">
                                <img src="{{ $student->photo ? asset('storage/' . $student->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($student->name) }}"
                                    class="rounded-circle" style="width:56px;height:56px;object-fit:cover;">
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ $student->name }}</div>
                                    <div class="text-muted small">{{ $guruh->title }}</div>
                                </div>
                                <div class="d-flex flex-column gap-1">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editStudentModal" data-student-id="{{ $student->id }}"
                                        data-student-name="{{ $student->name }}"
                                        data-student-photo="{{ $student->photo ? asset('storage/' . $student->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($student->name) }}">
                                        <i class="bi bi-pen"></i>
                                    </button>
                                    <form action="{{ route('students.destroy', $student) }}" method="POST"
                                        onsubmit="return confirm('O\'quvchini o\'chirishni tasdiqlaysizmi?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $students->links() }}
            </div>
        @endif
    </div>

    {{-- ===== EXCEL IMPORT MODAL ===== --}}
    <div class="modal fade" id="importStudentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('students.import', $guruh->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Excel orqali import qilish</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Excel fayl (.xlsx)</label>
                            <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls" required>
                            <div class="form-text">
                                Faylda faqat bitta ustun bo'lishi kerak: <code>name</code>
                                (o'quvchining to'liq ismi). Rasm avtomatik avatar sifatida yaratiladi.
                            </div>
                        </div>
                        <a href="{{ asset('templates/students_template.xlsx') }}" class="small">
                            <i class="bi bi-download"></i> Namuna fayl yuklash
                        </a>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Bekor qilish</button>
                        <button type="submit" class="btn btn-primary">Import qilish</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- ===== O'QUVCHI QO'SHISH MODAL ===== --}}
    <div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('students.store', $guruh->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title mb-0">O'quvchi qo'shish</h5>
                            <div class="text-muted small">Guruh: {{ $guruh->title }}</div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ $student->name }}</div>
                                <div class="text-muted small">{{ $guruh->title }}</div>

                                @if ($info)
                                    <span class="st-badge" title="O'rtacha baho">
                                        <span class="gb {{ $cls }}">{{ $info['avg'] }}</span>
                                        {{ $info['count'] }} ta baho
                                    </span>
                                @else
                                    <span class="st-badge none"><i class="bi bi-dash-circle"></i> Baholanmagan</span>
                                @endif
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Ism familiya</label>
                            <input type="text" name="name" class="form-control" placeholder="To'liq ismi"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rasm</label>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Bekor qilish</button>
                        <button type="submit" class="btn btn-primary">Qo'shish</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===== O'QUVCHINI TAHRIRLASH MODAL (BITTA, UMUMIY) ===== --}}
    <div class="modal fade" id="editStudentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="editStudentForm" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title mb-0">O'quvchini tahrirlash</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Rasm</label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <img src="" id="editPhotoPreview" class="rounded-circle border border-2"
                                    style="width:64px;height:64px;object-fit:cover;">
                                <span class="text-muted small">Joriy rasm</span>
                            </div>
                            <input type="file" name="photo" class="form-control" accept="image/*"
                                onchange="previewEditPhoto(event)">
                            <div class="form-text">Rasmni o'zgartirmoqchi bo'lmasangiz, bo'sh qoldiring.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ism familiya</label>
                            <input type="text" name="name" id="editStudentName" class="form-control"
                                placeholder="To'liq ismi" required>
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

    {{-- ===== STUDENT BAHOLARI MODAL ===== --}}
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
        document.addEventListener('DOMContentLoaded', function() {
            // Har qanday modal yopilishidan oldin fokusni olib tashlash
            document.querySelectorAll('.modal').forEach(function(modalEl) {
                modalEl.addEventListener('hide.bs.modal', function() {
                    if (document.activeElement) {
                        document.activeElement.blur();
                    }
                });
            });

            const editModal = document.getElementById('editStudentModal');

            if (editModal) {
                editModal.addEventListener('show.bs.modal', function(event) {
                    const btn = event.relatedTarget;
                    const studentId = btn.getAttribute('data-student-id');
                    const studentName = btn.getAttribute('data-student-name');
                    const studentPhoto = btn.getAttribute('data-student-photo');

                    const form = document.getElementById('editStudentForm');
                    form.action = "{{ url('students/update') }}/" + studentId;

                    document.getElementById('editStudentName').value = studentName;
                    document.getElementById('editPhotoPreview').src = studentPhoto;
                });
            }
        });

        function previewEditPhoto(event) {
            const preview = document.getElementById('editPhotoPreview');
            const file = event.target.files[0];
            if (file) {
                preview.src = URL.createObjectURL(file);
            }
        }
    </script>

    <script>
        const gradeData = @json($gradeData);

        function esc(s) {
            const d = document.createElement('div');
            d.textContent = s ?? '';
            return d.innerHTML;
        }

        function gClass(v) {
            return 'g-' + Math.max(2, Math.min(5, Math.round(v)));
        }

        document.addEventListener('DOMContentLoaded', function() {
            const modalEl = document.getElementById('studentGradesModal');
            const body = document.getElementById('sgBody');

            document.querySelectorAll('.student-card').forEach(card => {
                card.addEventListener('click', function(e) {
                    // tahrirlash / o'chirish tugmalari bosilsa, oyna ochilmasin
                    if (e.target.closest('button, form, a')) return;

                    const id = card.dataset.studentId;
                    const info = gradeData[id];

                    let html = `
                    <div class="sg-top">
                        <img src="${esc(card.dataset.studentPhoto)}" alt="">
                        <div><h5>${esc(card.dataset.studentName)}</h5>
                             <small class="text-muted">${info ? info.count + ' ta baho' : 'Baho yo\'q'}</small></div>
                        ${info ? `<div class="sg-total"><small>Umumiy o'rtacha</small>
                                                      <span class="gb ${gClass(info.avg)}">${info.avg}</span></div>` : ''}
                    </div>`;

                    if (!info) {
                        html += `<p class="text-muted mb-0">Bu talabaga hali baho qo'yilmagan.</p>`;
                    } else {
                        info.fans.forEach(f => {
                            html += `<div class="sg-fan">
                            <div class="sg-fan-head">
                                <div><b>${esc(f.fan)}</b> <small>· ${f.count} ta baho</small></div>
                                <span class="gb ${gClass(f.avg)}" title="Fan bo'yicha o'rtacha">${f.avg}</span>
                            </div>
                            <div class="sg-list">
                                ${f.grades.map(g => `
                                                        <span class="sg-item" title="${esc(g.dars)}">
                                                            <span class="gb ${gClass(g.baho)}">${g.baho}</span>
                                                            ${esc(g.dars)}${g.date ? ' · ' + esc(g.date) : ''}
                                                        </span>`).join('')}
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
