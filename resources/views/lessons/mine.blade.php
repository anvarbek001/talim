@extends('layouts.teacher')

@section('content')
    <div class="page">

        <div class="page-head fade-up mine-head">
            <div>
                <h1>Mening darslarim</h1>
                <p class="page-sub">Joylagan video darslaringiz va ularga biriktirilgan kitob/qo'llanmalar shu yerda
                    to'planadi.</p>
            </div>
            <a href="{{ route('lesson') }}" class="btn-primary mine-add-btn">
                <i class="bi bi-cloud-upload"></i> Yangi dars joylash
            </a>
        </div>

        @if (session('success'))
            <div class="mine-alert fade-up">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        <form method="GET" action="{{ route('lessons.mine') }}" class="mine-filter-bar fade-up">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Dars nomi bo'yicha qidirish..." class="mine-filter-input">
            <button type="submit" class="btn-primary mine-filter-btn"><i class="bi bi-search"></i> Qidirish</button>
            @if (request('q'))
                <a href="{{ route('lessons.mine') }}" class="mine-filter-reset">Tozalash</a>
            @endif
        </form>

        @if ($lessons->isEmpty())
            <div class="mine-empty fade-up">
                <div class="mine-empty-icon"><i class="bi bi-camera-reels"></i></div>
                @if (request('q'))
                    <div class="mine-empty-title">"{{ request('q') }}" bo'yicha dars topilmadi</div>
                @else
                    <div class="mine-empty-title">Hali dars joylanmagan</div>
                    <div class="mine-empty-sub">Birinchi video darsingizni joylab, shogirdlaringizga yetkazing.</div>
                    <a href="{{ route('lesson') }}" class="btn-primary">
                        <i class="bi bi-cloud-upload"></i> Dars joylash
                    </a>
                @endif
            </div>
        @else
            <div class="mine-grid">
                @foreach ($lessons as $index => $lesson)
                    @php
                        $videoFile = $lesson->lessonfiles->first(fn($f) => $f->isVideo() && $f->embedUrl());
                        $pendingVideo = $lesson->lessonfiles->first(fn($f) => $f->isVideo() && $f->isPending());
                        $failedVideo = $lesson->lessonfiles->first(fn($f) => $f->isVideo() && $f->isFailed());
                        $bookFiles = $lesson->lessonfiles->filter(fn($f) => !$f->isVideo());
                        $accent = $lesson->science->color ?? '#6C5CE7';
                    @endphp
                    <div class="lesson-card fade-up" style="animation-delay:{{ min($index * 0.06, 0.3) }}s;">
                        <div class="lesson-video">
                            @if ($videoFile)
                                <iframe src="{{ $videoFile->embedUrl() }}" loading="lazy" allowfullscreen
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    referrerpolicy="strict-origin-when-cross-origin"></iframe>
                            @elseif ($pendingVideo)
                                <div class="lesson-video-placeholder" style="background:linear-gradient(135deg,{{ $accent }},#9C8CFF);">
                                    <i class="bi bi-arrow-repeat"></i>
                                    <span>Video YouTube'ga yuklanmoqda...</span>
                                </div>
                            @elseif ($failedVideo)
                                <div class="lesson-video-placeholder" style="background:linear-gradient(135deg,var(--coral),#FF9B7B);">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    <span>Video yuklashda xatolik yuz berdi</span>
                                </div>
                            @else
                                <div class="lesson-video-placeholder" style="background:linear-gradient(135deg,{{ $accent }},#9C8CFF);">
                                    <i class="bi bi-camera-reels-fill"></i>
                                </div>
                            @endif
                        </div>

                        <div class="lesson-card-body">
                            <div class="lesson-card-title">{{ $lesson->title }}</div>

                            <div class="lesson-chip-row">
                                <span class="lesson-chip" style="background:{{ $accent }}1A;color:{{ $accent }};">
                                    <i class="bi bi-book"></i> {{ $lesson->science->title ?? '—' }}
                                </span>
                                <span class="lesson-chip lesson-chip-muted">
                                    <i class="bi bi-mortarboard"></i> {{ $lesson->grade->title ?? '—' }}
                                </span>
                                <span class="lesson-chip lesson-chip-muted">
                                    <i class="bi bi-clock"></i> {{ $lesson->created_at->diffForHumans() }}
                                </span>
                            </div>

                            @if ($lesson->description)
                                <div class="lesson-card-desc">{{ \Illuminate\Support\Str::limit($lesson->description, 110) }}</div>
                            @endif

                            @if ($bookFiles->isNotEmpty())
                                <div class="lesson-files">
                                    @foreach ($bookFiles as $file)
                                        <a href="{{ asset('storage/' . $file->lesson_file) }}" target="_blank"
                                            class="lesson-file-chip">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                            <span>{{ basename($file->lesson_file) }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            {{ $lessons->links() }}
        @endif
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

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 22px;
            border-radius: 10px;
            border: none;
            background: var(--primary);
            color: #fff;
            font-weight: 700;
            font-size: .88rem;
            transition: .2s;
        }

        .btn-primary:hover {
            background: #5A4BD6;
        }

        .mine-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .mine-add-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .mine-alert {
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

        .mine-empty {
            background: var(--card);
            border: 1px dashed var(--line);
            border-radius: 18px;
            padding: 60px 24px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }

        .mine-empty-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: var(--primary-soft);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 8px;
        }

        .mine-empty-title {
            font-weight: 700;
            font-size: 1.05rem;
        }

        .mine-empty-sub {
            color: var(--muted);
            font-size: .86rem;
            margin-bottom: 18px;
            max-width: 360px;
        }

        .mine-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 20px;
        }

        .lesson-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .lesson-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow);
        }

        .lesson-video {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 9;
            background: #000;
        }

        .lesson-video iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        .lesson-video-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #fff;
            font-size: 2rem;
            text-align: center;
            padding: 10px;
        }

        .lesson-video-placeholder span {
            font-size: .78rem;
            font-weight: 600;
        }

        .lesson-card-body {
            padding: 16px 18px 18px;
        }

        .lesson-card-title {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 10px;
            line-height: 1.3;
        }

        .lesson-chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 10px;
        }

        .lesson-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: .72rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            white-space: nowrap;
        }

        .lesson-chip-muted {
            background: var(--bg-soft);
            color: var(--muted);
        }

        .lesson-card-desc {
            font-size: .82rem;
            color: var(--muted);
            line-height: 1.5;
            margin-bottom: 10px;
        }

        .lesson-files {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-top: 8px;
            padding-top: 10px;
            border-top: 1px solid var(--line);
        }

        .lesson-file-chip {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: .8rem;
            font-weight: 600;
            color: var(--text);
            padding: 6px 8px;
            border-radius: 8px;
            transition: .2s;
        }

        .lesson-file-chip:hover {
            background: var(--bg-soft);
            color: var(--primary);
        }

        .lesson-file-chip span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .lesson-file-chip i {
            color: var(--coral);
            flex-shrink: 0;
        }

        @media (max-width:767px) {
            .mine-grid {
                grid-template-columns: 1fr;
            }
        }

        .mine-filter-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .mine-filter-input {
            flex: 1 1 260px;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: .86rem;
            background: var(--card);
            color: var(--text);
        }

        .mine-filter-btn {
            flex-shrink: 0;
        }

        .mine-filter-reset {
            display: inline-flex;
            align-items: center;
            font-size: .84rem;
            font-weight: 600;
            color: var(--muted);
        }

        .mine-filter-reset:hover {
            color: var(--coral);
        }
    </style>
@endsection
