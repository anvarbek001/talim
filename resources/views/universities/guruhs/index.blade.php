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

        /* select ochilganda variantlar har doim o'qilishi uchun */
        .form-select option {
            color: #17171D;
            background: #FFFFFF;
        }

        /* dark rejimda "x" yopish tugmasi ko'rinishi uchun */
        [data-theme="dark"] .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        /* dark rejimda "Bekor qilish" (light) tugmasi kontrastini tuzatish */
        [data-theme="dark"] .btn-light {
            background: var(--bg-soft);
            color: var(--text);
            border-color: var(--line);
        }

        [data-theme="dark"] .btn-light:hover {
            background: var(--line);
        }
    </style>

    <div class="page fade-up">
        <div class="page-head">
            <div>
                <h1>Guruhlar</h1>
                <p class="page-sub">Guruhlar va o'quvchilarni qo'shish va nazorat qilish.</p>
            </div>
            <button type="button" class="btn btn-primary rounded-3" data-bs-toggle="modal" data-bs-target="#addGuruhModal">
                <i class="bi bi-plus-lg"></i> Yangi guruh
            </button>
        </div>

        @if ($courses->isEmpty())
            <div class="card text-center py-5">
                <i class="bi bi-mortarboard fs-2 text-muted"></i>
                <p class="mt-3 mb-0 text-muted">Hali birorta kurs mavjud emas.</p>
            </div>
        @else
            {{-- Kurslar bo'yicha tablar --}}
            <ul class="nav nav-pills mb-4 flex-wrap gap-2" id="courseTabs" role="tablist">
                @foreach ($courses as $index => $course)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" data-bs-toggle="pill"
                            data-bs-target="#course-{{ $course->id }}" type="button" role="tab">
                            {{ $course->title }}
                            <span class="badge bg-light text-dark ms-1">
                                {{ $guruhs->where('course_id', $course->id)->count() }}
                            </span>
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content">
                @foreach ($courses as $index => $course)
                    @php $courseGuruhs = $guruhs->where('course_id', $course->id); @endphp
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="course-{{ $course->id }}"
                        role="tabpanel">

                        @if ($courseGuruhs->isEmpty())
                            <div class="card text-center py-5">
                                <i class="bi bi-people fs-2 text-muted"></i>
                                <p class="mt-3 mb-2 text-muted">Bu kursda hali guruh yo'q.</p>
                                <div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#addGuruhModal">
                                        Guruh qo'shish
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="row g-3">
                                @foreach ($courseGuruhs as $guruh)
                                    @php $guruhStudents = $students->where('guruh_id', $guruh->id); @endphp
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card h-100">
                                            <div class="card-body d-flex flex-column">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <div class="fw-bold">{{ $guruh->title }}</div>
                                                        <div class="text-muted small">{{ $course->title }}</div>
                                                    </div>
                                                    <span class="badge bg-primary-subtle text-primary">
                                                        {{ $guruhStudents->count() }} ta
                                                    </span>
                                                </div>

                                                <div class="mb-3">
                                                    @forelse ($guruhStudents->take(5) as $student)
                                                        <img src="{{ $student->photo ? asset('storage/' . $student->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($student->name) }}"
                                                            class="rounded-circle border border-2 border-white"
                                                            style="width:32px;height:32px;object-fit:cover;margin-left:-8px;"
                                                            title="{{ $student->name }}">
                                                    @empty
                                                        <span class="text-muted small">Hali o'quvchi yo'q</span>
                                                    @endforelse
                                                    @if ($guruhStudents->count() > 5)
                                                        <span class="badge bg-light text-dark ms-1">
                                                            +{{ $guruhStudents->count() - 5 }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <div
                                                    class="mt-auto d-flex justify-content-end gap-2 align-items-center pt-2 border-top">
                                                    <a href="{{ route('guruhs.edit', $guruh->id) }}"
                                                        class="btn btn-outline-secondary btn-sm"><i
                                                            class="bi bi-pen"></i></a>
                                                    <form action="{{ route('guruhs.destroy', $guruh->id) }}" method="POST"
                                                        onsubmit="return confirm('Guruhni o\'chirishni davom ettirasizmi?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-secondary btn-sm"><i
                                                                class="bi bi-trash"></i></button>
                                                    </form>
                                                    <a href="{{ route('guruhs.showStudents', $guruh->id) }}"
                                                        class="btn btn-link btn-sm text-decoration-none">
                                                        Ko'rish <i class="bi bi-arrow-right"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ===== GURUH QO'SHISH MODAL ===== --}}
    <div class="modal fade" id="addGuruhModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('guruhs.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Yangi guruh qo'shish</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Kurs</label>
                            <select name="course_id" class="form-select" required>
                                <option value="" disabled selected>Kursni tanlang</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Guruh nomi</label>
                            <input type="text" name="title" class="form-control" placeholder="Masalan: IT-24-1"
                                required>
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
        document.getElementById('addStudentModal').addEventListener('show.bs.modal', function(event) {
            const btn = event.relatedTarget;
            document.getElementById('studentGuruhId').value = btn.getAttribute('data-guruh-id');
            document.getElementById('studentCourseId').value = btn.getAttribute('data-course-id');
            document.getElementById('studentGuruhLabel').textContent = 'Guruh: ' + btn.getAttribute(
                'data-guruh-title');
        });
    </script>
@endpush
