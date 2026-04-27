<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKnowledgeRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'mental_disorder_id' => $this->input('mental_disorder_id', $this->input('disorder_id')),
            'rule_code' => $this->input('rule_code', 'R-ADMIN'),
        ]);
    }

    public function rules(): array
    {
        return [
            'rule_code' => ['nullable', 'string', 'max:20'],
            'mental_disorder_id' => ['required', 'integer', 'exists:mental_disorders,id'],
            'symptom_id' => [
                'required',
                'integer',
                'exists:symptoms,id',
                Rule::unique('knowledge_rules', 'symptom_id')->where(fn ($query) => $query
                    ->where('mental_disorder_id', $this->input('mental_disorder_id'))),
            ],
            'belief' => ['required', 'numeric', 'min:0', 'max:1'],
        ];
    }
}
