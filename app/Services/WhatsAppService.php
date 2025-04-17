<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WhatsAppService
{
    protected $phoneId;
    protected $token;
    protected $appId;
    protected $appSecret;
    protected $shortLivedToken;

    public function __construct()
    {
        $this->phoneId = env('WHATSAPP_PHONE_NUMBER_ID');
        $this->shortLivedToken = env('WHATSAPP_SHORT_LIVED_TOKEN');
        $this->appId = env('FACEBOOK_APP_ID');
        $this->appSecret = env('FACEBOOK_APP_SECRET');

        // Get the current access token (from cache or fallback to .env)
        $this->token = Cache::get('whatsapp_access_token');
    }

    public function sendMessage($to, $message)
    {
        // Optionally check token expiry before sending
        $this->refreshTokenIfNeeded();

        $url = "https://graph.facebook.com/v19.0/{$this->phoneId}/messages";

        $response = Http::withToken($this->token)->post($url, [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'text',
            'text' => [
                'body' => $message,
            ],
        ]);

        return $response->json();
    }

    public function refreshAccessToken()
    {
        $response = Http::get('https://graph.facebook.com/v19.0/oauth/access_token', [
            'grant_type' => 'fb_exchange_token',
            'client_id' => $this->appId,
            'client_secret' => $this->appSecret,
            'fb_exchange_token' => $this->shortLivedToken,
        ]);

        if ($response->successful()) {
            $data = $response->json();

            // Save token in cache and set expiry
            Cache::put('whatsapp_access_token', $data['access_token'], now()->addSeconds($data['expires_in']));

            $this->token = $data['access_token'];

            return $data;
        }

        return $response->json();
    }

    protected function refreshTokenIfNeeded()
    {
        // Optionally add logic to refresh token based on expiration
        if (!Cache::has('whatsapp_access_token')) {
            $this->refreshAccessToken();
        }
    }
}
