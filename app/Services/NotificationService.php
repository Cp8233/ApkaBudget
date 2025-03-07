<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected $firebaseUrl = 'https://fcm.googleapis.com/v1/projects/apka-budget-partner/messages:send';
    protected $accessToken;

    public function __construct()
    {
        $serviceAccount = json_decode(file_get_contents(storage_path('app/apka-budget-partner-firebase-adminsdk-fbsvc-509ccf49ab.json')), true);
        $this->accessToken = $this->getAccessToken($serviceAccount);
    }

    public function sendPushNotification($deviceTokens, $title, $body, $sound = 'default')
    {
        if (empty($deviceTokens)) {
            Log::error('Device tokens are empty.');
            return false;
        }

        $tokens = is_array($deviceTokens) ? $deviceTokens : [$deviceTokens];

        foreach ($tokens as $token) {
            $notificationData = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body
                    ],
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'sound' => $sound
                        ]
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'sound' => $sound
                            ]
                        ]
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json'
            ])->post($this->firebaseUrl, $notificationData);

            if ($response->failed()) {
                Log::error('Failed to send notification: ' . $response->body());
            }
        }

        return true;
    }

    protected function getAccessToken($serviceAccount)
    {
        $jwt = $this->createJwt($serviceAccount);

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        return $response->json()['access_token'] ?? null;
    }

    protected function createJwt($serviceAccount)
    {
        $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $payload = base64_encode(json_encode([
            'iss' => $serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => time() + 3600,
            'iat' => time()
        ]));

        $signature = '';
        openssl_sign("$header.$payload", $signature, openssl_pkey_get_private($serviceAccount['private_key']), 'sha256');
        $signature = base64_encode($signature);

        return "$header.$payload.$signature";
    }
}
