<?php


namespace App\Services;


use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Validation\Rule;

class TeacherService
{
    public function fetchTeachers()
    {
        try {
            $teachers = Teacher::with([
                'bankBranch' => function ($query) {
                    $query->select('id', 'branch_name', 'branch_code', 'bank_id');
                },
                'bankBranch.bank' => function ($query) {
                    $query->select('id', 'bank_name', 'bank_code');
                }
            ])->orderBy('id', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $teachers
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch teachers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Optional: Get only active teachers
    public function fetchActiveTeachers()
    {
        try {
            $teachers = Teacher::with([
                'bankBranch' => function ($query) {
                    $query->select('id', 'branch_name', 'branch_code', 'bank_id');
                },
                'bankBranch.bank' => function ($query) {
                    $query->select('id', 'bank_name', 'bank_code');
                }
            ])->where('is_active', 1)
                ->orderBy('id', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $teachers
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch active teachers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // API endpoint to fetch specific teacher
    public function fetchTeacher($id)
    {
        try {
            $teacher = Teacher::with([
                'bankBranch' => function ($query) {
                    $query->select('id', 'branch_name', 'branch_code', 'bank_id');
                },
                'bankBranch.bank' => function ($query) {
                    $query->select('id', 'bank_name', 'bank_code');
                }
            ])->find($id);

            if (!$teacher) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Teacher not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $teacher
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch teacher details',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // 🟢 Validate inputs - Fixed experience validation
            $validated = $request->validate([
                'fname' => 'required|string|max:255',
                'lname' => 'required|string|max:255',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('teachers', 'email')
                ],
                'mobile' => 'required|string|max:15',
                'nic' => [
                    'nullable',
                    'string',
                    'max:20',
                    Rule::unique('teachers', 'nic')
                ],
                'bday' => 'nullable|date',
                'gender' => 'nullable|in:male,female,other',
                'address1' => 'nullable|string|max:255',
                'address2' => 'nullable|string|max:255',
                'address3' => 'nullable|string|max:255',
                'graduation_details' => 'nullable|string',
                'experience' => 'nullable|string|max:100', // ✅ Fixed: removed min:0
                'precentage' => 'required|numeric|min:0|max:100', // ✅ Make required
                'account_number' => 'nullable|string|max:50',
                'bank_branch_id' => 'nullable|exists:bank_branch,id',
                'is_active' => 'nullable|boolean',
            ]);

            // Custom ID එක create කිරීම
            $customId = $this->generateCustomId();

            // Create Teacher - Provide defaults for NOT NULL fields
            $teacher = Teacher::create([
                'custom_id' => $customId,
                'fname' => $validated['fname'],
                'lname' => $validated['lname'],
                'email' => $validated['email'],
                'mobile' => $validated['mobile'],
                'nic' => $validated['nic'] ?? null,
                'bday' => $validated['bday'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'address1' => $validated['address1'] ?? '', // Empty string for NOT NULL
                'address2' => $validated['address2'] ?? '', // Empty string for NOT NULL
                'address3' => $validated['address3'] ?? null,
                'is_active' => $request->has('is_active') ? $request->is_active : true,
                'graduation_details' => $validated['graduation_details'] ?? null,
                'experience' => $validated['experience'] ?? 'Less than a year', // Default value
                'precentage' => $validated['precentage'] ?? 0,
                'account_number' => $validated['account_number'] ?? null,
                'bank_branch_id' => $validated['bank_branch_id'] ?? null,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'The Teacher was successfully created.',
                'data' => $teacher
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Teacher creation error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while creating the teacher.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $teacher = Teacher::find($id);

            if (!$teacher) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Teacher could not be found.'
                ], 404);
            }

            // Update validation rules
            $validated = $request->validate([
                'fname' => 'required|string|max:255',
                'lname' => 'required|string|max:255',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('teachers', 'email')->ignore($id)
                ],
                'mobile' => 'required|string|max:15',
                'nic' => [
                    'nullable',
                    'string',
                    'max:20',
                    Rule::unique('teachers', 'nic')->ignore($id)
                ],
                'bday' => 'nullable|date',
                'gender' => 'nullable|in:male,female,other',
                'address1' => 'nullable|string|max:255',
                'address2' => 'nullable|string|max:255',
                'address3' => 'nullable|string|max:255',
                'graduation_details' => 'nullable|string',
                'experience' => 'nullable|string|max:100', // ✅ Add validation
                'precentage' => 'required|numeric|min:0|max:100', // ✅ Make required
                'account_number' => 'nullable|string|max:50',
                'bank_branch_id' => 'nullable|exists:bank_branch,id',
                'is_active' => 'nullable|boolean',
            ]);

            // 🟢Update database record - Fix experience logic
            $teacher->update([
                'fname' => $validated['fname'],
                'lname' => $validated['lname'],
                'email' => $validated['email'],
                'mobile' => $validated['mobile'],
                'nic' => $validated['nic'] ?? null,
                'bday' => $validated['bday'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'address1' => $validated['address1'] ?? $teacher->address1,
                'address2' => $validated['address2'] ?? $teacher->address2,
                'address3' => $validated['address3'] ?? $teacher->address3,
                'is_active' => $request->has('is_active') ? $request->is_active : $teacher->is_active,
                'graduation_details' => $validated['graduation_details'] ?? $teacher->graduation_details,
                'experience' => $validated['experience'] ?? $teacher->experience ?? 'Less than a year', // ✅ Fix default
                'precentage' => $validated['precentage'] ?? $teacher->precentage,
                'account_number' => $validated['account_number'] ?? $teacher->account_number,
                'bank_branch_id' => $validated['bank_branch_id'] ?? $teacher->bank_branch_id,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Teacher update successful.',
                'data' => $teacher
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Teacher update error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating the teacher.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $teacher = Teacher::find($id);

            if (!$teacher) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Teacher not found'
                ], 404);
            }

            // Deactivate instead of deleting
            $teacher->update([
                'is_active' => 0
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Teacher deactivated successfully'
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to deactivate teacher',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Reactivate teacher function
    public function reactivate($id)
    {
        DB::beginTransaction();

        try {
            $teacher = Teacher::find($id);

            if (!$teacher) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Teacher not found'
                ], 404);
            }

            // Reactivate the teacher
            $teacher->update([
                'is_active' => 1
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Teacher reactivated successfully',
                'data' => $teacher
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to reactivate teacher',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Check if email is unique (for frontend validation)
    public function checkEmailUnique(Request $request)
    {
        try {
            $exists = Teacher::where('email', $request->email)
                ->when($request->id, fn($q) => $q->where('id', '!=', $request->id))
                ->exists();

            return response()->json([
                'status' => 'success',
                'is_unique' => !$exists,
                'message' => $exists ? 'This email already exists.' : 'The email is available.'
            ]);
        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while checking email uniqueness.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Check if NIC is unique (for frontend validation)
    public function checkNicUnique(Request $request)
    {
        try {
            // NIC හිස් නම් හොඳයි (nullable)
            if (!$request->nic) {
                return response()->json([
                    'status' => 'success',
                    'is_unique' => true,
                    'message' => 'NIC can be obtained.'
                ]);
            }

            $exists = Teacher::where('nic', $request->nic)
                ->when($request->id, fn($q) => $q->where('id', '!=', $request->id))
                ->exists();

            return response()->json([
                'status' => 'success',
                'is_unique' => !$exists,
                'message' => $exists ? 'This NIC already exists.' : 'The NIC is available.'
            ]);
        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while checking the NIC uniqueness.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getDropdownTeachers()
    {
        try {
            $teachers = Teacher::select('id', 'custom_id', 'fname', 'lname')
                ->where('is_active', 1)
                ->get();
            return response()->json([
                'status' => 'success',
                'data' => $teachers,
                'message' => $teachers->isEmpty() ? 'No teachers found' : 'Teachers fetched successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch teachers for dropdown',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    private function generateCustomId()
    {
        $lastTeacher = Teacher::orderBy('id', 'desc')->first();

        if (!$lastTeacher) {
            return 'SAT0001';
        }

        $lastCustomId = $lastTeacher->custom_id;
        $lastNumber = (int) substr($lastCustomId, 3);
        $nextNumber = $lastNumber + 1;

        return 'SAT' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
