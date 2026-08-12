<?php

namespace App\Services;

use App\Models\User;
use Firebase\JWT\JWT;

/**
 * LiveKit'ga ulanish uchun kirish tokeni (JWT) yaratadi. LiveKit server SDK
 * o'rniga to'g'ridan-to'g'ri JWT quramiz — loyihada allaqachon mavjud bo'lgan
 * firebase/php-jwt paketidan foydalanib, qo'shimcha bog'liqlik qo'shmaymiz.
 *
 * Token formati: https://docs.livekit.io/home/get-started/authentication/
 */
class LiveKitTokenService
{
    public function isConfigured(): bool
    {
        return filled(config('livekit.url'))
            && filled(config('livekit.api_key'))
            && filled(config('livekit.api_secret'));
    }

    /**
     * @param  bool  $isModerator  O'qituvchi/xona egasi — yozib olishga ruxsat,
     *                             boshqa ishtirokchilarni boshqarish huquqlari.
     */
    public function generateToken(string $roomName, User $user, bool $isModerator = false): string
    {
        $apiKey = config('livekit.api_key');
        $apiSecret = config('livekit.api_secret');
        $ttl = (int) config('livekit.token_ttl', 4 * 60 * 60);
        $now = time();

        $payload = [
            'iss' => $apiKey,
            'sub' => $this->identityFor($user),
            'iat' => $now,
            'nbf' => $now,
            'exp' => $now + $ttl,
            'name' => $user->name,
            'video' => [
                'room' => $roomName,
                'roomJoin' => true,
                // Kim birinchi ulansa ("o'qituvchi hali boshlamagan" holatini oldini
                // olish uchun) LiveKit xonasini avtomatik yaratadi — bitta ilova
                // ichida ishlatilgani uchun bu xavfsizlikka ta'sir qilmaydi, chunki
                // "room" claim aynan shu darsning nomiga qattiq bog'langan.
                'roomCreate' => true,
                'canPublish' => true,
                'canSubscribe' => true,
                'canPublishData' => true,
                'roomRecord' => $isModerator,
                'roomAdmin' => $isModerator,
            ],
        ];

        return JWT::encode($payload, $apiSecret, 'HS256');
    }

    public function identityFor(User $user): string
    {
        return 'user-'.$user->id;
    }
}
