<?php

namespace App\Http\Controllers;


use App\Services\SystemUserService;
use Illuminate\Http\Request;


class SystemUserController extends Controller
{

    protected $systemUserService;

    public function __construct(SystemUserService $systemUserService)
    {
        $this->systemUserService = $systemUserService;
    }

    public function getSystemUsers()
    {
        return $this->systemUserService->getSystemUsers();
    }

    public function getSystemUser($id)
    {
        return $this->systemUserService->getSystemUser($id);
    }

    public function destroy($id)
    {
        return $this->systemUserService->destroy($id);
    }
    public function reactivate($id)
    {
        return $this->systemUserService->reactivate($id);
    }
    public function update(Request $request, $id)
    {
        return $this->systemUserService->update($request, $id);
    }
    public function store(Request $request)
    {
        return $this->systemUserService->store($request);
    }

    /* ------------------------------------------------------------
     | WEB CONTROLLER METHODS  (ONLY RETURN VIEWS)
     |------------------------------------------------------------ */

    // system-users main page
    public function viewPage()
    {
        return view('system-users.view');
    }

    // create user page
    public function createPage()
    {
        return view('system-users.create');
    }

    // view one user page
    public function showPage($id)
    {
        return view('system-users.show', compact('id'));
    }

    // SystemUserController 
    public function editPage($id)
    {
        return view('system-users.edit', compact('id'));
    }
}
