@extends('layouts.admin')

@section('content')
    <div class="page">

        <div class="page-head fade-up">
            <div>
                <h1>Video darslar</h1>
                <p class="page-sub">Barcha o'qituvchilar tomonidan joylangan video darslarni tahrirlash yoki o'chirish.</p>
            </div>
            <a href="{{ route('lesson') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Dars qo'shish</a>
        </div>

        @if (session('success'))
            <div class="empty-hint" style="background:var(--mint-soft);color:var(--mint);border-color:var(--mint);margin-bottom:16px;">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <form method="GET" action="{{ route('admin.lessons.index') }}" class="filter-bar fade-up">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Dars nomi bo'yicha qidirish..." class="filter-input">
            <select name="teacher" class="filter-select" onchange="this.form.submit()">
                <option value="">Barcha o'qituvchilar</option>
                @foreach ($teachers as $teacher)
                    <option value="{{ $teacher->id }}" {{ (string) request('teacher') === (string) $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                @endforeach
            </select>
            <select name="science" class="filter-select" onchange="this.form.submit()">
                <option value="">Barcha fanlar</option>
                @foreach ($sciences as $science)
                    <option value="{{ $science->id }}" {{ (string) request('science') === (string) $science->id ? 'selected' : '' }}>{{ $science->title }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-outline"><i class="bi bi-search"></i> Qidirish</button>
            @if (request('q') || request('teacher') || request('science'))
                <a href="{{ route('admin.lessons.index') }}" class="filter-reset">Tozalash</a>
            @endif
        </form>

        <div class="card fade-up" style="animation-delay:.05s;">
            @if ($lessons->isEmpty())
                <div class="empty-hint"><i class="bi bi-info-circle"></i> Hech qanday video dars topilmadi.</div>
            @else
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nomi</th>
                                <th>O'qituvchi</th>
                                <th>Fan / Sinf</th>
                                <th>Fayllar</th>
                                <th>Joylangan</th>
                                <th>Amallar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lessons as $lesson)
                                <tr>
                                    <td class="cell-strong" data-label="Nomi">{{ $lesson->title }}</td>
                                    <td data-label="O'qituvchi">{{ $lesson->user->name }}</td>
                                    <td class="cell-muted" data-label="Fan">{{ $lesson->science?->title }} / {{ $lesson->grade?->title }}</td>
                                    <td data-label="Fayllar">{{ $lesson->lessonfiles->count() }} ta</td>
                                    <td class="cell-muted" data-label="Sana">{{ $lesson->created_at->format('d.m.Y') }}</td>
                                    <td class="actions-cell">
                                        <button type="button" class="btn btn-outline btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editLessonModal" data-id="{{ $lesson->id }}"
                                            data-title="{{ $lesson->title }}" data-description="{{ $lesson->description }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('admin.lessons.destroy', $lesson) }}" method="POST"
                                            onsubmit="return confirm('Rostdan ham o\'chirmoqchimisiz? Barcha fayllar ham o\'chib ketadi.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $lessons->links() }}
            @endif
        </div>
    </div>

    <div class="modal fade" id="editLessonModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editLessonForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Video darsni tahrirlash</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div style="margin-bottom:12px;">
                            <label class="form-label">Nomi</label>
                            <input type="text" name="title" id="lesson_title" class="form-control" required>
                        </div>
                        <div>
                            <label class="form-label">Tavsif</label>
                            <textarea name="description" id="lesson_description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Bekor qilish</button>
                        <button type="submit" class="btn btn-primary">Yangilash</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('editLessonModal').addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            const form = document.getElementById('editLessonForm');
            form.action = '{{ url('admin/lessons') }}/' + btn.dataset.id;
            document.getElementById('lesson_title').value = btn.dataset.title;
            document.getElementById('lesson_description').value = btn.dataset.description ?? '';
        });
    </script>
@endsection
