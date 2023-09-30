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
            'institution' => ['nullable', 'required_if:type,department_based,faculty_based,college_based,institution_only'],
            'college' => ['nullable', 'required_if:type,college_based'],
            'school' => ['nullable', 'required_if:type,college_based'],
            'faculty' => ['nullable', 'required_if:type,faculty_based'],
            'department' => ['nullable', 'required_if:type,department_based,faculty_based,college_based'],
            'logo' => ['nullable', 'image'],
        ];
    }
}
