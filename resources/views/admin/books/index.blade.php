@extends('layouts.admin')

@section('content')
    <div class="page">

        <div class="page-head fade-up">
            <div>
                <h1>Kitoblar</h1>
                <p class="page-sub">Barcha o'qituvchilar tomonidan joylangan kitoblarni tahrirlash yoki o'chirish.</p>
            </div>
            <a href="{{ route('book') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Kitob qo'shish</a>
        </div>

        @if (session('success'))
            <div class="empty-hint" style="background:var(--mint-soft);color:var(--mint);border-color:var(--mint);margin-bottom:16px;">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <form method="GET" action="{{ route('admin.books.index') }}" class="filter-bar fade-up">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Kitob nomi bo'yicha qidirish..." class="filter-input">
            <select name="teacher" class="filter-select" onchange="this.form.submit()">
                <option value="">Barcha o'qituvchilar</option>
                @foreach ($teachers as $teacher)
                    <option value="{{ $teacher->id }}" {{ (string) request('teacher') === (string) $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-outline"><i class="bi bi-search"></i> Qidirish</button>
            @if (request('q') || request('teacher'))
                <a href="{{ route('admin.books.index') }}" class="filter-reset">Tozalash</a>
            @endif
        </form>

        <div class="card fade-up" style="animation-delay:.05s;">
            @if ($books->isEmpty())
                <div class="empty-hint"><i class="bi bi-info-circle"></i> Hech qanday kitob topilmadi.</div>
            @else
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nomi</th>
                                <th>O'qituvchi</th>
                                <th>Fayllar</th>
                                <th>Narxi</th>
                                <th>Joylangan</th>
                                <th>Amallar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($books as $book)
                                <tr>
                                    <td class="cell-strong" data-label="Nomi">{{ $book->title }}</td>
                                    <td data-label="O'qituvchi">{{ $book->user->name }}</td>
                                    <td data-label="Fayllar">{{ $book->files->count() }} ta</td>
                                    <td data-label="Narxi">{{ number_format($book->price, 0, '.', ' ') }} so'm</td>
                                    <td class="cell-muted" data-label="Sana">{{ $book->created_at->format('d.m.Y') }}</td>
                                    <td class="actions-cell">
                                        <button type="button" class="btn btn-outline btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editBookModal" data-id="{{ $book->id }}"
                                            data-title="{{ $book->title }}" data-description="{{ $book->description }}"
                                            data-price="{{ $book->price }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('admin.books.destroy', $book) }}" method="POST"
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
                {{ $books->links() }}
            @endif
        </div>
    </div>

    <div class="modal fade" id="editBookModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editBookForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Kitobni tahrirlash</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div style="margin-bottom:12px;">
                            <label class="form-label">Nomi</label>
                            <input type="text" name="title" id="book_title" class="form-control" required>
                        </div>
                        <div style="margin-bottom:12px;">
                            <label class="form-label">Tavsif</label>
                            <textarea name="description" id="book_description" class="form-control" rows="3"></textarea>
                        </div>
                        <div>
                            <label class="form-label">Narxi (so'm)</label>
                            <input type="number" name="price" id="book_price" class="form-control" min="0" required>
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
        document.getElementById('editBookModal').addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            const form = document.getElementById('editBookForm');
            form.action = '{{ url('admin/books') }}/' + btn.dataset.id;
            document.getElementById('book_title').value = btn.dataset.title;
            document.getElementById('book_description').value = btn.dataset.description ?? '';
            document.getElementById('book_price').value = btn.dataset.price;
        });
    </script>
@endsection
