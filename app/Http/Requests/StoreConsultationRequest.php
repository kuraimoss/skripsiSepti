<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $symptoms = $this->input('symptoms', $this->input('gejala', $this->input('symptom_ids')));

        if (is_string($symptoms)) {
            $symptoms = array_values(array_filter(array_map('trim', explode(',', $symptoms))));
        }

        $this->merge([
            'name' => $this->input('name', $this->input('nama', $this->input('patient_name'))),
            'age' => $this->input('age', $this->input('umur', $this->input('respondent_age'))),
            'gender' => $this->normalizeGender($this->input('gender', $this->input('kelamin', $this->input('jenis_kelamin')))),
            'address' => $this->input('address', $this->input('alamat')),
            'phone' => $this->input('phone', $this->input('telepon', $this->input('telephone', $this->input('no_hp')))),
            'school' => $this->input('school', $this->input('sekolah')),
            'parent_guardian' => $this->input('parent_guardian', $this->input('wali')),
            'family_stressor' => $this->input('family_stressor', $this->input('pemicu_stres')),
            'notes' => $this->input('notes', $this->input('catatan')),
            'symptoms' => $symptoms,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'age' => ['nullable', 'integer', 'min:10', 'max:24'],
            'gender' => ['required', 'string', Rule::in(['laki-laki', 'perempuan'])],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s().]+$/'],
            'school' => ['nullable', 'string', 'max:150'],
            'parent_guardian' => ['nullable', 'string', 'max:100'],
            'family_stressor' => ['nullable', 'string', Rule::in(['konflik', 'komunikasi', 'ekonomi', 'pengasuhan'])],
            'notes' => ['nullable', 'string', 'max:1000'],
            'symptoms' => ['required', 'array', 'min:1'],
            'symptoms.*' => ['bail', 'integer', 'distinct', 'exists:symptoms,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama',
            'age' => 'umur',
            'gender' => 'jenis kelamin',
            'address' => 'alamat',
            'phone' => 'telepon',
            'school' => 'sekolah',
            'parent_guardian' => 'orang tua/wali',
            'family_stressor' => 'pemicu stres',
            'notes' => 'catatan tambahan',
            'symptoms' => 'gejala',
            'symptoms.*' => 'gejala',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'gender.in' => 'Jenis kelamin harus laki-laki atau perempuan.',
            'family_stressor.in' => 'Pemicu stres tidak valid.',
            'phone.regex' => 'Telepon hanya boleh berisi angka, spasi, dan simbol + - ( ).',
            'symptoms.min' => 'Pilih minimal satu gejala.',
            'symptoms.required' => 'Pilih minimal satu gejala.',
        ];
    }

    private function normalizeGender(mixed $gender): mixed
    {
        if ($gender === null) {
            return null;
        }

        $value = str_replace(['_', ' '], '-', strtolower(trim((string) $gender)));

        return match ($value) {
            'l', 'lk', 'm', 'male', 'pria', 'laki', 'laki-laki' => 'laki-laki',
            'p', 'f', 'female', 'wanita', 'perempuan' => 'perempuan',
            default => $gender,
        };
    }
}
