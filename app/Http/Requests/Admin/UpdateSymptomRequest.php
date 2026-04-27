<?php

namespace App\Http\Requests\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSymptomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'belief' => $this->input('belief', $this->input('weight')),
        ]);
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('symptoms', 'code')->ignore($this->routeKey('symptom'))],
            'name' => ['required', 'string', 'max:255'],
            'belief' => ['required', 'numeric', 'min:0', 'max:1'],
            'description' => ['nullable', 'string'],
        ];
    }

    private function routeKey(string $parameter): mixed
    {
        $value = $this->route($parameter);

        return $value instanceof Model ? $value->getKey() : $value;
    }
}
