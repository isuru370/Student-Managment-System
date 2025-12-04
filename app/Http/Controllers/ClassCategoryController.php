<?php

namespace App\Http\Controllers;

use App\Services\ClassCategoryService;
use Illuminate\Http\Request;

class ClassCategoryController extends Controller
{

    protected $classCategoryService;
    public function __construct(ClassCategoryService $classCategoryService)
    {
        $this->classCategoryService = $classCategoryService;
    }
    public function fetchDropdownCategory()
    {
        return $this->classCategoryService->fetchDropdownCategory();
    }
    public function fetchClassCategory()
    {
        return $this->classCategoryService->fetchClassCategory();
    }

    public function fetchSingleCategory($id)
    {
        return $this->classCategoryService->fetchSingleCategory($id);
    }

    public function update(Request $request, $id)
    {
        return $this->classCategoryService->update($request, $id);
    }

    public function store(Request $request)
    {
        return $this->classCategoryService->store($request);
    }
}
