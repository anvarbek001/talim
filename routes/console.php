<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Shared hostinglarda doimiy ishlaydigan `queue:work` jarayonini ushlab turish
// imkoni bo'lmaydi (masalan aHost'da). Shuning uchun navbatni doimiy worker
// o'rniga har daqiqada ishga tushadigan scheduler orqali qayta-qayta bo'shatib
// turamiz — guruh taklifnomalari (GroupInviteNotification) va YouTube'ga video
// yuklash (UploadLessonVideoToYoutube) shu tarzda ishlaydi. Buning uchun
// hosting cPanel'da FAQAT bitta cron kerak:
//   * * * * * php /home/.../artisan schedule:run >> /dev/null 2>&1
Schedule::command('queue:work --stop-when-empty --max-time=55')
    ->everyMinute()
    ->withoutOverlapping();
