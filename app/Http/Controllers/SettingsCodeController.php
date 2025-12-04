<?php

namespace App\Http\Controllers;


class SettingsCodeController extends Controller
{
    public function indexPage()
    {
        return view('settings.index');
    }
}