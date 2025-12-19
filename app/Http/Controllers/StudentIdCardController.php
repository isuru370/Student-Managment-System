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
     * Display bulk ID card generator page with search/sort functionality
     */
    public function ganarateStudentId(Request $request)
    {
        try {
            // Get parameters from request
            $searchTerm = $request->input('search');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');

            // Validate sort parameters
            $validSortColumns = ['created_at', 'custom_id', 'lname', 'fname'];
            $validSortOrders = ['asc', 'desc'];

            $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'created_at';
            $sortOrder = in_array($sortOrder, $validSortOrders) ? $sortOrder : 'desc';

            // If search term or date filters exist, use search method
            if ($searchTerm || $startDate || $endDate) {
                $students = $this->studentIdCardService->searchStudents(
                    $searchTerm,
                    $startDate,
                    $endDate,
                    $sortBy,
                    $sortOrder
                );
            } else {
                // Get all students with sorting
                $students = $this->studentIdCardService->getAllStudentsForIdCard($sortBy, $sortOrder);
            }

            // Pass search parameters back to view for form persistence
            return view('students.ganarate-student-id', compact('students', 'searchTerm', 'startDate', 'endDate', 'sortBy', 'sortOrder'));
        } catch (Exception $e) {
            Log::error("Error loading ID card generation page: " . $e->getMessage());
            return redirect()->route('dashboard')
                ->with('error', 'Failed to load ID card generation page. Please try again.');
        }
    }

    /**
     * Display single student ID card preview
     */
    public function previewCard($custom_id)
    {
        try {
            // Call service to get student details
            $student = $this->studentIdCardService->getStudentForIdCard($custom_id);

            if (!$student) {
                // Student not found
                abort(404, 'Student ID card not found');
            }

            // Pass student array directly to Blade
            return view('id-cards.design1', compact('student'));
        } catch (Exception $e) {
            // Log error and show friendly page
            Log::error("Student ID card fetch error: " . $e->getMessage());
            return view('id-cards.design1', [
                'student' => null,
                'error' => 'Failed to load student ID card. Please try again.'
            ]);
        }
    }

    /**
     * Generate ID cards for selected students
     */
    public function generateBulkCards(Request $request)
    {
        try {
            $studentIds = $request->input('student_ids', []);
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');

            if (empty($studentIds)) {
                return redirect()->route('student-id-card.ganarateStudentId')
                    ->with('error', 'Please select at least one student.');
            }

            // Get selected students with sorting
            $students = $this->studentIdCardService->getMultipleStudentsForIdCard($studentIds, $sortBy, $sortOrder);

            if (!$students || $students->isEmpty()) {
                return redirect()->route('student-id-card.ganarateStudentId')
                    ->with('error', 'No students found for the selected IDs.');
            }

            // Return bulk preview view
            return view('id-cards.bulk-preview', compact('students'));
        } catch (Exception $e) {
            Log::error("Bulk ID card generation error: " . $e->getMessage());
            return redirect()->route('student-id-card.ganarateStudentId')
                ->with('error', 'Failed to generate ID cards. Please try again.');
        }
    }

    /**
     * Generate ID card for all students
     */
    public function generateAllCards(Request $request)
    {
        try {
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');

            $students = $this->studentIdCardService->getAllStudentsForIdCard($sortBy, $sortOrder);

            if (!$students || $students->isEmpty()) {
                return redirect()->route('student-id-card.ganarateStudentId')
                    ->with('error', 'No students found to generate ID cards.');
            }

            // Return bulk preview view
            return view('id-cards.bulk-preview', compact('students'));
        } catch (Exception $e) {
            Log::error("All ID cards generation error: " . $e->getMessage());
            return redirect()->route('student-id-card.ganarateStudentId')
                ->with('error', 'Failed to generate ID cards. Please try again.');
        }
    }

    /**
     * Clear search filters
     */
    public function clearFilters()
    {
        return redirect()->route('student-id-card.ganarateStudentId');
    }
}
