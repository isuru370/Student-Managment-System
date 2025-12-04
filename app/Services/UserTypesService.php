<?php

namespace App\Services;

use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Exception;

class UserTypesService
{
/* ------------------------------------------------------------
     | API ENDPOINTS (RETURN JSON - FOR WEB & MOBILE)
     |------------------------------------------------------------ */

    /**
     * Get all user types (for both web and mobile)
     */
    public function getUserTypes()
    {
        try {
            $userTypes = UserType::all();

            return response()->json([
                'status' => 'success',
                'data' => $userTypes
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch user types',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single user type (for both web and mobile)
     */
    public function getUserType($id)
    {
        try {
            $userType = UserType::findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => $userType
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'User type not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Create new user type (for both web and mobile)
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|string|max:255|unique:user_types,type'
            ]);

            $userType = UserType::create([
                'type' => $request->type
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'User type created successfully',
                'data' => $userType
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create user type',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user type (for both web and mobile)
     */
    public function update(Request $request, $id)
    {
        try {
            $userType = UserType::findOrFail($id);

            $request->validate([
                'type' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('user_types', 'type')->ignore($id)
                ]
            ]);

            $userType->update([
                'type' => $request->type
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'User type updated successfully',
                'data' => $userType
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update user type',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user types for dropdown (simplified data - for forms)
     */
    public function getDropdownUserTypes()
    {
        try {
            $userTypes = UserType::select('id', 'type')->get();

            return response()->json([
                'status' => 'success',
                'data' => $userTypes
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch user types for dropdown',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}