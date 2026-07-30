@php
    $videoFile = $lesson->lessonfiles->first(fn ($f) => $f->isVideo() && $f->embedUrl());
    $accent = $lesson->science->color ?? '#6C5CE7';
    $isSaved = in_array($lesson->id, $savedIds ?? [], true);
@endphp

<div class="lesson-card fade-up" style="animation-delay:{{ min(($index ?? 0) * 0.05, 0.3) }}s;">
    <a href="{{ route('student-lessons.show', $lesson) }}" class="lesson-video">
        @if ($videoFile)
            <img src="https://img.youtube.com/vi/{{ $videoFile->youtube_id }}/hqdefault.jpg" alt="{{ $lesson->title }}" loading="lazy">
            <span class="lesson-play"><i class="bi bi-play-fill"></i></span>
        @else
            <div class="lesson-video-placeholder" style="background:linear-gradient(135deg,{{ $accent }},#9C8CFF);">
                <i class="bi bi-camera-reels-fill"></i>
            </div>
        @endif
        @if (! $lesson->isFreePreview())
            <span class="lesson-lock"><i class="bi bi-lock-fill"></i></span>
        @endif
    </a>

    <div class="lesson-card-body">
        <div class="lesson-card-head">
            <a href="{{ route('student-lessons.show', $lesson) }}" class="lesson-card-title">{{ $lesson->title }}</a>
            <form action="{{ route('student-lessons.save', $lesson) }}" method="POST">
                @csrf
                <button type="submit" class="save-btn {{ $isSaved ? 'is-saved' : '' }}" title="{{ $isSaved ? 'Saqlanganlardan olib tashlash' : 'Saqlab qo\'yish' }}">
                    <i class="bi {{ $isSaved ? 'bi-bookmark-heart-fill' : 'bi-bookmark' }}"></i>
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
        </div>

        @if ($lesson->description)
            <div class="lesson-card-desc">{{ \Illuminate\Support\Str::limit($lesson->description, 90) }}</div>
        @endif

        <div class="lesson-card-foot">
            <span><i class="bi bi-person"></i> {{ $lesson->user->name ?? "O'qituvchi" }}</span>
            <span>{{ $lesson->created_at->diffForHumans() }}</span>
        </div>
    </div>
</div>
