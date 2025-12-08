<?php

namespace App\Http\Controllers;


use App\Services\StudentAttendanceService;
use Illuminate\Http\Request;


class StudentAttendancesController extends Controller
{


    protected $attendanceService;

    public function __construct(StudentAttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function readAttendance(Request $request)
    {
        return $this->attendanceService->readAttendance($request);
    }

    public function getAllAttendances(Request $request)
    {
        return $this->attendanceService->getAllAttendances($request);
    }
    public function studentAttendClass($class_id, $attendance_id, $class_category_student_class_id)
    {
        return $this->attendanceService->studentAttendClass($class_id, $attendance_id, $class_category_student_class_id);
    }

    public function updateAttendance(Request $request, $id)
    {
        return $this->attendanceService->updateAttendance($request, $id);
    }



    public function monthStudentAttendanceCount($student_id, $student_class_id, $yearMonth)
    {
        return $this->attendanceService->monthStudentAttendanceCount($student_id, $student_class_id, $yearMonth);
    }

    public function attendanceRecoadDelete($id)
    {
        return $this->attendanceService->attendanceRecoadDelete($id);
    }

    public function storeAttendance(Request $request)
    {
        return $this->attendanceService->storeAttendance($request);
    }

    /**
     * web page Route
     */
    public function indexPage()
    {
        return view('student_attendance.index');
    }
    public function dailyMarkPage()
    {
        return view('student_attendance.daily');
    }

    public function detailsPage($class_id, $attendance_id, $class_category_student_class_id)
    {
        return view('student_attendance.details', [
            'class_id' => $class_id,
            'attendance_id' => $attendance_id,
            'class_category_student_class_id' => $class_category_student_class_id,
        ]);
    }
}
