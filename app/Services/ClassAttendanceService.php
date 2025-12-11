<?php


namespace App\Services;

use App\Models\ClassAttendance;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use illuminate\Support\Carbon;

class ClassAttendanceService

{

    public function fetchByClassCategoryHasStudentClasses($classCategoryHasStudentClassId)
    {
        try {
            $attendanceRecords = ClassAttendance::with(['classCategoryStudentClass', 'hall'])
                ->where('class_category_has_student_class_id', $classCategoryHasStudentClassId)
                ->orderBy('date', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $attendanceRecords
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch attendance records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function fetchClassesByDate(Request $request)
    {
        try {
            // Validate incoming date (optional)
            $request->validate([
                'date' => 'nullable|date',
            ]);

            // Use requested date OR default to today
            $date = $request->input('date', now()->format('Y-m-d'));

            $attendances = ClassAttendance::with([
                'classCategoryStudentClass.studentClass.teacher',
                'classCategoryStudentClass.studentClass.subject',
                'classCategoryStudentClass.studentClass.grade',
                'classCategoryStudentClass.classCategory',
                'hall'
            ])
                ->whereDate('date', $date)
                ->get()
                ->map(function ($attendance) {
                    return [
                        'attendance_id' => $attendance->id,
                        'date' => $attendance->date->toDateString(),
                        'start_time' => $attendance->start_time,
                        'end_time' => $attendance->end_time,
                        'day_of_week' => $attendance->day_of_week,
                        'status' => $attendance->status,
                        'is_ongoing' => $attendance->is_ongoing,
                        'class_hall' => $attendance->hall ? $attendance->hall->hall_name : null,
                        'classCategoryStudentClass' => [
                            'class_category_student_class_id' => $attendance->classCategoryStudentClass->id,
                        ],
                        'class_details' => [
                            'class_id' => $attendance->classCategoryStudentClass->studentClass->id,
                            'class_name' => $attendance->classCategoryStudentClass->studentClass->class_name ?? null,
                            'teacher_name' => optional($attendance->classCategoryStudentClass->studentClass->teacher)->fname
                                ? ($attendance->classCategoryStudentClass->studentClass->teacher->fname . ' ' .
                                    $attendance->classCategoryStudentClass->studentClass->teacher->lname)
                                : null,
                            'subject_name' => optional($attendance->classCategoryStudentClass->studentClass->subject)->subject_name,
                            'grade_name' => optional($attendance->classCategoryStudentClass->studentClass->grade)->grade_name,
                            'category_name' => optional($attendance->classCategoryStudentClass->classCategory)->category_name,
                        ]
                    ];
                });

            return response()->json([
                'status' => true,
                'date' => $date,
                'data' => $attendances
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function fetchClassAttendanceByStudent(Request $request)
    {
        $request->validate([
            'student_created_at' => 'required|date|date_format:Y-m-d',
            'class_category_has_student_class_id' => 'required|integer|min:1',
        ]);

        $sriLankaTimezone = 'Asia/Colombo';
        $startDate = Carbon::parse($request->student_created_at)->toDateString();
        $today = Carbon::now($sriLankaTimezone)->toDateString();
        $classCategoryId = $request->class_category_has_student_class_id;

        try {
            $records = ClassAttendance::where('class_category_has_student_class_id', $classCategoryId)
                ->where('status', 1)
                ->whereBetween('date', [$startDate, $today])
                ->orderBy('date', 'asc')
                ->get();

            return response()->json([
                'status' => 'success',
                'total_records' => $records->count(),
                'data' => $records,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch class attendance records.'
            ], 500);
        }
    }

    public function testConnection()
    {
        try {
            $sriLankaTimezone = 'Asia/Colombo';

            // Test database connection
            $sampleRecords = ClassAttendance::limit(5)->get();

            return response()->json([
                'database_connection' => 'success',
                'app_timezone' => config('app.timezone'),
                'current_time_utc' => Carbon::now('UTC')->format('Y-m-d H:i:s'),
                'current_time_colombo' => Carbon::now($sriLankaTimezone)->format('Y-m-d H:i:s'),
                'sample_records_count' => $sampleRecords->count(),
                'sample_records' => $sampleRecords->toArray()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'database_connection' => 'failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store bulk attendance records
     */
    public function storeBulk(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validation (start and end removed from validation)
            $validated = $request->validate([
                'class_category_has_student_class_id' => 'required|exists:class_category_has_student_class,id',
                'start_month' => 'required|date_format:Y-m',
                'end_month' => 'required|date_format:Y-m|after_or_equal:start_month',
                'start_time' => 'required|string',
                'end_time' => 'required|string',
                'day_of_week' => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
                'class_hall_id' => 'required|exists:class_halls,id',
                'status' => 'required|in:0,1',
                'is_ongoing' => 'sometimes|boolean'
            ]);

            // Generate dates between start and end month for the specific day
            $generatedDates = $this->generateDatesForBulk(
                $validated['start_month'],
                $validated['end_month'],
                $validated['day_of_week']
            );

            if (empty($generatedDates)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No dates found for the selected day between the given months'
                ], 422);
            }

            // Check hall availability for ALL dates using single query
            $conflictingBooking = $this->checkBookingHallBulk(
                $generatedDates,
                $validated['start_time'],
                $validated['end_time'],
                $validated['class_hall_id']
            );

            // If conflict found, return error immediately
            if ($conflictingBooking) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hall is already booked on ' . $conflictingBooking->date . ' at the selected time. Please choose a different hall or time.',
                    'conflicting_date' => $conflictingBooking->date
                ], 422);
            }

            // Create all attendance records
            $createdRecords = $this->createBulkAttendanceRecords($generatedDates, $validated);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Bulk attendance records created successfully',
                'data' => [
                    'created_count' => count($createdRecords),
                    'dates_created' => $generatedDates,
                    'total_dates' => count($generatedDates)
                ]
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create bulk attendance records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store single attendance record
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validation (start and end removed from validation)
            $validated = $request->validate([
                'class_category_has_student_class_id' => 'required|exists:class_category_has_student_class,id',
                'date' => 'required|date',
                'start_time' => 'required|string',
                'end_time' => 'required|string',
                'day_of_week' => 'required|string',
                'class_hall_id' => 'required|exists:class_halls,id',
                'status' => 'required|in:0,1',
                'is_ongoing' => 'sometimes|boolean'
                // start and end removed - they will be auto-generated from the date
            ]);

            // Check hall availability for this specific date and time
            $isHallAvailable = $this->checkBookingHall(
                $validated['date'],
                $validated['start_time'],
                $validated['end_time'],
                $validated['class_hall_id']
            );

            if (!$isHallAvailable) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hall is already booked for the selected date and time. Please choose a different hall or time.'
                ], 422);
            }

            // Auto-generate start and end from the date (use the same month)
            $date = Carbon::parse($validated['date']);
            $validated['start'] = $date->format('Y-m'); // 2025-02
            $validated['end'] = $date->format('Y-m');   // 2025-02

            // Create single attendance record
            $attendance = ClassAttendance::create($validated);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Attendance record created successfully',
                'data' => $attendance
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create attendance record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update attendance record
     * PUT /api/class-attendances/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // Find the attendance record
            $attendance = ClassAttendance::findOrFail($id);

            // Validation (start and end removed from validation)
            $validated = $request->validate([
                'class_category_has_student_class_id' => 'required|exists:class_category_has_student_class,id',
                'date' => 'required|date',
                'start_time' => 'required|string',
                'end_time' => 'required|string',
                'day_of_week' => 'required|string',
                'class_hall_id' => 'required|exists:class_halls,id',
                'status' => 'required|in:0,1',
                'is_ongoing' => 'sometimes|boolean'
                // start and end removed - they will be auto-generated from the date
            ]);

            // Check hall availability (excluding current record)
            $isHallAvailable = $this->checkBookingHall(
                $validated['date'],
                $validated['start_time'],
                $validated['end_time'],
                $validated['class_hall_id'],
                $id // Exclude current record
            );

            if (!$isHallAvailable) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hall is already booked for the selected date and time. Please choose a different hall or time.'
                ], 422);
            }

            // Auto-generate start and end from the date (use the same month)
            $date = Carbon::parse($validated['date']);
            $validated['start'] = $date->format('Y-m'); // 2025-02
            $validated['end'] = $date->format('Y-m');   // 2025-02

            // Update the record
            $attendance->update($validated);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Attendance record updated successfully',
                'data' => $attendance
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update attendance record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ==================== HELPER METHODS ====================

    /**
     * Generate dates for bulk operation
     */
    private function generateDatesForBulk($startMonth, $endMonth, $dayOfWeek)
    {
        $startDate = Carbon::parse($startMonth . '-01');
        $endDate = Carbon::parse($endMonth)->endOfMonth();

        $generatedDates = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            if ($currentDate->englishDayOfWeek === $dayOfWeek) {
                $generatedDates[] = $currentDate->format('Y-m-d');
            }
            $currentDate->addDay();
        }

        return $generatedDates;
    }

