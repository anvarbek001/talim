@extends('layouts.teacher')

@section('content')
    <div class="page">
        <div class="page-head fade-up">
            <h1>Yozma javoblarni baholash</h1>
            <p class="page-sub">Sertifikat testlaringizdagi yozma savollarga o'quvchilar yuborgan javoblarni shu yerdan
                baholang.</p>
        </div>

        @if (session('success'))
            <div class="alert-success fade-up"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert-error fade-up"><i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}</div>
        @endif

        <div class="card fade-up" style="animation-delay:.06s;">
            <div class="card-head">
                <div class="card-title">Baholanishi kerak ({{ $pending->count() }})</div>
            </div>

            @if ($pending->isEmpty())
                <div class="empty-hint">
                    <i class="bi bi-info-circle"></i>
                    Hozircha baholanishi kerak bo'lgan yozma javob yo'q.
                </div>
            @else
                @foreach ($pending as $answer)
                    <div class="grade-block">
                        <div class="grade-meta">
                            <span class="grade-chip"><i class="bi bi-mortarboard"></i> {{ $answer->attempt->user->name ?? "O'quvchi" }}</span>
                            <span class="grade-chip grade-chip-muted"><i class="bi bi-patch-check"></i> {{ $answer->questionable->sertifikatTest->title ?? '—' }}</span>
                        </div>
                        <div class="grade-question">{{ $answer->questionable->question }}</div>
                        <div class="grade-answer">{{ $answer->answer_text ?: 'Javob berilmagan' }}</div>
                        <form action="{{ route('tests.grading.store', $answer) }}" method="POST" class="grade-form">
                            @csrf
                            <label class="grade-label">Ball (maks. {{ $answer->max_score }})</label>
                            <input type="number" name="score" class="text-control grade-score-input" min="0"
                                max="{{ $answer->max_score }}" required>
                            <button type="submit" class="btn-primary">
                                <i class="bi bi-check-circle"></i> Saqlash
                            </button>
                        </form>
                    </div>
                @endforeach
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

        .alert-success {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--mint-soft);
            color: var(--mint);
            border-radius: 12px;
            padding: 13px 16px;
            font-weight: 600;
            font-size: .88rem;
            margin-bottom: 20px;
        }

        .alert-error {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--coral-soft);
            color: var(--coral);
            border-radius: 12px;
            padding: 13px 16px;
            font-weight: 600;
            font-size: .88rem;
            margin-bottom: 20px;
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

        .grade-block {
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 14px;
        }

        .grade-block:last-child {
            margin-bottom: 0;
        }

        .grade-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 10px;
        }

        .grade-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: .72rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            background: var(--primary-soft);
            color: var(--primary);
        }

        .grade-chip-muted {
            background: var(--bg-soft);
            color: var(--muted);
        }

        .grade-question {
            font-weight: 700;
            font-size: .92rem;
            margin-bottom: 8px;
        }

        .grade-answer {
            background: var(--bg-soft);
            border-radius: 10px;
            padding: 10px 12px;
            font-size: .86rem;
            white-space: pre-wrap;
            margin-bottom: 12px;
        }

        .grade-form {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            flex-wrap: wrap;
        }

        .grade-label {
            font-size: .78rem;
            font-weight: 600;
            color: var(--muted);
            display: block;
            margin-bottom: 6px;
        }

        .grade-score-input {
            width: 100px;
        }

        .text-control {
            background: var(--bg-soft);
            border: 1.5px solid var(--line);
            border-radius: 10px;
            padding: 10px 12px;
            font-family: 'Inter', sans-serif;
            font-size: .88rem;
            color: var(--text);
        }

        .text-control:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--card);
            box-shadow: 0 0 0 3px var(--primary-soft);
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            background: var(--primary);
            color: #fff;
            font-weight: 700;
            font-size: .86rem;
        }

        .btn-primary:hover {
            background: #5A4BD6;
        }
    </style>
@endsection
