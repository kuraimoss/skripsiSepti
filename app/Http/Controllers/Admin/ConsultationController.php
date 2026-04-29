<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreConsultationRequest;
use App\Http\Requests\Admin\UpdateConsultationRequest;
use App\Models\Consultation;
use App\Models\Disorder;
use App\Models\Symptom;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ConsultationController extends Controller
{
    private const PER_PAGE = 10;

    /**
     * Function ini digunakan untuk menampilkan daftar riwayat konsultasi
     * yang pernah disimpan oleh sistem.
     */
    public function index(): View
    {
        $consultations = Consultation::query()
            ->with(['detectedMentalDisorder', 'results.mentalDisorder'])
            ->withCount('symptoms')
            ->latest()
            ->paginate(self::PER_PAGE);

        return view('admin.consultations.index', compact('consultations'));
    }

    /**
     * Function ini digunakan untuk menampilkan form admin
     * untuk menambahkan riwayat konsultasi secara manual.
     */
    public function create(): View
    {
        return view('admin.consultations.create', [
            'disorders' => $this->disorderOptions(),
            'symptoms' => $this->symptomOptions(),
        ]);
    }

    /**
     * Function ini digunakan untuk menyimpan riwayat konsultasi
     * setelah data dari admin berhasil divalidasi.
     */
    public function store(StoreConsultationRequest $request): RedirectResponse
    {
        Consultation::create($request->validated());

        return redirect()
            ->route('admin.consultations.index')
            ->with('success', 'Riwayat konsultasi berhasil ditambahkan.');
    }

    /**
     * Function ini digunakan untuk menampilkan detail riwayat konsultasi
     * beserta gejala dan hasil gangguan yang terdeteksi.
     */
    public function show(Consultation $consultation): View
    {
        return view('admin.consultations.show', [
            'consultation' => $consultation->load(['symptoms', 'detectedMentalDisorder', 'results.mentalDisorder']),
        ]);
    }

    /**
     * Function ini digunakan untuk menampilkan form edit
     * riwayat konsultasi yang dipilih admin.
     */
    public function edit(Consultation $consultation): View
    {
        return view('admin.consultations.edit', [
            'consultation' => $consultation,
            'disorders' => $this->disorderOptions(),
            'symptoms' => $this->symptomOptions(),
        ]);
    }

    /**
     * Function ini digunakan untuk memperbarui riwayat konsultasi
     * berdasarkan input admin yang sudah divalidasi.
     */
    public function update(UpdateConsultationRequest $request, Consultation $consultation): RedirectResponse
    {
        $consultation->update($request->validated());

        return redirect()
            ->route('admin.consultations.index')
            ->with('success', 'Riwayat konsultasi berhasil diperbarui.');
    }

    /**
     * Function ini digunakan untuk menghapus riwayat konsultasi
     * dari database sistem.
     */
    public function destroy(Consultation $consultation): RedirectResponse
    {
        $consultation->delete();

        return redirect()
            ->route('admin.consultations.index')
            ->with('success', 'Riwayat konsultasi berhasil dihapus.');
    }

    /**
     * Function ini digunakan untuk mengambil daftar gangguan
     * sebagai pilihan pada form riwayat konsultasi.
     */
    private function disorderOptions(): Collection
    {
        return Disorder::query()
            ->orderBy('code')
            ->get();
    }

    /**
     * Function ini digunakan untuk mengambil daftar gejala
     * sebagai pilihan pada form riwayat konsultasi.
     */
    private function symptomOptions(): Collection
    {
        return Symptom::query()
            ->orderBy('code')
            ->get();
    }
}
