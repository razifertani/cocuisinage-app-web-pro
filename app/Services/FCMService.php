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

    public function sendFCM($establishment_id, $sender_id, $receiver_id, $notification_type_id, $title, $body)
    {
        try {
            $sender = Professional::findOrFail($sender_id);
            $receiver = Professional::findOrFail($receiver_id);

            if ($receiver->fcm_token == null) {
                return false;
            }

            if ($sender_id == $receiver_id) {
                return false;
            }
            if ($sender->fcm_token == $receiver->fcm_token) {
                return false;
            }

            $fcm_type_activate = $receiver->notifications_params()->where('notifications_types.id', $notification_type_id)->wherePivot('establishment_id', $establishment_id)->first()->pivot->active;
            if (!$fcm_type_activate) {
                return false;
            }

            $serverKey = env('FIREBASE_SERVER_KEY');
            $request = self::$client->request('POST', 'https://fcm.googleapis.com/fcm/send', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'key=' . $serverKey,
                ],
                'body' => json_encode([
                    'to' => $receiver->fcm_token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    // 'data' => [
                    //     'Nick' => 'Mario',
                    //     'Room' => 'PortugalVSDenmark',
                    // ],
                ]),
            ]);

            $response = $request->getBody();
            $json = json_decode($response, true);

            if ($json['success'] == 1) {

                FCMNotification::create([
                    'sender_id' => $sender_id,
                    'receiver_id' => $receiver_id,
                    'notification_type_id' => $notification_type_id,
                    'title' => $title,
                    'establishment_id' => $establishment_id,
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
