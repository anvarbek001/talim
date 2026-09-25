@extends('layouts.university')
@section('body')
    <style>
        .card-form {
            background: var(--card);
            color: var(--text);
            border: 1px solid var(--line);
            border-radius: 16px;
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

        .form-select option {
            color: #17171D;
            background: #FFFFFF;
        }
    </style>

    <div class="page fade-up">
        <div class="page-head">
            <div>
                <h1>Guruhni tahrirlash</h1>
                <p class="page-sub">Guruhlar tahrirlash.</p>
            </div>
        </div>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card card-form p-4" style="max-width: 520px;">
            <form action="{{ route('guruhs.update', $guruh->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Kurs</label>
                    <select name="course_id" class="form-select" required>
                        <option value="" disabled>Kursni tanlang</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}" {{ $course->id == $guruh->course_id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('course_id')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">Guruh nomi</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $guruh->title) }}"
                        placeholder="Masalan: IT-24-1" required>
                    @error('title')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('guruhs.index') }}" class="btn btn-light">Bekor qilish</a>
                    <button type="submit" class="btn btn-primary">Saqlash</button>
                </div>
            </form>
        </div>
    </div>
@endsection
