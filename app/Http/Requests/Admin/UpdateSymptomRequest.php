<?php

namespace App\Http\Requests\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSymptomRequest extends FormRequest
{
    /**
     * Function ini digunakan untuk mengizinkan admin
     * memperbarui data gejala.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Function ini digunakan untuk menyamakan input bobot
     * ke field belief sebelum divalidasi.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'belief' => $this->input('belief', $this->input('weight')),
        ]);
    }

    /**
     * Function ini digunakan untuk menentukan aturan validasi
     * data gejala yang sedang diperbarui.
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('symptoms', 'code')->ignore($this->routeKey('symptom'))],
            'name' => ['required', 'string', 'max:255'],
            'belief' => ['required', 'numeric', 'min:0.1', 'max:1'],
            'description' => ['nullable', 'string'],
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
