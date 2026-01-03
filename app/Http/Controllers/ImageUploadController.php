<?php

namespace App\Http\Controllers;

use App\Services\ImageUploadService;
use Illuminate\Http\Request;

class ImageUploadController extends Controller
{

    protected $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService;
    }

    public function upload(Request $request)
    {
        return $this->imageUploadService->upload($request);
    }
    public function publickUpload(Request $request)
    {
        return $this->imageUploadService->publicUpload($request);
    }
}
