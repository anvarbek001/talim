<?php

namespace App\Http\Controllers;

use Google_Client;
use Illuminate\Http\Request;

class YoutubeAuthController extends Controller
{
    public function redirect()
    {
        $client = new Google_Client();
        $client->setClientId(env('YOUTUBE_CLIENT_ID'));
        $client->setClientSecret(env('YOUTUBE_CLIENT_SECRET'));
        $client->setRedirectUri(env('YOUTUBE_REDIRECT_URI'));
        $client->addScope('https://www.googleapis.com/auth/youtube.upload');
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        return redirect($client->createAuthUrl());
    }

    public function callback(Request $request)
    {
        $client = new Google_Client();
        $client->setClientId(env('YOUTUBE_CLIENT_ID'));
        $client->setClientSecret(env('YOUTUBE_CLIENT_SECRET'));
        $client->setRedirectUri(env('YOUTUBE_REDIRECT_URI'));

        $token = $client->fetchAccessTokenWithAuthCode($request->get('code'));

        return $token['refresh_token'] ?? 'Token topilmadi';
    }
}
