<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class ImageUploadService
{
    /**
     * Upload image to public/uploads
     * Save FULL URL to DB
     */
    public function upload(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            ]);

            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $imageName = time() . '.' . strtolower($extension);

            // ✅ නිවැරදි මාර්ගය: public_path භාවිතා කරන්න
            $uploadPath = public_path('uploads');

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $finalPath = $uploadPath . '/' . $imageName;
            $image->move($uploadPath, $imageName);
            chmod($finalPath, 0644);

            // ✅ නිවැරදි URL: asset() භාවිතා කරන්න
            $imageURL = asset('uploads/' . $imageName);

            Log::info('Image uploaded', [
                'filename' => $imageName,
                'url' => $imageURL,
            ]);

            return response()->json([
                'status' => 'success',
                'image_url' => $imageURL,
                'message' => 'Image uploaded successfully'
            ]);
        } catch (\Exception $e) {
            // දෝෂ කළමනාකරණය
        }
    }


    /**
     * Upload image to public/uploads/images
     */
    public function publicUpload(Request $request)
    {
        return $this->upload($request);
    }
}
