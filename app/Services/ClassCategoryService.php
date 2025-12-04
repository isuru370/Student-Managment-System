<?php


namespace App\Services;

use App\Models\ClassCategory;
use Exception;
use Illuminate\Http\Request;

class ClassCategoryService
{

    // Fetch Category for dropdown
    public function fetchDropdownCategory()
    {
        try {
            $categories = ClassCategory::select('id', 'category_name')->get();

            return response()->json([
                'status' => 'success',
                'data' => $categories
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch categores for dropdown',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function fetchClassCategory()
    {
        return response()->json(ClassCategory::all());
    }

    public function fetchSingleCategory($id)
    {
        try {
            $category = ClassCategory::find($id);

            if (!$category) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Category not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $category
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //  STORE: Create new Class Catrgory
    public function store(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'category_name' => 'required|string|max:255|unique:class_categories,category_name'
            ]);

            // Create grade
            $category = ClassCategory::create([
                'category_name' => $request->category_name
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Category created successfully',
                'data' => $category
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create grade',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //  UPDATE: Update category
    public function update(Request $request, $id)
    {
        try {
            $category = ClassCategory::find($id);

            if (!$category) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Category not found'
                ], 404);
            }

            // Validate
            $request->validate([
                'category_name' => 'required|string|max:255|unique:class_categories,category_name,' . $id
            ]);

            // Update
            $category->update([
                'category_name' => $request->category_name
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'category updated successfully',
                'data' => $category
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update category',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
