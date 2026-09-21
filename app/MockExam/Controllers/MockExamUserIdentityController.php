<?php
// app/MockExam/Controllers/MockExamUserIdentityController.php
namespace App\MockExam\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class MockExamUserIdentityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit(): View
    {
        return view('mock-exam.identity.edit');
    }
}
