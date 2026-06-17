<?php

namespace App\Http\Requests\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKnowledgeRuleRequest extends FormRequest
{
    /**
     * Function ini digunakan untuk mengizinkan admin
     * memperbarui aturan basis pengetahuan.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Function ini digunakan untuk menormalkan input aturan
     * sebelum proses validasi dijalankan.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'mental_disorder_id' => $this->input('mental_disorder_id', $this->input('disorder_id')),
            'rule_code' => $this->input('rule_code', 'R-ADMIN'),
        ]);
    }

    /**
     * Function ini digunakan untuk menentukan aturan validasi
     * relasi gejala, gangguan, dan belief yang sedang diperbarui.
     */
    public function rules(): array
    {
        return [
            'rule_code' => ['nullable', 'string', 'max:20'],
            'mental_disorder_id' => ['required', 'integer', 'exists:mental_disorders,id'],
            'symptom_id' => [
                'required',
                'integer',
                'exists:symptoms,id',
                Rule::unique('knowledge_rules', 'symptom_id')
                    ->where(fn ($query) => $query->where('mental_disorder_id', $this->input('mental_disorder_id')))
                    ->ignore($this->routeKey('knowledge_rule')),
            ],
            'belief' => ['required', 'numeric', 'min:0.1', 'max:1'],
        ];
    }

    /**
     * Function ini digunakan untuk mengambil ID model
     * dari parameter route yang sedang diproses.
     */
    private function routeKey(string $parameter): mixed
    {
        $value = $this->route($parameter);

        return $value instanceof Model ? $value->getKey() : $value;
    }
}
