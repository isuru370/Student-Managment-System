<?php

namespace App\Services;


use App\Models\QuickPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class QuickPhotoService
{
    // Fetch all active Quick Photos
    public function fetchActiveQuickPhoto()
    {
        try {
            $activePhotos = QuickPhoto::with('grade:id,grade_name')
                ->where('is_active', 1)
                ->orderBy('id', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $activePhotos
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch active quick photos',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // Store Quick Photo
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'quick_img' => 'required|string',
                'grade_id' => 'required|integer'
            ]);

            $customId = $this->generateCustomId();

            $quickPhoto = QuickPhoto::create([
                'custom_id' => $customId,
                'quick_img' => $request->quick_img,
                'grade_id' => $request->grade_id,
                'is_active' => 1,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Quick photo created successfully',
                'data' => $quickPhoto
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create quick photo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Deactivate Quick Photo
    public function destroy($id)
    {
        try {
            $quickPhoto = QuickPhoto::find($id);

            if (!$quickPhoto) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Quick photo not found'
                ], 404);
            }

            $quickPhoto->update(['is_active' => 0]);

            return response()->json([
                'status' => 'success',
                'message' => 'Quick photo deactivated successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to deactivate quick photo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Generate Custom ID
    private function generateCustomId()
    {
        $lastPhoto = QuickPhoto::orderBy('id', 'desc')->first();

        if (!$lastPhoto) {
            return 'SAQI0001';
        }

        $lastCustomId = $lastPhoto->custom_id;
        $lastNumber = (int) substr($lastCustomId, 4); // Corrected index
        $nextNumber = $lastNumber + 1;

        return 'SAQI' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}