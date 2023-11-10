<?php

namespace App\Http\Controllers;

class ControllerProfile extends Controller
{
    public function index()
    {
        return view('profile.index');
    }
}
