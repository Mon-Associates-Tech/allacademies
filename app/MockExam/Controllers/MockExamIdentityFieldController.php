<?php
// app/MockExam/Controllers/MockExamIdentityFieldController.php
namespace App\MockExam\Controllers;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class MockExamIdentityFieldController extends Controller
{
    public function __construct()
    {
        $this->middleware(fn ($request, $next) => $this->ensureOwner($next));
    }

    public function index(): View
    {
        return view('mock-exam.identity-fields.index');
    }

    private function ensureOwner(\Closure $next): mixed
    {
        abort_unless(auth()->user()?->role === UserRole::OWNER, 403, 'Only the platform owner can manage identity fields.');

        return $next(request());
    }
}
