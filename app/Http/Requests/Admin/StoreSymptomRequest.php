<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSymptomRequest extends FormRequest
{
    /**
     * Function ini digunakan untuk mengizinkan admin
     * menyimpan data gejala baru.
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
     * data gejala yang akan disimpan.
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('symptoms', 'code')],
            'name' => ['required', 'string', 'max:255'],
            'belief' => ['required', 'numeric', 'min:0.1', 'max:1'],
            'description' => ['nullable', 'string'],
        ];
    }
}
