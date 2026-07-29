<?php

namespace App\Services;

use Google\Service\YouTube\Video as YoutubeVideo;
use Google\Service\YouTube\VideoSnippet;
use Google\Service\YouTube\VideoStatus;
use Google\Service\YouTube as GoogleYouTube;
use Google\Client as GoogleClient;
use Illuminate\Http\UploadedFile;
use RuntimeException;

class YoutubeUploadService
{
    protected GoogleClient $client;
    protected GoogleYouTube $youtube;

    public function __construct()
    {
        $this->client = new GoogleClient();
        $this->client->setClientId(config('services.youtube.client_id'));
        $this->client->setClientSecret(config('services.youtube.client_secret'));
        $this->client->setRedirectUri(config('services.youtube.redirect_uri'));
        $this->client->addScope('https://www.googleapis.com/auth/youtube.upload');

        $refreshToken = config('services.youtube.refresh_token');

        if (!$refreshToken) {
            throw new RuntimeException("YOUTUBE_REFRESH_TOKEN .env faylida topilmadi. Avval /youtube/authorize orqali avtorizatsiyadan o'ting.");
        }

        // Refresh token orqali yangi access token olamiz (u har safar
        // ~1 soatdan keyin eskiradi, shuning uchun avtomatik yangilanadi)
        $this->client->refreshToken($refreshToken);

        $this->client->setDefer(true);  // katta fayllarni resumable upload qilish uchun shart
        $this->youtube = new GoogleYouTube($this->client);
    }

    /**
     * Video faylni "Unlisted" holatda YouTube'ga yuklaydi va
     * video ID'sini qaytaradi (buni bazangizda saqlaysiz).
     */
    public function upload(UploadedFile $file, string $title, string $description = ''): string
    {
        $snippet = new VideoSnippet();
        $snippet->setTitle($title);
        $snippet->setDescription($description);
        $snippet->setCategoryId('27');  // 27 = "Education" toifasi

        $status = new VideoStatus();
        $status->setPrivacyStatus('unlisted');  // ASOSIY QATOR — shu tufayli havola bilan ko'radi
        $status->setEmbeddable(true);  // saytga kichik oynachada joylash uchun shart

        $video = new YoutubeVideo();
        $video->setSnippet($snippet);
        $video->setStatus($status);

        // Resumable upload boshlanadi (setDefer(true) tufayli
        // haqiqiy so'rov emas, PHP_Service_Request qaytadi)
        $insertRequest = $this->youtube->videos->insert('snippet,status', $video);

        $media = new \Google\Http\MediaFileUpload(
            $this->client,
            $insertRequest,
            'video/*',
            null,
            true,
            1024 * 1024 * 5  // 5MB'lik qismlarga bo'lib yuklaydi (katta fayllar uchun barqaror)
        );
        $media->setFileSize($file->getSize());

        $status = false;
        $handle = fopen($file->getRealPath(), 'rb');

        while (!$status && !feof($handle)) {
            $chunk = fread($handle, 1024 * 1024 * 5);
            $status = $media->nextChunk($chunk);
        }

        fclose($handle);
        $this->client->setDefer(false);

        // $status — endi to'liq Video resource, ID shu yerdan olinadi
        return $status['id'];
    }

    /**
     * Ko'rsatish uchun embed havola (privacy-enhanced, youtube-nocookie).
     */
    public function embedUrl(string $videoId): string
    {
        return "https://www.youtube-nocookie.com/embed/{$videoId}";
    }
}
