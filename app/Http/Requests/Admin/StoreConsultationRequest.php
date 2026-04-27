<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_name' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'string', 'max:25'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:55'],
            'symptom_ids' => ['required', 'array', 'min:1'],
            'symptom_ids.*' => ['integer', 'exists:symptoms,id'],
            'result_disorder_id' => ['nullable', 'integer', 'exists:disorders,id'],
            'confidence' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
