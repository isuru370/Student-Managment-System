<?php

namespace App\Services;


use Illuminate\Http\Request;
use App\Models\Admissions;

class StudentAdmissionService
{
    public function fetchAdmissions()
    {
        try {
            // Fetch all admissions
            $result = Admissions::all();

            // If there are admissions, return them
            if ($result->isNotEmpty()) {
                return response()->json([
                    'status' => true,
                    'data' => $result
                ], 200);
            }

            // If the result is empty
            return response()->json([
                'status' => false,
                'message' => 'No admissions found'
            ], 404);
        } catch (\Exception $e) {
            // Error occurred
            return response()->json([
                'status' => false,
                'error' => 'Failed to fetch admissions data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function storeAdmission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        try {
            // Check for duplicate name
            $exists = Admissions::where('name', $request->name)->exists();

            if ($exists) {
                return response()->json([
                    'status' => false,
                    'message' => 'An admission with this name already exists.'
                ], 409); // 409 = Conflict
            }

            // Create new admission
            $admission = Admissions::create([
                'name' => $request->input('name'),
                'amount' => $request->input('amount'),
            ]);

            return response()->json([
                'status' => true,
                'data' => $admission,
                'message' => 'Admission created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => 'Failed to create admission',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateAdmission(Request $request, $id)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        try {
            // Find record
            $admission = Admissions::find($id);

            if (!$admission) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Admission record not found',
                ], 404);
            }

            // Check if new name already exists in other records
            $duplicate = Admissions::where('name', $request->name)
                ->where('id', '!=', $id) // exclude current record
                ->exists();

            if ($duplicate) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Another admission with this name already exists.',
                ], 409); // conflict
            }

            // Update record
            $admission->update([
                'name'   => $request->name,
                'amount' => $request->amount,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Admission updated successfully',
                'data'    => $admission,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'error'   => 'Failed to update admission',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function showAdmission($id)
    {
        try {
            // Find the admission by ID
            $admission = Admissions::find($id);

            if (!$admission) {
                return response()->json([
                    'status' => false,
                    'message' => 'Admission not found'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $admission
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => 'Failed to fetch admission data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getDropdownAdmissions()
    {
        try {
            $admissions = Admissions::select('id', 'name', 'amount')->get();

            return response()->json([
                'status' => 'success',
                'data' => $admissions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch admission for dropdown',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
