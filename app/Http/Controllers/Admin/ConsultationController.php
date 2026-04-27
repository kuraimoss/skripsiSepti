<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreConsultationRequest;
use App\Http\Requests\Admin\UpdateConsultationRequest;
use App\Models\Consultation;
use App\Models\Disorder;
use App\Models\Symptom;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ConsultationController extends Controller
{
    private const PER_PAGE = 10;

    public function index(): View
    {
        $consultations = Consultation::query()
            ->with(['detectedMentalDisorder', 'results.mentalDisorder'])
            ->withCount('symptoms')
            ->latest()
            ->paginate(self::PER_PAGE);

        return view('admin.consultations.index', compact('consultations'));
    }

    public function create(): View
    {
        return view('admin.consultations.create', [
            'disorders' => $this->disorderOptions(),
            'symptoms' => $this->symptomOptions(),
        ]);
    }

    public function store(StoreConsultationRequest $request): RedirectResponse
    {
        Consultation::create($request->validated());

        return redirect()
            ->route('admin.consultations.index')
            ->with('success', 'Riwayat konsultasi berhasil ditambahkan.');
    }

    public function show(Consultation $consultation): View
    {
        return view('admin.consultations.show', [
            'consultation' => $consultation->load(['symptoms', 'detectedMentalDisorder', 'results.mentalDisorder']),
        ]);
    }

    public function edit(Consultation $consultation): View
    {
        return view('admin.consultations.edit', [
            'consultation' => $consultation,
            'disorders' => $this->disorderOptions(),
            'symptoms' => $this->symptomOptions(),
        ]);
    }

    public function update(UpdateConsultationRequest $request, Consultation $consultation): RedirectResponse
    {
        $consultation->update($request->validated());

        return redirect()
            ->route('admin.consultations.index')
            ->with('success', 'Riwayat konsultasi berhasil diperbarui.');
    }

    public function destroy(Consultation $consultation): RedirectResponse
    {
        $consultation->delete();

        return redirect()
            ->route('admin.consultations.index')
            ->with('success', 'Riwayat konsultasi berhasil dihapus.');
    }

    private function disorderOptions()
    {
        return Disorder::query()
            ->orderBy('code')
            ->get();
    }

    private function symptomOptions()
    {
        return Symptom::query()
            ->orderBy('code')
            ->get();
    }
}
