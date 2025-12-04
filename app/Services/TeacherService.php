<?php


namespace App\Services;


use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            // 🟢 Validate inputs
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
                'experience' => 'nullable|integer|min:0',
                'precentage' => 'nullable|numeric|min:0|max:100',
                'account_number' => 'nullable|string|max:50',
                'bank_branch_id' => 'nullable|exists:bank_branch,id',
            ]);

            // Custom ID එක create කිරීම
            $customId = $this->generateCustomId();

            // Create Teacher
            $teacher = Teacher::create([
                'custom_id' => $customId,
                'fname' => $validated['fname'],
                'lname' => $validated['lname'],
                'email' => $validated['email'],
                'mobile' => $validated['mobile'],
                'nic' => $validated['nic'] ?? null,
                'bday' => $validated['bday'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'address1' => $validated['address1'] ?? null,
                'address2' => $validated['address2'] ?? null,
                'address3' => $validated['address3'] ?? null,
                'is_active' => $request->is_active ?? 1,
                'graduation_details' => $validated['graduation_details'] ?? null,
                'experience' => $validated['experience'] ?? null,
                'precentage' => $validated['precentage'] ?? null,
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
                    'message' => 'Teacher සොයාගන්න නොලැබුණි.'
                ], 404);
            }

            //  Update validation rules
            $request->validate([
                'fname' => 'required|string|max:255',
                'lname' => 'required|string|max:255',

                // Email duplicate check (ignore current record)
                'email' => [
                    'required',
                    'email',
                    Rule::unique('teachers', 'email')->ignore($id)
                ],

                'mobile' => 'required|string|max:15',

                // NIC duplicate check (nullable + ignore current record)
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
                'experience' => 'nullable|integer|min:0',
                'precentage' => 'nullable|numeric|min:0|max:100',
                'account_number' => 'nullable|string|max:50',
                'bank_branch_id' => 'nullable|exists:bank_branch,id',
                'is_active' => 'boolean',
            ]);

            // 🟢Update database record
            $teacher->update([
                'fname' => $request->fname,
                'lname' => $request->lname,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'nic' => $request->nic,
                'bday' => $request->bday,
                'gender' => $request->gender,
                'address1' => $request->address1,
                'address2' => $request->address2,
                'address3' => $request->address3,
                'is_active' => $request->is_active ?? $teacher->is_active,
                'graduation_details' => $request->graduation_details,
                'experience' => $request->experience,
                'precentage' => $request->precentage,
                'account_number' => $request->account_number,
                'bank_branch_id' => $request->bank_branch_id,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Teacher update කිරීම සාර්ථකයි.',
                'data' => $teacher
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Teacher update කිරීමේදී දෝෂයක් ඇතිවිය.',
                'error' => $e->getMessage()
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
