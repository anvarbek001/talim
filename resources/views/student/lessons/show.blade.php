@extends('layouts.student')

@php
    $videoFile = $lesson->lessonfiles->first(fn ($f) => $f->isVideo() && $f->embedUrl());
    $bookFiles = $lesson->lessonfiles->filter(fn ($f) => ! $f->isVideo());
    $accent = $lesson->science->color ?? '#6C5CE7';
@endphp

@section('content')
    <div class="page">
        <a href="{{ route('student-lessons.index') }}" class="back-link fade-up">
            <i class="bi bi-arrow-left"></i> Darslarga qaytish
        </a>

        <div class="watch-grid">
            <div>
                <div class="card fade-up watch-card" style="animation-delay:.04s;">
                    <div class="watch-video">
                        @if ($videoFile)
                            <iframe src="{{ $videoFile->embedUrl() }}" loading="lazy" allowfullscreen
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                referrerpolicy="strict-origin-when-cross-origin"></iframe>
                        @else
                            <div class="watch-video-placeholder" style="background:linear-gradient(135deg,{{ $accent }},#9C8CFF);">
                                <i class="bi bi-camera-reels-fill"></i>
                                <span>Bu darsda video mavjud emas</span>
                            </div>
                        @endif
                    </div>

                    <div class="watch-body">
                        <div class="watch-head">
                            <h1>{{ $lesson->title }}</h1>
                            <form action="{{ route('student-lessons.save', $lesson) }}" method="POST">
                                @csrf
                                <button type="submit" class="save-btn-lg {{ $isSaved ? 'is-saved' : '' }}">
                                    <i class="bi {{ $isSaved ? 'bi-bookmark-heart-fill' : 'bi-bookmark' }}"></i>
                                    {{ $isSaved ? 'Saqlangan' : "Saqlab qo'yish" }}
                                </button>
                            </form>
                        </div>

                        <div class="lesson-chip-row">
                            <span class="lesson-chip" style="background:{{ $accent }}1A;color:{{ $accent }};">
                                <i class="bi bi-book"></i> {{ $lesson->science->title ?? '—' }}
                            </span>
                            <span class="lesson-chip lesson-chip-muted">
                                <i class="bi bi-mortarboard"></i> {{ $lesson->grade->title ?? '—' }}
                            </span>
                            <span class="lesson-chip lesson-chip-muted">
                                <i class="bi bi-collection"></i> {{ $lesson->section->title ?? '—' }}
                            </span>
                            <span class="lesson-chip lesson-chip-muted">
                                <i class="bi bi-person"></i> {{ $lesson->user->name ?? "O'qituvchi" }}
                            </span>
                        </div>

                        @if ($lesson->description)
                            <p class="watch-desc">{{ $lesson->description }}</p>
                        @endif

                        @if ($bookFiles->isNotEmpty())
                            <div class="watch-files">
                                <div class="watch-files-title">Qo'shimcha materiallar</div>
                                @foreach ($bookFiles as $file)
                                    <a href="{{ asset('storage/'.$file->lesson_file) }}" target="_blank" class="lesson-file-chip">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                        <span>{{ basename($file->lesson_file) }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div>
                <div class="card fade-up" style="animation-delay:.1s;">
                    <div class="card-head">
                        <div class="card-title">Shu mavzudagi boshqa darslar</div>
                    </div>
                    @forelse ($related as $item)
                        <a href="{{ route('student-lessons.show', $item) }}" class="related-row">
                            <div class="related-thumb" style="background:linear-gradient(135deg,{{ $item->science->color ?? '#6C5CE7' }},#9C8CFF);">
                                <i class="bi bi-play-fill"></i>
                            </div>
                            <div>
                                <div class="related-title">{{ $item->title }}</div>
                                <div class="related-sub">{{ $item->science->title ?? '—' }}</div>
                            </div>
                        </a>
                    @empty
                        <div class="related-empty">Shu mavzuda boshqa dars yo'q.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <style>
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            font-weight: 600;
            font-size: .85rem;
            margin-bottom: 16px;
        }

        .back-link:hover {
            color: var(--primary);
        }

        .watch-grid {
            display: grid;
            grid-template-columns: 1.7fr 1fr;
            gap: 18px;
            align-items: start;
        }

        @media (max-width:1100px) {
            .watch-grid {
                grid-template-columns: 1fr;
            }
        }

        .watch-card {
            padding: 0;
            overflow: hidden;
        }

        .watch-video {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 9;
            background: #000;
        }

        .watch-video iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        .watch-video-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: #fff;
            font-size: 2rem;
        }

        .watch-video-placeholder span {
            font-size: .85rem;
            font-weight: 600;
        }

        .watch-body {
            padding: 20px 22px 22px;
        }

        .watch-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 12px;
        }

        .watch-head h1 {
            font-size: 1.3rem;
            margin: 0;
            line-height: 1.35;
        }

        .save-btn-lg {
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 10px;
            border: 1.5px solid var(--line);
            background: var(--bg-soft);
            color: var(--muted);
            font-weight: 700;
            font-size: .84rem;
            transition: .2s;
            white-space: nowrap;
        }

        .save-btn-lg:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .save-btn-lg.is-saved {
            background: var(--primary-soft);
            color: var(--primary);
            border-color: transparent;
        }

        .lesson-chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 14px;
        }

        .lesson-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: .74rem;
            font-weight: 700;
            padding: 5px 11px;
            border-radius: 20px;
            white-space: nowrap;
        }

        .lesson-chip-muted {
            background: var(--bg-soft);
            color: var(--muted);
        }

        .watch-desc {
            font-size: .88rem;
            color: var(--text);
            line-height: 1.6;
            margin: 0 0 14px;
        }

        .watch-files {
            padding-top: 14px;
            border-top: 1px solid var(--line);
        }

        .watch-files-title {
            font-size: .78rem;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 8px;
        }

        .lesson-file-chip {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: .84rem;
            font-weight: 600;
            color: var(--text);
            padding: 8px;
            border-radius: 8px;
            transition: .2s;
        }

        .lesson-file-chip:hover {
            background: var(--bg-soft);
            color: var(--primary);
        }

        .lesson-file-chip i {
            color: var(--coral);
        }

        .related-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid var(--line);
        }

        .related-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .related-thumb {
            width: 52px;
            height: 42px;
            border-radius: 10px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .related-title {
            font-size: .84rem;
            font-weight: 700;
            line-height: 1.3;
        }

        .related-sub {
            font-size: .72rem;
            color: var(--muted);
            margin-top: 2px;
        }

        .related-empty {
            font-size: .84rem;
            color: var(--muted);
        }
    </style>
@endsection
