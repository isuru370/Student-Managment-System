<?php

namespace App\Http\Controllers;

use App\Services\UserTypesService;
use Illuminate\Http\Request;


class UserTypesController extends Controller
{

    protected $userTypeService;

    public function __construct(UserTypesService $userTypesService)
    {
        $this->userTypeService = $userTypesService;
    }

    public function getDropdownUserTypes()
    {
        return $this->userTypeService->getDropdownUserTypes();
    }

    public function getUserTypes()
    {
        return $this->userTypeService->getUserTypes();
    }
    public function getUserType($id)
    {
        return $this->userTypeService->getUserType($id);
    }

    public function update(Request $request, $id)
    {
        return $this->userTypeService->update($request, $id);
    }
    public function store(Request $request)
    {
        return $this->userTypeService->store($request);
    }




    /* ------------------------------------------------------------
     | WEB CONTROLLER METHODS (RETURN VIEWS)
     |------------------------------------------------------------ */

    public function index()
    {
        return view('user-type.index');
    }

    public function createPage()
    {
        return view('user-type.create');
    }

    public function showPage($id)
    {
        return view('user-type.show', compact('id'));
    }
}
