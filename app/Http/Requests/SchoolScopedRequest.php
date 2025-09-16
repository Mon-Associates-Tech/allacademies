<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class SchoolScopedRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Check if user can access the school in the request
        $schoolId = $this->route('school') ?: $this->input('school_id');

        if ($schoolId) {
            return Gate::allows('access-school', $schoolId);
        }

        return true;
    }

    protected function prepareForValidation(): void
    {
        $user = auth()->user();

        // Auto-assign school_id for non-admin users
        if ($user && !($user->isSuperAdmin() || $user->hasRole('owner'))) {
            if (!$this->has('school_id') && $user->school_id) {
                $this->merge(['school_id' => $user->school_id]);
            }
        }
    }
}
