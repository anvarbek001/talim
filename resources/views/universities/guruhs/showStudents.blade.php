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
    </style>

    <div class="page fade-up">
        <div class="page-head">
            <div>
                <h1>{{ $guruh->title }}</h1>
                <p class="page-sub">{{ $guruh->course->title ?? '' }} — o'quvchilar ro'yxati</p>
                <p>Jami o'quvchilar: {{ $students->count() }} ta</p>
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
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100">
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
@endpush
