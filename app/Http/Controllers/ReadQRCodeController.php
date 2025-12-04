<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReadQRCodeService;

class ReadQRCodeController extends Controller
{

    protected $readQRCodeService;

    public function __construct(ReadQRCodeService $readQRCodeService)
    {
        $this->readQRCodeService = $readQRCodeService;
    }

    public function readQRCode(Request $request)
    {
        return $this->readQRCodeService->readQRCode($request);
    }
}
