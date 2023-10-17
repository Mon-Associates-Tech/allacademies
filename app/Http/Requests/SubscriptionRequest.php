<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'package' => ['required', 'string', 'in:individual:full,institution:full'],
            'duration' => ['required', 'numeric'],
            'team' => ['required', 'numeric'],
            'beneficiaries' => ['required_if:package,institution:full'],
            'academic_subject_ids' => ['required', 'array'],
            'academic_subjects_ids.*' => ['required', 'numeric', 'exists:academic_subjects,id']
        ];
    }
}
