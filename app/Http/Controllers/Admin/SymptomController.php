<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSymptomRequest;
use App\Http\Requests\Admin\UpdateSymptomRequest;
use App\Models\Symptom;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SymptomController extends Controller
{
    private const PER_PAGE = 10;

    /**
     * Function ini digunakan untuk menampilkan daftar gejala
     * yang dipakai sebagai evidence pada sistem pakar.
     */
    public function index(): View
    {
        $symptoms = Symptom::query()
            ->orderBy('code')
            ->paginate(self::PER_PAGE);

        return view('admin.symptoms.index', compact('symptoms'));
    }

    /**
     * Function ini digunakan untuk menampilkan form
     * penambahan data gejala baru.
     */
    public function create(): View
    {
        return view('admin.symptoms.create');
    }

    /**
     * Function ini digunakan untuk menyimpan data gejala baru
     * setelah input admin berhasil divalidasi.
     */
    public function store(StoreSymptomRequest $request): RedirectResponse
    {
        Symptom::create($request->validated());

        return redirect()
            ->route('admin.symptoms.index')
            ->with('success', 'Data gejala berhasil ditambahkan.');
    }

    /**
     * Function ini digunakan untuk menampilkan detail satu gejala
     * beserta nilai belief yang dimiliki.
     */
    public function show(Symptom $symptom): View
    {
        return view('admin.symptoms.show', compact('symptom'));
    }

    /**
     * Function ini digunakan untuk menampilkan form edit
     * data gejala yang dipilih admin.
     */
    public function edit(Symptom $symptom): View
    {
        return view('admin.symptoms.edit', compact('symptom'));
    }

    /**
     * Function ini digunakan untuk memperbarui data gejala
     * berdasarkan input admin yang sudah divalidasi.
     */
    public function update(UpdateSymptomRequest $request, Symptom $symptom): RedirectResponse
    {
        $symptom->update($request->validated());

        return redirect()
            ->route('admin.symptoms.index')
            ->with('success', 'Data gejala berhasil diperbarui.');
    }

    /**
     * Function ini digunakan untuk menghapus data gejala
     * dari basis pengetahuan sistem.
     */
    public function destroy(Symptom $symptom): RedirectResponse
    {
        $symptom->delete();

        return redirect()
            ->route('admin.symptoms.index')
            ->with('success', 'Data gejala berhasil dihapus.');
    }
}
