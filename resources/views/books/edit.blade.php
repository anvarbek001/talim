@extends('layouts.teacher')

@section('content')
    <div class="page">
        <div class="page-head">
            <h1>Kitobni tahrirlash</h1>
        </div>

        <form action="{{ route('books.update', $book) }}" method="POST" enctype="multipart/form-data" class="card">
            @csrf
            @method('PUT')

            <div class="field mb-16">
                <label class="field-label">Kitob nomi</label>
                <input type="text" name="title" class="text-control" value="{{ old('title', $book->title) }}" required>
                @error('title')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field mb-16">
                <label class="field-label">Tavsif</label>
                <textarea name="description" class="text-control" rows="4">{{ old('description', $book->description) }}</textarea>
                @error('description')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field mb-16">
                <label class="field-label">Narx (so'm)</label>
                <input type="number" name="price" class="text-control" min="0" value="{{ old('price', $book->price) }}" required>
                @error('price')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field-label mb-8">PDF fayl(lar) — yangilash yoki qo'shish</div>
            <label class="dropzone" id="bookDropzoneEdit" for="bookInputEdit">
                <div class="dropzone-icon"><i class="bi bi-file-earmark-pdf"></i></div>
                <div class="dropzone-text">Fayllarni shu yerga tashlang yoki <span>tanlash uchun bosing</span></div>
                <div class="dropzone-hint">Yangi PDF fayllarni qo'shing yoki mavjud fayllarni belgilang va o'chiring.</div>
                <input type="file" name="book_files[]" id="bookInputEdit" accept="application/pdf" multiple hidden>
            </label>

            <div class="file-list">
                @foreach($book->files as $file)
                    <div class="file-chip">
                        <i class="bi bi-file-earmark-pdf file-icon"></i>
                        <a href="{{ route('books.stream', [$book, $file]) }}" target="_blank" class="file-name">{{ $file->original_name }}</a>
                        <label style="margin-left:10px;font-size:.85rem;color:var(--muted)"><input type="checkbox" name="delete_file_ids[]" value="{{ $file->id }}"> O'chirish</label>
                    </div>
                @endforeach
            </div>

            <div class="form-actions">
                <a href="{{ route('books.mine') }}" class="btn-ghost">Bekor qilish</a>
                <button type="submit" class="btn-primary">Saqlash</button>
            </div>
        </form>
    </div>
@endsection
