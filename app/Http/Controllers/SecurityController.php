<?php

namespace App\Http\Controllers;

class SecurityController extends Controller
{
    public function __invoke()
    {
        return view('security');
    }
}
