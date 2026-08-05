{{-- Bitta video (yoki bo'sh joy)ni ko'rsatadi — dars kartasidagi asosiy
     pleer, shuningdek JS orqali video ro'yxatidan almashtirilganda ham
     shu razmetka qayta yasaladi (resources/views/lessons/mine.blade.php). --}}
@if ($video && $video->embedUrl())
    <iframe src="{{ $video->embedUrl() }}" loading="lazy" allowfullscreen
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
        referrerpolicy="strict-origin-when-cross-origin"></iframe>
@elseif ($video && $video->isPending())
    <div class="lesson-video-placeholder">
        <i class="bi bi-arrow-repeat"></i>
        <span>Video YouTube'ga yuklanmoqda...</span>
    </div>
@elseif ($video && $video->isFailed())
    <div class="lesson-video-placeholder" style="background:linear-gradient(135deg,var(--coral),#FF9B7B);">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span>Video yuklashda xatolik yuz berdi</span>
    </div>
@else
    <div class="lesson-video-placeholder">
        <i class="bi bi-camera-reels-fill"></i>
    </div>
@endif
