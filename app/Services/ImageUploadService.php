<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ImageUploadService
{
    /**
     * Upload image and return full URL
     */
    public function upload(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            $image = $request->file('image');

            $imageName = time() . '_' . preg_replace('/[^A-Za-z0-9\-\_\.]/', '_', $image->getClientOriginalName());

            $uploadPath = public_path('uploads');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $image->move($uploadPath, $imageName);

            // 1. DB save path (without domain)
            $dbPath = '/uploads/' . $imageName;

            // 2. Full URL for API response
            $imageURL = config('constants.image_url') . $imageName;

            return response()->json([
                'status' => 'success',
                'image_url' => $imageURL,
                'db_path' => $dbPath,
                'message' => 'Image uploaded successfully'
            ]);
        } catch (ValidationException $ve) {

            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $ve->errors()
            ], 422);
        } catch (\Exception $e) {

            Log::error('Image upload failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload image',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function publickUpload(Request $request)
    {
        try {
            // Validate the incoming request
            $request->validate([
                'image' => 'required|image|mimes:jpg,jpeg,png|max:2048', // max 2MB
            ]);

            $image = $request->file('image');
            // Remove spaces + special characters
            $imageName = time() . '_' . preg_replace('/[^A-Za-z0-9\-\_\.]/', '_', $image->getClientOriginalName());


            // Ensure uploads/images folder exists
            $uploadPath = public_path('uploads/images');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true); // recursive creation
            }

            // Move image to uploads/images
            $image->move($uploadPath, $imageName);

            // Full URL of the uploaded image
            $imageURL = config('constants.image_url') . $imageName;

            return response()->json([
                'status' => 'success',
                'image_url' => $imageURL,
                'message' => 'Image uploaded successfully'
            ]);
        } catch (ValidationException $ve) {
            // Validation errors
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $ve->errors()
            ], 422);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Image upload failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload image',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
