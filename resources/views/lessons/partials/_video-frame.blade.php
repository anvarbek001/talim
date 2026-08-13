{{-- Bitta video (yoki bo'sh joy)ni ko'rsatadi — dars kartasidagi asosiy
     pleer, shuningdek JS orqali video ro'yxatidan almashtirilganda ham
     shu razmetka qayta yasaladi (resources/views/lessons/mine.blade.php). --}}
@if ($video && $video->embedUrl())
    {{-- Havola sahifa manbasida yozilmaydi — JS sahifa yuklangach
    /lesson-files/{id}/embed orqali (ruxsat tekshirilgach) so'raydi. --}}
    <div class="lesson-video-placeholder" data-video-loading data-video-id="{{ $video->id }}">
        <i class="bi bi-arrow-repeat"></i>
    </div>
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
