<?php

namespace App\Services;

use App\Models\Titute;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class TituteService
{
    /**
     * Fetch titute records with filters
     */
    public function fetch(array $filters = []): Collection
    {
        $query = Titute::query();

        // Active only by default
        if (!isset($filters['status'])) {
            $query->where('status', 1);
        } else {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (!empty($filters['class_category_has_student_class_id'])) {
            $query->where(
                'class_category_has_student_class_id',
                $filters['class_category_has_student_class_id']
            );
        }

        if (!empty($filters['month'])) {
            $date = Carbon::parse($filters['month']);
            $query->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Store new titute
     */
    public function store(array $data): Titute
    {
        $month = Carbon::parse($data['month'] ?? now());

        $this->ensureNoDuplicate(
            $data['student_id'],
            $data['class_category_has_student_class_id'],
            $month
        );

        return Titute::create([
            'student_id' => $data['student_id'],
            'class_category_has_student_class_id' =>
            $data['class_category_has_student_class_id'],
            'titute_for' => $data['titute_for'],
            'status' => 1,
            'created_at' => $month,
        ]);
    }

    /**
     * Update titute
     */
    public function update(int $id, array $data): Titute
    {
        $titute = Titute::where('status', 1)->findOrFail($id);

        $month = Carbon::parse($data['month'] ?? $titute->created_at);

        $this->ensureNoDuplicate(
            $data['student_id'] ?? $titute->student_id,
            $data['class_category_has_student_class_id']
                ?? $titute->class_category_has_student_class_id,
            $month,
            $id
        );

        $titute->update([
            'student_id' =>
            $data['student_id'] ?? $titute->student_id,
            'class_category_has_student_class_id' =>
            $data['class_category_has_student_class_id']
                ?? $titute->class_category_has_student_class_id,
            'titute_for' =>
            $data['titute_for'] ?? $titute->titute_for,
            'created_at' => $month,
        ]);

        return $titute;
    }

    /**
     * Soft delete (status = 0)
     */
    public function delete(int $id): bool
    {
        $titute = Titute::findOrFail($id);

        return $titute->update([
            'status' => 0,
        ]);
    }

    /**
     * Duplicate check helper
     */
    protected function ensureNoDuplicate(
        int $studentId,
        int $classCategoryStudentClassId,
        Carbon $month,

    ): void {
        $query = Titute::where('student_id', $studentId)
            ->where(
                'class_category_has_student_class_id',
                $classCategoryStudentClassId
            )
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->where('status', 1);


        if ($query->exists()) {
            throw ValidationException::withMessages([
                'titute' => 'Titute already exists for this student, class, and month.',
            ]);
        }
    }

    public function checkTitute(
        int $studentId,
        int $classCategoryStudentClassId,
        Carbon $month,
    ) {
        $query = Titute::where('student_id', $studentId)
            ->where(
                'class_category_has_student_class_id',
                $classCategoryStudentClassId
            )
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->where('status', 1);

        return response()->json([
            'status' => 'success',
            'exists' =>$query->exists() ? true : false,
            
        ]);
    }
}
