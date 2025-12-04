<?php

namespace App\Http\Controllers;


use App\Services\QuickPhotoService;
use Illuminate\Http\Request;


class QuickPhotoController extends Controller
{

    protected $quickPhotoService;

    public function __construct(QuickPhotoService $quickPhotoService)
    {
        $this->quickPhotoService = $quickPhotoService;
    }

    public function fetchActiveQuickPhoto()
    {
        return $this->quickPhotoService->fetchActiveQuickPhoto();
    }
    public function destroy($id)
    {
        return $this->quickPhotoService->destroy($id);
    }

    public function store(Request $request)
    {
        return $this->quickPhotoService->store($request);
    }

    // View Students Page
    public function viewPage()
    {
        return view('students.index');
    }

    // Create Student Page
    public function createPage()
    {
        return view('students.create');
    }
}
