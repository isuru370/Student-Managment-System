<?php

namespace App\Services;

use App\Models\Student;
use Exception;

class StudentIdCardService
{
    public function getStudentForIdCard(string $customId)
    {
        try {
            // custom_id එකෙන් student එක fetch කරනවා
            $student = Student::where('custom_id', $customId)
                ->select('custom_id', 'fname', 'lname', 'address1', 'address2', 'address3', 'img_url', 'created_at')
                ->first();

            if (!$student) {
                return null; // student not found
            }

            // ID card එකට අවශ්‍ය fields පමණක් return කරන්න
            return [
                'custom_id' => $student->custom_id,
                'fname' => $student->fname,
                'lname' => $student->lname,
                'address' => trim(($student->address1 ?? '') . ' ' . ($student->address2 ?? '') . ' ' . ($student->address3 ?? '')),
                'img_url' => $student->img_url ?? null,
                'created_at' => $student->created_at ?? null,
            ];
        } catch (Exception $e) {
            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }

    public function getAllStudentsForIdCard(string $sortBy = 'created_at', string $sortOrder = 'desc')
    {
        try {
            // Get all students with required fields
            $students = Student::select('custom_id', 'fname', 'lname', 'address1', 'address2', 'address3', 'img_url', 'created_at')
                ->orderBy($sortBy, $sortOrder)
                ->get();

            if ($students->isEmpty()) {
                return null; // No students found
            }

            // Transform each student for ID card format
            return $students->map(function ($student) {
                return [
                    'custom_id' => $student->custom_id,
                    'fname' => $student->fname,
                    'lname' => $student->lname,
                    'address' => trim(
                        ($student->address1 ?? '') . ' ' .
                            ($student->address2 ?? '') . ' ' .
                            ($student->address3 ?? '')
                    ),
                    'img_url' => $student->img_url ?? null,
                    'created_at' => $student->created_at ? $student->created_at->format('Y-m-d H:i:s') : null,
                ];
            });
        } catch (Exception $e) {
            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }

    public function getMultipleStudentsForIdCard(?array $studentIds = null, string $sortBy = 'created_at', string $sortOrder = 'desc')
    {
        try {
            $query = Student::select('custom_id', 'fname', 'lname', 'address1', 'address2', 'address3', 'img_url', 'created_at');

            // If specific IDs are provided, filter by them
            if ($studentIds && !empty($studentIds)) {
                $query->whereIn('custom_id', $studentIds);
            }

            // Apply sorting
            $query->orderBy($sortBy, $sortOrder);

            $students = $query->get();

            if ($students->isEmpty()) {
                return null;
            }

            return $students->map(function ($student) {
                return [
                    'custom_id' => $student->custom_id,
                    'fname' => $student->fname,
                    'lname' => $student->lname,
                    'address' => trim(
                        ($student->address1 ?? '') . ' ' .
                            ($student->address2 ?? '') . ' ' .
                            ($student->address3 ?? '')
                    ),
                    'img_url' => $student->img_url ?? null,
                    'created_at' => $student->created_at ? $student->created_at->format('Y-m-d H:i:s') : null,
                ];
            });
        } catch (Exception $e) {
            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }

    /**
     * Search students with multiple criteria
     */
    public function searchStudents(string $searchTerm = null, ?string $startDate = null, ?string $endDate = null, string $sortBy = 'created_at', string $sortOrder = 'desc')
    {
        try {
            $query = Student::select('custom_id', 'fname', 'lname', 'address1', 'address2', 'address3', 'img_url', 'created_at');

            // Apply search term if provided
            if ($searchTerm) {
                $searchTerm = trim($searchTerm);
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('custom_id', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('fname', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('lname', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('address1', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('address2', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('address3', 'LIKE', "%{$searchTerm}%");
                });
            }

            // Apply date filters if provided
            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            }

            if ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            }

            // Apply sorting
            $query->orderBy($sortBy, $sortOrder);

            $students = $query->get();

            if ($students->isEmpty()) {
                return null;
            }

            return $students->map(function ($student) {
                return [
                    'custom_id' => $student->custom_id,
                    'fname' => $student->fname,
                    'lname' => $student->lname,
                    'address' => trim(
                        ($student->address1 ?? '') . ' ' .
                            ($student->address2 ?? '') . ' ' .
                            ($student->address3 ?? '')
                    ),
                    'img_url' => $student->img_url ?? null,
                    'created_at' => $student->created_at ? $student->created_at->format('Y-m-d H:i:s') : null,
                ];
            });
        } catch (Exception $e) {
            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }
}
