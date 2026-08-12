@extends('layouts.teacher')

@section('content')
    <div class="page">
        <div class="page-head fade-up">
            <div>
                <h1>Yangi guruh yaratish</h1>
                <p class="page-sub">Guruh yaratgach, o'quvchilarni taklif havolasi yoki email orqali qo'sha
                    olasiz.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('groups.store') }}" class="form-card fade-up">
            @csrf

            <div class="form-group">
                <label for="name">Guruh nomi</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Masalan: Matematika — 9-sinf, kechki guruh" required>
                @error('name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="science_id">Fan (ixtiyoriy)</label>
                <select id="science_id" name="science_id">
                    <option value="">— Tanlanmagan —</option>
                    @foreach ($sciences as $science)
                        <option value="{{ $science->id }}" @selected(old('science_id') == $science->id)>{{ $science->title }}</option>
                    @endforeach
                </select>
                @error('science_id')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Tavsif (ixtiyoriy)</label>
                <textarea id="description" name="description" rows="3" placeholder="Guruh haqida qisqacha...">{{ old('description') }}</textarea>
                @error('description')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('groups.index') }}" class="btn-secondary">Bekor qilish</a>
                <button type="submit" class="btn-primary"><i class="bi bi-check-lg"></i> Guruh yaratish</button>
            </div>
        </form>
    </div>

    <style>
        .form-card {
            max-width: 560px;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 26px;
            box-shadow: var(--shadow-sm);
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: .84rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 6px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: .88rem;
            background: var(--bg);
            color: var(--text);
        }

        .form-error {
            color: var(--coral);
            font-size: .78rem;
            margin-top: 4px;
            font-weight: 600;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 22px;
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 22px;
            border-radius: 10px;
            border: 1px solid var(--line);
            background: var(--card);
            color: var(--text);
            font-weight: 700;
            font-size: .88rem;
        }

        .btn-secondary:hover {
            background: var(--bg-soft);
        }
    </style>
@endsection
