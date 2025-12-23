<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
                'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            $image = $request->file('image');

            $imageName = time() . '_' . preg_replace(
                '/[^A-Za-z0-9\-\_\.]/',
                '_',
                $image->getClientOriginalName()
            );

            // ✅ වෙනස්කම්: public_path() වෙනුවට base_path() භාවිතා කරන්න
            $uploadPath = base_path('uploads'); // මෙය project root එකේ uploads ෆෝල්ඩරයට යොමු කරයි

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $image->move($uploadPath, $imageName);

            // ✅ වෙනස්කම්: 'public/' නොමැතිව URL එක ජනනය කරන්න
            $imageURL = config('services.image.base_url') . $imageName;
            // හෝ
            // $imageURL = config('app.url') . '/uploads/' . $imageName;

            return response()->json([
                'status' => 'success',
                'image_url' => $imageURL,
                'message' => 'Image uploaded successfully'
            ]);
        } catch (ValidationException $ve) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $ve->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload image',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Upload image to public/uploads/images
     */
    public function publickUpload(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            $image = $request->file('image');

            $imageName = time() . '_' . preg_replace(
                '/[^A-Za-z0-9\-\_\.]/',
                '_',
                $image->getClientOriginalName()
            );

            // ✅ වෙනස්කම්: public_path() වෙනුවට base_path() භාවිතා කරන්න
            $uploadPath = base_path('uploads'); // මෙය project root එකේ uploads ෆෝල්ඩරයට යොමු කරයි

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $image->move($uploadPath, $imageName);

            // ✅ වෙනස්කම්: 'public/' නොමැතිව URL එක ජනනය කරන්න
            $imageURL = config('services.image.base_url') . $imageName;
            // හෝ
            // $imageURL = config('app.url') . '/uploads/' . $imageName;

            return response()->json([
                'status' => 'success',
                'image_url' => $imageURL,
                'message' => 'Image uploaded successfully'
            ]);
        } catch (ValidationException $ve) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $ve->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload image',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
