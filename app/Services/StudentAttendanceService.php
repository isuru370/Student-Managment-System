<?php

namespace App\Services;

use App\Models\ClassAttendance;
use App\Models\Student;
use App\Models\StudentStudentStudentClass;
use App\Models\ClassCategoryHasStudentClass;
use App\Models\StudentAttendances;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StudentAttendanceService
{
    public function readAttendance(Request $request)
    {
        $request->validate([
            'custom_id' => 'required|string'
        ]);

        try {
            $student = Student::where('custom_id', $request->custom_id)->first();

            if (!$student) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Student not found'
                ]);
            }

            if ($student->is_active == 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Student is inactive'
                ]);
            }

            $result = $this->getStudentClassesDetails($student->id);

            return response()->json([
                'status' => 'success',
                'student_id' => $student->id,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch student attendance',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getStudentClassesDetails($student_id)
    {
        if (!$student_id) {
            return [];
        }

        // 1) Student enrollments
        $enrollments = StudentStudentStudentClass::with('studentClass', 'student')
            ->where('student_id', $student_id)
            ->get();

        $studentClassIds = $enrollments->pluck('studentClass.id')->unique();
        if ($studentClassIds->isEmpty()) {
            return [];
        }

        // 2) Fetch categories for enrolled classes
        $allCategories = ClassCategoryHasStudentClass::with('classCategory')
            ->whereIn('student_classes_id', $studentClassIds)
            ->get();
        $categoryIds = $allCategories->pluck('id')->unique();
        if ($categoryIds->isEmpty()) {
            return [];
        }

        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        // 3) Fetch today's classes
        $todaysClasses = ClassAttendance::with('hall')
            ->whereIn('class_category_has_student_class_id', $categoryIds)
            ->whereDate('date', $today)
            ->get();

        $result = [];

        foreach ($enrollments as $enrollment) {
            $studentClass = $enrollment->studentClass;
            if (!$studentClass) continue;

            $categories = $allCategories->where('student_classes_id', $studentClass->id);

            foreach ($categories as $cat) {
                $todaysClass = $todaysClasses->first(
                    fn($c) => $c->class_category_has_student_class_id == $cat->id
                );

                if (!$todaysClass) continue;

                // FIX: parse UTC timestamp to date only for Y-m-d
                $cleanDate = Carbon::parse($todaysClass->date)->format('Y-m-d');

                $start = $this->parseDateTime($cleanDate, $todaysClass->start_time);
                $end   = $this->parseDateTime($cleanDate, $todaysClass->end_time);

                if (!$start || !$end) continue;

                $oneHourBefore = $start->copy()->subHour();

                // Check if current time is inside attendance window (1 hour before → end)
                if (!$now->between($oneHourBefore, $end)) continue;

                // Add to result
                $result[] = [
                    'category_name' => $cat->classCategory->category_name ?? 'N/A',
                    'student_class_name' => $studentClass->class_name ?? 'N/A',
                    'studentStudentStudentClass' => [
                        'student_student_student_class_id' => $enrollment->id,
                        'student_class_status' => $enrollment->status,
                    ],
                    'student' => [
                        'id' => $student_id,
                        'custom_id' => $enrollment->student->custom_id,
                        'first_name' => $enrollment->student->fname,
                        'last_name' => $enrollment->student->lname,
                        'guardian_mobile' => $enrollment->student->guardian_mobile
                    ],
                    'ongoing_class' => [
                        'id' => $todaysClass->id,
                        'class_category_has_student_class_id' => $todaysClass->class_category_has_student_class_id,
                        'start_time' => $todaysClass->start_time,
                        'end_time' => $todaysClass->end_time,
                        'class_hall_id' => $todaysClass->class_hall_id,
                        'class_hall_name' => $todaysClass->hall->hall_name ?? null,
                        'class_hall_price' => $todaysClass->hall->hall_price ?? null,
                        'date' => Carbon::parse($todaysClass->date)->format('Y-m-d'),
                        'is_ongoing' => $todaysClass->is_ongoing,
                        'current_time' => $now->format('h:i A'),
                    ]
                ];
            }
        }

        return $result;
    }

    private function parseDateTime(string $date, string $timeString): ?Carbon
    {
        $time = trim($timeString);

        // Parse formats like: "2 PM", "2:00 PM", "14:00", "2.00 PM"
        $formats = ['h:i A', 'h A', 'H:i', 'h.i A'];

        foreach ($formats as $f) {
            try {
                return Carbon::createFromFormat('Y-m-d ' . $f, $date . ' ' . $time);
            } catch (\Exception $e) {
                // try next format
            }
        }

        return null;
    }

    // ================================
    // STORE ATTENDANCE
    // ================================
    public function storeAttendance(Request $request)
    {
        try {

            // Validate input
            $request->validate([
                'student_id' => 'required|integer',
                'student_student_student_classes' => 'required|integer',
                'status' => 'required|integer'
            ]);


            $date = date('Y-m-d');
            $student_id = $request->student_id;
            $student_student_student_classes_id = $request->student_student_student_classes;
            $class_attendance_id = $request->status;

            // Check duplicate
            $exists = StudentAttendances::whereDate('at_date', $date)
                ->where('student_id', $student_id)
                ->where('student_student_student_classes', $student_student_student_classes_id)
                ->where('status', $class_attendance_id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'status' => 'duplicate',
                    'message' => 'Attendance for this student in this class with this status has already been added for today.'
                ], 409);
            }

            // Create attendance
            $attendance = StudentAttendances::create([
                'at_date' => $date,
                'student_student_student_classes' => $student_student_student_classes_id,
                'student_id' => $student_id,
                'status' => $class_attendance_id
            ]);

            // Update class attendance status
            $this->classAttendanceStatusUpdate($class_attendance_id);

            return response()->json([
                'status' => 'success',
                'message' => 'Attendance added successfully!',
                'data' => $attendance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong while saving attendance. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the class attendance status after student attendance is added
     */
    private function classAttendanceStatusUpdate($class_attendance_id)
    {
        if ($class_attendance_id) {
            $classAttendance = ClassAttendance::find($class_attendance_id);
            if ($classAttendance) {
                // Example: mark as ongoing (1) if not already
                $classAttendance->status = "1";
                $classAttendance->save();
            }
        }
    }

    // ================================
    // Frtch All ATTENDANCE
    // ================================
    public function getAllAttendances(Request $request)
    {
        $request->validate([
            'student_id' => 'required|integer',
            'student_student_student_classes' => 'required|integer'
        ]);

        $student_id = $request->student_id;
        $student_student_student_classes_id = $request->student_student_student_classes;

        try {
            $records = StudentAttendances::with([
                'student',
                'studentStudentClass.studentClass' // use camelCase function name
            ])
                ->where('student_id', $student_id)
                ->where('student_student_student_classes', $student_student_student_classes_id)
                ->orderBy('at_date', 'asc')
                ->get();



            return response()->json([
                'status' => 'success',
                'total_records' => $records->count(),
                'data' => $records
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch attendance records.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ================================
    // UPDATE ATTENDANCE
    // ================================
    public function updateAttendance(Request $request, $id)
    {
        try {

            // Validate input
            $request->validate([
                'student_id' => 'required|integer',
                'student_student_student_classes' => 'required|integer',
                'status' => 'required|integer' // status = class_attendance id
            ]);

            $attendance = StudentAttendances::find($id);

            if (!$attendance) {
                return response()->json([
                    'status' => 'not_found',
                    'message' => 'Attendance record not found.'
                ], 404);
            }

            $date = $attendance->at_date;

            // Check duplicate except current record
            $duplicate = StudentAttendances::whereDate('at_date', $date)
                ->where('student_id', $request->student_id)
                ->where('student_student_student_classes', $request->student_student_student_classes)
                ->where('status', $request->status)
                ->where('id', '!=', $id)
                ->exists();

            if ($duplicate) {
                return response()->json([
                    'status' => 'duplicate',
                    'message' => 'Another attendance record already exists for this student in this class with the same status on this date.'
                ], 409);
            }

            // Update record
            $attendance->update([
                'student_id' => $request->student_id,
                'student_student_student_classes' => $request->student_student_student_classes,
                'status' => $request->status
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Attendance updated successfully!',
                'data' => $attendance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong while updating attendance. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function monthStudentAttendanceCount($student_id, $student_class_id, $yearMonth)
    {
        try {
            [$year, $month] = explode('-', $yearMonth);

            $start = Carbon::create($year, $month, 1)->startOfMonth();
            $end   = Carbon::create($year, $month, 1)->endOfMonth();

            // Attendance Count
            $count = StudentAttendances::where('student_id', $student_id)
                ->where('student_student_student_classes', $student_class_id)
                ->whereBetween('at_date', [$start, $end])
                ->count();

            // Number of days in this month
            $daysInMonth = $start->daysInMonth;

            // Calculate number of weeks in this month
            $weeksInMonth = (int) ceil($daysInMonth / 7);

            return response()->json([
                'status' => 'success',
                'message' => 'Monthly attendance count fetched successfully',
                'data' => [
                    'student_id' => $student_id,
                    'student_class_id' => $student_class_id,
                    'year_month' => $yearMonth,
                    'attendance_count' => $count,
                    'weeks_in_month' => $weeksInMonth
                ]
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch monthly attendance count',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
