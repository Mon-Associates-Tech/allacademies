<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MetaDataRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'institution_type' => ['nullable', 'required_if:institution_type,department_based,faculty_based,college_based,institution_only'],
            'institution_name' => ['nullable', 'required_if:institution_type,department_based,faculty_based,college_based,institution_only'],
            'college' => ['nullable', 'required_if:institution_type,college_based'],
            'school' => ['nullable', 'required_if:institution_type,college_based'],
            'faculty' => ['nullable', 'required_if:institution_type,faculty_based'],
            'department' => ['nullable', 'required_if:institution_type,department_based,faculty_based,college_based'],
            'logo' => ['nullable', 'image'],
        ];
    }
}
