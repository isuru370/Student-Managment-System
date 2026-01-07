<?php

namespace App\Http\Controllers;

use App\Services\StudentIdCardService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StudentIdCardController extends Controller
{
    protected $studentIdCardService;

    public function __construct(StudentIdCardService $studentIdCardService)
    {
        $this->studentIdCardService = $studentIdCardService;
    }

    /**
     * ID කාඩ්පත් ජනන පිටුව පෙන්වීම
     */
    public function ganarateStudentId(Request $request)
    {
        try {
            // Search parameters ලබා ගැනීම
            $searchName = $request->input('search_name');
            $searchCustomId = $request->input('search_custom_id');
            $searchDate = $request->input('search_date');
            
            // Search filters සමග සිසුන් ලබා ගැනීම
            $students = $this->studentIdCardService->getAllStudentsForIdCard(
                'created_at', 
                'desc', 
                20,
                $searchDate,
                $searchName,
                $searchCustomId
            );
            
            if (isset($students['status']) && $students['status'] === 'error') {
                throw new Exception($students['message']);
            }

            // Pagination query parameters
            $paginationQuery = '';
            if ($request->filled('search_name')) {
                $paginationQuery .= '&search_name=' . urlencode($request->input('search_name'));
            }
            if ($request->filled('search_custom_id')) {
                $paginationQuery .= '&search_custom_id=' . urlencode($request->input('search_custom_id'));
            }
            if ($request->filled('search_date')) {
                $paginationQuery .= '&search_date=' . $request->input('search_date');
            }

            return view('students.ganarate-student-id', compact('students', 'searchDate', 'searchName', 'searchCustomId', 'paginationQuery'));
        } catch (Exception $e) {
            Log::error("Error loading ID card generation page: " . $e->getMessage());
            return redirect()->route('dashboard')
                ->with('error', 'Failed to load the ID card generation page.');
        }
    }

    /**
     * තනි සිසුවෙකුගේ ID කාඩ්පත පෙරදසුන
     */
    public function previewCard($custom_id)
    {
        try {
            $student = $this->studentIdCardService->getStudentForIdCard($custom_id);

            if (!$student) {
                abort(404, 'Students ID card not found');
            }

            return view('id-cards.design1', compact('student'));
        } catch (Exception $e) {
            Log::error("Error getting student ID card:" . $e->getMessage());
            return view('id-cards.design1', [
                'student' => null,
                'error' => 'Student ID card loading failed.'
            ]);
        }
    }
}