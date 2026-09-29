@extends('layouts.university')
@section('body')
    <style>
        .detail-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 24px;
        }

        .detail-head {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 18px;
        }

        .detail-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: var(--primary-soft);
            color: var(--primary);
            display: grid;
            place-items: center;
            font-size: 1.6rem;
        }

        .detail-head h1 {
            margin: 0;
            font-size: 1.5rem;
            color: var(--text);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
            margin-top: 18px;
        }

        .info-item {
            background: var(--bg-soft);
            border: 1px dashed var(--line);
            border-radius: 12px;
            padding: 12px 14px;
        }

        .info-item small {
            display: block;
            color: var(--muted);
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .info-item b {
            color: var(--text);
        }

        .detail-desc {
            color: var(--muted);
            margin: 0;
        }
    </style>

    <div class="page fade-up">
        <div class="page-head">
            <div>
                <h1>Fan haqida</h1>
                <p class="page-sub">Fan va biriktirilgan guruh ma'lumotlari.</p>
            </div>
            <a href="{{ url()->previous() }}" class="btn btn-light rounded-3">
                <i class="bi bi-arrow-left"></i> Orqaga
            </a>
        </div>

        <div class="detail-card">
            <div class="detail-head">
                <div class="detail-icon"><i class="bi bi-book"></i></div>
                <h1>{{ $fan->title }}</h1>
            </div>

            <p class="detail-desc">{{ $fan->desc ?: 'Izoh kiritilmagan' }}</p>

            <div class="info-grid">
                <div class="info-item">
                    <small>Guruh</small>
                    <b>{{ $fan->guruh->title ?? '—' }}</b>
                </div>
                <div class="info-item">
                    <small>Kurs</small>
                    <b>{{ $fan->guruh->course->title ?? '—' }}</b>
                </div>
                <div class="info-item">
                    <small>Qo'shgan</small>
                    <b>{{ $fan->user->name ?? '—' }}</b>
                </div>
                <div class="info-item">
                    <small>Qo'shilgan sana</small>
                    <b>{{ $fan->created_at->format('d.m.Y') }}</b>
                </div>
            </div>
        </div>
    </div>
@endsection