    /**
     * Check hall availability for multiple dates (Bulk operation)
     */
    private function checkBookingHallBulk($dates, $startTime, $endTime, $hallId)
    {
        try {
            $conflictingBooking = ClassAttendance::where('class_hall_id', $hallId)
                ->whereIn('date', $dates)
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->where(function ($q) use ($startTime) {
                        $q->whereRaw('? BETWEEN TIME(start_time) AND TIME(end_time)', [$startTime]);
                    })->orWhere(function ($q) use ($endTime) {
                        $q->whereRaw('? BETWEEN TIME(start_time) AND TIME(end_time)', [$endTime]);
                    })->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->whereRaw('TIME(start_time) <= ? AND TIME(end_time) >= ?', [$startTime, $endTime]);
                    });
                })
                ->first();

            return $conflictingBooking;
        } catch (Exception $e) {
            logger()->error('Bulk hall booking check error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check hall availability for single date
     */
    private function checkBookingHall($scheduleDate, $startTime, $endTime, $hallId, $excludeId = null)
    {
        try {
            $query = ClassAttendance::where('class_hall_id', $hallId)
                ->where('date', $scheduleDate)
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->where(function ($q) use ($startTime) {
                        $q->whereRaw('? BETWEEN TIME(start_time) AND TIME(end_time)', [$startTime]);
                    })->orWhere(function ($q) use ($endTime) {
                        $q->whereRaw('? BETWEEN TIME(start_time) AND TIME(end_time)', [$endTime]);
                    })->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->whereRaw('TIME(start_time) <= ? AND TIME(end_time) >= ?', [$startTime, $endTime]);
                    });
                });

            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            $conflictExists = $query->exists();
            return !$conflictExists;
        } catch (Exception $e) {
            logger()->error('Hall booking check error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Bulk delete pending attendance records
     * DELETE /api/class-attendances/bulk-delete
     */
    public function bulkDelete(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:class_attendances,id'
            ]);

            // Delete only pending records (status = 0 and future dates)
            $pendingRecords = ClassAttendance::whereIn('id', $validated['ids'])
                ->where('status', '0')
                ->where('date', '>', now()->format('Y-m-d'))
                ->get();

            $deletedCount = $pendingRecords->count();

            // Actually delete the records
            ClassAttendance::whereIn('id', $pendingRecords->pluck('id'))
                ->update(['is_ongoing' => 0]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully deleted ' . $deletedCount . ' pending records',
                'deleted_count' => $deletedCount
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create bulk attendance records
     */
    private function createBulkAttendanceRecords($dates, $validatedData)
    {
        $createdRecords = [];
        $startMonth = $validatedData['start_month']; // 2025-02
        $endMonth = $validatedData['end_month'];     // 2025-12

        foreach ($dates as $date) {
            $attendance = ClassAttendance::create([
                'class_category_has_student_class_id' => $validatedData['class_category_has_student_class_id'],
                'date' => $date, // Specific date (e.g., 2025-02-01)
                'start_time' => $validatedData['start_time'],
                'end_time' => $validatedData['end_time'],
                'day_of_week' => $validatedData['day_of_week'],
                'class_hall_id' => $validatedData['class_hall_id'],
                'status' => $validatedData['status'],
                'is_ongoing' => $validatedData['is_ongoing'] ?? false,
                'start' => $startMonth, // Use the start month directly (2025-02)
                'end' => $endMonth      // Use the end month directly (2025-12)
            ]);

            $createdRecords[] = $attendance;
        }

        return $createdRecords;
    }
}
