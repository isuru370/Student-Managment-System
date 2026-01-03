<?php

namespace App\Services;

use App\Models\ClassAttendance;
use App\Models\Payments;
use App\Models\Student;
use App\Models\StudentAttendances;
use App\Models\StudentStudentStudentClass;
use Carbon\Carbon;
use Exception;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class StudentService
{
    public function fetchStudents()
    {
        try {
            $perPage = request()->get('per_page', 15); // Default to 15 items per page
            $search = request()->get('search', '');

            $studentsQuery = Student::with([
                'grade' => function ($query) {
                    $query->select('id', 'grade_name');
                },
            ]);

            // Add search functionality if search parameter is provided
            if (!empty($search)) {
                $studentsQuery->where(function ($query) use ($search) {
                    $query->where('fname', 'like', "%{$search}%")
                        ->orWhere('lname', 'like', "%{$search}%")
                        ->orWhere('custom_id', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('grade', function ($gradeQuery) use ($search) {
                            $gradeQuery->where('grade_name', 'like', "%{$search}%");
                        });
                });
            }

            $students = $studentsQuery->orderBy('id', 'desc')->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'students' => $students->items(),
                    'pagination' => [
                        'current_page' => $students->currentPage(),
                        'last_page' => $students->lastPage(),
                        'per_page' => $students->perPage(),
                        'total' => $students->total(),
                        'from' => $students->firstItem(),
                        'to' => $students->lastItem(),
                        'links' => $students->links()->toHtml() ?? [], // For Blade templates
                    ]
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch students',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    // Optional: Get only active student
    public function fetchActiveStudents()
    {
        try {
            $teachers = Student::with([
                'grade' => function ($query) {
                    $query->select('id', 'grade_name');
                },
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

    public function fetchNotPaidAdmissionStudent()
    {
        try {
            $students = Student::select([
                'id',
                'custom_id',
                'fname',
                'lname',
                'mobile',
                'whatsapp_mobile',
                'img_url',
                'guardian_mobile',
                'grade_id'
            ])
                ->with([
                    'grade:id,grade_name'
                ])
                ->where('is_active', 1)
                ->orderBy('id', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $students
            ]);
        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch active students',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // API: fetch single student
    public function fetchStudent($id)
    {
        try {
            $student = Student::with([
                'grade' => function ($query) {
                    $query->select('id', 'grade_name');
                },
            ])->find($id);

            if (!$student) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Student not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $student
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch student details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function filterByCreatedDate(Request $request)
    {
        try {

            if ($request->has('date')) {
                $request->validate([
                    'date' => 'required|date',
                ]);

                $students = Student::with('grade:id,grade_name')
                    ->whereDate('created_at', $request->date)
                    ->get();
            } elseif ($request->has('from') && $request->has('to')) {
                $request->validate([
                    'from' => 'required|date',
                    'to'   => 'required|date|after_or_equal:from',
                ]);

                $students = Student::with('grade:id,grade_name')
                    ->whereBetween('created_at', [$request->from, $request->to])
                    ->get();
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please provide either a "date" or a "from" and "to" date range.'
                ], 400);
            }

            return response()->json([
                'status' => 'success',
                'count' => $students->count(),
                'data' => $students
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch filtered students',
                'error' => $e->getMessage(),
            ], 500);
        }
    }




    //API: custome Id search
    public function fetchStudentCustomId($customId)
    {
        try {
            $student = Student::with([
                'grade:id,grade_name'
            ])
                ->where('custom_id', $customId)   // <-- FIXED
                ->first();

            if (!$student) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Student not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $student
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch student details',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // API: create student
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'fname' => 'required|string|max:255',
                'lname' => 'required|string|max:255',
                'email' => ['nullable', 'email', Rule::unique('students', 'email')],
                'mobile' => 'required|string|max:15',
                'whatsapp_mobile' => 'nullable|string|max:15',
                'nic' => ['nullable', 'string', 'max:20', Rule::unique('students', 'nic')],
                'bday' => 'nullable|date',
                'gender' => 'nullable|in:male,female,other',
                'address1' => 'nullable|string|max:255',
                'address2' => 'nullable|string|max:255',
                'address3' => 'nullable|string|max:255',
                'guardian_fname' => 'nullable|string|max:255',
                'guardian_lname' => 'nullable|string|max:255',
                'guardian_nic' => 'nullable|string|max:20',
                'guardian_mobile' => 'nullable|string|max:15',
                'is_active' => 'nullable|boolean',
                'img_url' => 'required|string|max:255',
                'grade_id' => ['required', 'exists:grades,id'],
                'admission' => 'nullable|boolean',
                'is_freecard' => 'nullable|boolean',
                'student_school' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Generate custom_id
            $customId = $this->generateCustomId($request->grade_id);
            $data = $request->all();
            $data['custom_id'] = $customId;

            // Ensure boolean fields are properly cast
            $data['is_active'] = filter_var($data['is_active'] ?? 1, FILTER_VALIDATE_BOOLEAN);
            $data['admission'] = filter_var($data['admission'] ?? 0, FILTER_VALIDATE_BOOLEAN);
            $data['is_freecard'] = filter_var($data['is_freecard'] ?? 0, FILTER_VALIDATE_BOOLEAN);

            Log::info('Creating student with data:', $data);

            $student = Student::create($data);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Student created successfully',
                'data' => $student
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Student creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // API: public student register

    public function publicStudentRegister(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'fname' => 'required|string|max:255',
                'lname' => 'required|string|max:255',
                'email' => ['nullable', 'email', Rule::unique('students', 'email')],
                'mobile' => 'required|string|max:15',
                'whatsapp_mobile' => 'nullable|string|max:15',
                'nic' => ['nullable', 'string', 'max:20', Rule::unique('students', 'nic')],
                'bday' => 'nullable|date',
                'gender' => 'nullable|in:male,female,other',
                'address1' => 'nullable|string|max:255',
                'address2' => 'nullable|string|max:255',
                'address3' => 'nullable|string|max:255',
                'guardian_fname' => 'nullable|string|max:255',
                'guardian_lname' => 'nullable|string|max:255',
                'guardian_nic' => 'nullable|string|max:20',
                'guardian_mobile' => 'nullable|string|max:15',
                'is_active' => 'nullable|boolean',
                'img_url' => 'required|string|max:255',
                'grade_id' => ['required', 'exists:grades,id'],
                'admission' => 'nullable|boolean',
                'is_freecard' => 'nullable|boolean',
                'student_school' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Generate custom_id
            $customId = $this->generateCustomId($request->grade_id);
            $data = $request->all();
            $data['custom_id'] = $customId;

            // Ensure boolean fields are properly cast
            $data['is_active'] = filter_var($data['is_active'] ?? 1, FILTER_VALIDATE_BOOLEAN);
            $data['admission'] = filter_var($data['admission'] ?? 0, FILTER_VALIDATE_BOOLEAN);
            $data['is_freecard'] = filter_var($data['is_freecard'] ?? 0, FILTER_VALIDATE_BOOLEAN);

            Log::info('Creating student with data:', $data);

            $student = Student::create($data);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Student created successfully',
                'data' => $student
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Student creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // API: update student
    public function update(Request $request, $custom_id)
    {
        DB::beginTransaction();

        try {
            $student = Student::where('custom_id', $custom_id)->first();
            if (!$student) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Student not found'
                ], 404);
            }

            // -------------------------
            // VALIDATION
            // -------------------------
            $validated = $request->validate([
                'fname'             => 'required|string|max:255',
                'lname'             => 'required|string|max:255',
                'email' => [
                    'nullable',
                    'email',
                    Rule::unique('students', 'email')->ignore($student->id),
                ],
                'mobile'            => 'required|string|max:15',
                'whatsapp_mobile'   => 'required|string|max:15',
                'nic' => [
                    'nullable',
                    'string',
                    'max:20',
                    Rule::unique('students', 'nic')->ignore($student->id),
                ],
                // bday will be validated manually (multiple formats)
                'bday'              => 'required|string',
                'gender'            => 'required|in:male,female,other',
                'address1'          => 'required|string|max:255',
                'address2'          => 'required|string|max:255',
                'address3'          => 'nullable|string|max:255',
                'guardian_fname'    => 'required|string|max:255',
                'guardian_lname'    => 'required|string|max:255',
                'guardian_nic'      => 'nullable|string|max:20',
                'guardian_mobile'   => 'required|string|max:15',
                'is_active'         => 'required|boolean',
                'img_url'           => 'required|string|max:255',
                'grade_id' => ['required', 'exists:grades,id'],
                'admission'         => 'required|boolean',
                'is_freecard'       => 'required|boolean',
                'student_school'    => 'nullable|string|max:255'
            ]);

            // -------------------------
            // MULTI-FORMAT DATE FIX
            // -------------------------
            if ($request->filled('bday')) {
                $possibleFormats = [
                    'Y-m-d',
                    'd/m/Y',
                    'd-m-Y',
                    'm-d-Y',
                    'm/d/Y',
                ];

                $parsed = null;

                foreach ($possibleFormats as $format) {
                    $try = \DateTime::createFromFormat($format, $request->bday);
                    if ($try && $try->format($format) === $request->bday) {
                        $parsed = $try->format('Y-m-d'); // Save in proper format
                        break;
                    }
                }

                // If nothing matched, try Carbon's smart parser
                if (!$parsed) {
                    try {
                        $parsed = Carbon::parse($request->bday)->format('Y-m-d');
                    } catch (Exception $e) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Invalid date format',
                            'formats_allowed' => $possibleFormats
                        ], 422);
                    }
                }

                $validated['bday'] = $parsed;
            }

            // -------------------------
            // UPDATE
            // -------------------------
            $student->update($validated);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Student updated successfully',
                'data'    => $student
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to update student',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    //API : student image update
    public function updateStudentImage(Request $request, $custom_id)
    {
        DB::beginTransaction();
        try {
            // ✔ Find by custom_id
            $student = Student::where('custom_id', $custom_id)->first();
            if (!$student) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Student not found'
                ], 404);
            }
            // -------------------------
            // VALIDATION
            // -------------------------
            $validated = $request->validate([
                'img_url' => 'required|string|max:255',
            ]);
            // -------------------------
            // UPDATE IMAGE
            // -------------------------
            $student->update([
                'img_url' => $validated['img_url']
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Student image updated successfully',
                'data'    => $student
            ]);
        } catch (Exception $e) {

            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to update student image',
                'error'   => $e->getMessage()
            ], 500);
        }
    }



    // API: deactivate student
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Log::info('Attempting to deactivate student ID: ' . $id);

            $student = Student::find($id);

            if (!$student) {
                Log::warning('Student not found with ID: ' . $id);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Student not found'
                ], 404);
            }

            Log::info('Student found:', ['student_id' => $student->id, 'current_status' => $student->is_active]);

            $student->update(['is_active' => 0]);

            Log::info('Student deactivated successfully:', ['student_id' => $student->id, 'new_status' => $student->is_active]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Student deactivated successfully',
                'student' => [
                    'id' => $student->id,
                    'is_active' => $student->is_active
                ]
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to deactivate student:', [
                'student_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to deactivate student',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    // API: reactivate student
    public function reactivate($id)
    {
        DB::beginTransaction();
        try {
            $student = Student::find($id);

            if (!$student) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Student not found'
                ], 404);
            }

            $student->update(['is_active' => 1]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Student reactivated successfully',
                'data' => $student
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to reactivate student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Generate custom ID
    private function generateCustomId($gradeId)
    {
        try {
            // Find grade
            $grade = Grade::find($gradeId);

            if (!$grade) {
                throw new Exception("Invalid Grade ID.");
            }

            // Grade code එක grade ID අනුව හෝ grade name අනුව තීරණය කරන්න
            $gradeCode = '';

            // Grade name වලින් අංකය ගන්න (Grade 9, Grade 10, 2025 A/L)
            if (preg_match('/\d+/', $grade->grade_name, $matches)) {
                // Grade 9 -> 09, Grade 10 -> 10
                $gradeCode = str_pad($matches[0], 2, '0', STR_PAD_LEFT);
            } elseif (preg_match('/(\d{4})/', $grade->grade_name, $matches)) {
                // 2025 A/L -> 25
                $year = substr($matches[0], 2); // පසුව ඉරියව් 2
                $gradeCode = $year;
            } else {
                // අනෙක් අවස්ථාවලට grade ID එක භාවිතා කරන්න
                $gradeCode = str_pad($gradeId, 2, '0', STR_PAD_LEFT);
            }

            // මෙම grade එකේ ඇති ළමයින්ගේ ගණන (active හෝ සියල්ල)
            $studentCount = Student::where('grade_id', $gradeId)->count();

            // ඊළඟ අංකය = දැනට ඇති count + 1
            $nextNumber = $studentCount + 1;

            // 3 digits format කරන්න (023, 112, 123 වගේ)
            $sequenceNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            // Custom ID generate කරන්න
            $customId = "SA" . $gradeCode . $sequenceNumber;

            // ප්‍රථම වරට උත්සාහ කරනකොට අංකය භාවිතා වී ඇත්දැයි පරීක්ෂා කරන්න
            $counter = 1;
            $originalCustomId = $customId;

            while (Student::where('custom_id', $customId)->exists()) {
                // අංකය දැනටමත් තිබේ නම්, ඊළඟ අංකයට යන්න
                $nextNumber = $studentCount + $counter + 1;
                $sequenceNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                $customId = "SA" . $gradeCode . $sequenceNumber;
                $counter++;

                // ආරක්ෂිත ලූපයක් සඳහා
                if ($counter > 100) {
                    throw new Exception('Unable to generate unique custom ID after 100 attempts');
                }
            }

            Log::info('Generated Custom ID', [
                'grade_id' => $gradeId,
                'grade_name' => $grade->grade_name,
                'grade_code' => $gradeCode,
                'student_count' => $studentCount,
                'next_number' => $nextNumber,
                'custom_id' => $customId
            ]);

            return $customId;
        } catch (Exception $e) {
            Log::error('Failed to generate custom ID', [
                'grade_id' => $gradeId,
                'error' => $e->getMessage()
            ]);
            throw new Exception('Failed to generate custom ID: ' . $e->getMessage());
        }
    }



    // check kirima sadaha me methoad eka use karanne
    public function generateCustomIdAPI(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'grade_id' => 'required|exists:grades,id'
            ]);

            $gradeId = $request->grade_id;

            // Find grade
            $grade = Grade::find($gradeId);

            if (!$grade) {
                throw new Exception("Invalid Grade ID.");
            }

            // Determine grade code
            if (is_numeric($grade->grade_name)) {
                $gradeCode = str_pad($grade->grade_name, 2, '0', STR_PAD_LEFT);
            } else {
                preg_match('/\d{4}/', $grade->grade_name, $matches);
                $year = $matches ? substr($matches[0], 2) : "00";
                $gradeCode = $year;
            }

            // Count students in this grade
            $studentCount = Student::where('grade_id', $gradeId)->count() + 1;

            // Generate the custom ID
            $customId = "CS" . $gradeCode . str_pad($studentCount, 3, '0', STR_PAD_LEFT);

            return response()->json([
                'status' => 'success',
                'custom_id' => $customId
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate custom ID',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function studentAnalytics($student_id)
    {
        if (!$student_id) {
            throw new \InvalidArgumentException("Student ID is required");
        }

        $today = Carbon::now();

        $studentClasses = StudentStudentStudentClass::with([
            'student',
            'student.grade',
            'classCategoryHasStudentClass',
            'classCategoryHasStudentClass.classCategory',
            'studentClass',
            'studentClass.teacher',
            'studentClass.subject',
            'studentClass.grade'
        ])
            ->where('student_id', $student_id)
            ->get()
            ->map(function ($item) use ($student_id, $today) {

                $classRelation = $item->classCategoryHasStudentClass;
                $studentClass = $item->studentClass;

                $classCategoryId = $item->class_category_has_student_class_id;

                // -------------------------
                // 1) PAYMENTS (only summary)
                // -------------------------
                $payments = $this->getPaymentsForClassEnrollment($student_id, $item->id);

                // -------------------------
                // 2) CLASS ATTENDANCE (no session_ids)
                // -------------------------
                $startDate = Carbon::parse($item->created_at)->startOfDay();
                $endDate = $today->endOfDay();

                $classAttendance = $this->getClassAttendance($classCategoryId, $startDate, $endDate);

                // -------------------------
                // 3) STUDENT ATTENDANCE
                // -------------------------
                $studentAttendance = $this->getStudentAttendance(
                    $classAttendance['ids'],
                    $student_id,
                    $classAttendance['count']
                );

                return [
                    'enrollment_id'   => $item->id,
                    'enrollment_date' => $item->created_at->toDateTimeString(),
                    'status'          => $item->status,
                    'is_free_card'    => $item->is_free_card,

                    // CLASS INFO
                    'class_info' => $studentClass ? [
                        'class_id'   => $studentClass->id,
                        'class_name' => $studentClass->class_name,
                        'teacher'    => $studentClass->teacher ? [
                            'custom_id'  => $studentClass->teacher->custom_id,
                            'first_name' => $studentClass->teacher->fname,
                            'last_name'  => $studentClass->teacher->lname
                        ] : null,
                        'subject' => $studentClass->subject ? [
                            'subject_name' => $studentClass->subject->subject_name
                        ] : null,
                        'grade' => $studentClass->grade ? [
                            'grade_name' => $studentClass->grade->grade_name
                        ] : null
                    ] : null,

                    // CATEGORY INFO
                    'category_info' => $classRelation ? [
                        'fees'          => $classRelation->fees,
                        'category_name' => $classRelation->classCategory->category_name ?? null
                    ] : null,

                    // PAYMENTS — ONLY SUMMARY
                    'payments' => [
                        'summary' => [
                            'payment_count' => $payments['count'],
                            'total_paid'    => $payments['total'],
                            'fees'          => $classRelation->fees ?? 0,
                            'is_fully_paid' => $payments['total'] >= ($classRelation->fees ?? 0)
                        ]
                    ],

                    // CLASS ATTENDANCE — NO session_ids
                    'class_attendance' => [
                        'total_sessions' => $classAttendance['count']
                    ],

                    // STUDENT ATTENDANCE
                    'student_attendance' => [
                        'present_count'   => $studentAttendance['present_count'],
                        'absent_count'    => $studentAttendance['absent_count'],
                        'attendance_rate' =>
                        $classAttendance['count'] > 0
                            ? round(($studentAttendance['present_count'] / $classAttendance['count']) * 100, 2)
                            : 0
                    ]
                ];
            });

        return $studentClasses;
    }


    // --------------------------------------------------------------------
    // PAYMENTS SUMMARY ONLY
    // --------------------------------------------------------------------
    private function getPaymentsForClassEnrollment($student_id, $enrollment_id)
    {
        $payments = Payments::where('student_id', $student_id)
            ->where('student_student_student_classes_id', $enrollment_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'count' => $payments->count(),
            'total' => $payments->sum('amount')
        ];
    }

    // --------------------------------------------------------------------
    // CLASS ATTENDANCE
    // --------------------------------------------------------------------
    private function getClassAttendance($classCategoryId, $startDate, $endDate)
    {
        if (!$classCategoryId) {
            return ['ids' => collect([]), 'count' => 0];
        }

        $records = ClassAttendance::where('class_category_has_student_class_id', $classCategoryId)
            ->where('status', 1)
            ->where('is_ongoing', 1)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->pluck('id');

        return [
            'ids' => $records,
            'count' => $records->count()
        ];
    }

    // --------------------------------------------------------------------
    // STUDENT ATTENDANCE
    // --------------------------------------------------------------------
    private function getStudentAttendance($attendanceIds, $student_id, $totalSessions)
    {
        if ($attendanceIds->count() === 0) {
            return [
                'present_count' => 0,
                'absent_count'  => $totalSessions
            ];
        }

        $records = StudentAttendances::whereIn('status', $attendanceIds)
            ->where('student_id', $student_id)
            ->get();

        $present = $records->count();
        $absent = max(0, $totalSessions - $present);

        return [
            'present_count' => $present,
            'absent_count'  => $absent
        ];
    }
}
