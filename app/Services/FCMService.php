<?php

namespace App\Services;

use App\Exceptions\FCMCannotBeSent;
use App\Models\FCMNotification;
use App\Models\Professional;

class FCMService
{
    private static $client;

    public function __construct()
    {
        self::$client = new \GuzzleHttp\Client();
    }

    public function sendFCM($professional_id, $title, $body)
    {
        try {
            $user = Professional::findOrFail($professional_id);

            if ($user->fcm_token == null) {
                return false;
            }

            if (auth()->user()?->id == $professional_id) {
                return false;
            }

            $serverKey = env('FIREBASE_SERVER_KEY');
            $request = self::$client->request('POST', 'https://fcm.googleapis.com/fcm/send', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'key=' . $serverKey,
                ],
                'body' => json_encode([
                    "to" => $user->fcm_token,
                    "notification" => [
                        "title" => $title,
                        "body" => $body,
                    ],
                ]),
            ]);

            $response = $request->getBody();
            $json = json_decode($response, true);

            if ($json['success'] == 1) {

                FCMNotification::create([
                    'professional_id' => $professional_id,
                    'title' => $title,
                    'body' => $body,
                ]);

                return true;
            } else {
                throw new FCMCannotBeSent('Impossible d\'envoyer la notification');
            };

        } catch (\Throwable$th) {
            report($th);
            throw new FCMCannotBeSent('Impossible d\'envoyer la notification');
        }
    }
}
