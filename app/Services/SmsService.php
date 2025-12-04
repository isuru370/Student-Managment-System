<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SmsService
{
    protected $authToken;
    protected $defaultSender;

    public function __construct()
    {
        $this->authToken = env('SEND_LK_TOKEN');
        $this->defaultSender = env('SEND_LK_DEFAULT_SENDER', 'Success_Edu');
    }

    public function sendSMS($mobile, $message, $senderId = null)
    {
        try {
            $sender = $senderId ?? $this->defaultSender; 

            $payload = [
                "recipient" => $mobile,
                "sender_id" => $sender,
                "message"   => urldecode($message),
            ];

            $response = Http::withToken($this->authToken)
                ->acceptJson()
                ->withHeaders([
                    "cache-control" => "no-cache",
                    "content-type"  => "application/x-www-form-urlencoded",
                ])
                ->post("https://sms.send.lk/api/v3/sms/send", $payload);

            // If Send.lk API returns error
            if (!$response->successful()) {
                return [
                    'status' => 'error',
                    'message' => 'Send.lk API request failed',
                    'response' => $response->body()
                ];
            }

            return [
                'status' => 'success',
                'data' => $response->json()
            ];
        } catch (\Exception $e) {

            return [
                'status' => 'error',
                'message' => 'SMS sending failed',
                'error' => $e->getMessage()
            ];
        }
    }
}
