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

    public function index(): View
    {
        $symptoms = Symptom::query()
            ->orderBy('code')
            ->paginate(self::PER_PAGE);

        return view('admin.symptoms.index', compact('symptoms'));
    }

    public function create(): View
    {
        return view('admin.symptoms.create');
    }

    public function store(StoreSymptomRequest $request): RedirectResponse
    {
        Symptom::create($request->validated());

        return redirect()
            ->route('admin.symptoms.index')
            ->with('success', 'Data gejala berhasil ditambahkan.');
    }

    public function show(Symptom $symptom): View
    {
        return view('admin.symptoms.show', compact('symptom'));
    }

    public function edit(Symptom $symptom): View
    {
        return view('admin.symptoms.edit', compact('symptom'));
    }

    public function update(UpdateSymptomRequest $request, Symptom $symptom): RedirectResponse
    {
        $symptom->update($request->validated());

        return redirect()
            ->route('admin.symptoms.index')
            ->with('success', 'Data gejala berhasil diperbarui.');
    }

    public function destroy(Symptom $symptom): RedirectResponse
    {
        $symptom->delete();

        return redirect()
            ->route('admin.symptoms.index')
            ->with('success', 'Data gejala berhasil dihapus.');
    }
}
