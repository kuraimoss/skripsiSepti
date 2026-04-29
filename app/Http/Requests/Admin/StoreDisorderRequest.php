<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDisorderRequest extends FormRequest
{
    /**
     * Function ini digunakan untuk mengizinkan admin
     * menyimpan data gangguan baru.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Function ini digunakan untuk menyamakan input rekomendasi
     * ke field solution sebelum divalidasi.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'solution' => $this->input('solution', $this->input('recommendations')),
        ]);
    }

    /**
     * Function ini digunakan untuk menentukan aturan validasi
     * data gangguan yang akan disimpan.
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('mental_disorders', 'code')],
            'name' => ['required', 'string', 'max:255'],
            'scientific_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'solution' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
