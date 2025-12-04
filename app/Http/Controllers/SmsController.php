<?php

namespace App\Http\Controllers;

use App\Services\SmsService;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function sendSMS(Request $request)
    {
        $request->validate([
            "mobile"  => "required",
            "message" => "required",
        ]);

        $result = $this->smsService->sendSMS(
            $request->mobile,
            $request->message
        );

        return response()->json($result);
    }
}
