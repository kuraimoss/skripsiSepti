<?php

namespace App\Http\Requests\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDisorderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'solution' => $this->input('solution', $this->input('recommendations')),
        ]);
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('mental_disorders', 'code')->ignore($this->routeKey('disorder'))],
            'name' => ['required', 'string', 'max:255'],
            'scientific_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'solution' => ['nullable', 'string', 'max:1000'],
        ];
    }

    private function routeKey(string $parameter): mixed
    {
        $value = $this->route($parameter);

        return $value instanceof Model ? $value->getKey() : $value;
    }
}
