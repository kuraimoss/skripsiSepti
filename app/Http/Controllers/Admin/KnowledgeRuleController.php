<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreKnowledgeRuleRequest;
use App\Http\Requests\Admin\UpdateKnowledgeRuleRequest;
use App\Models\Disorder;
use App\Models\KnowledgeRule;
use App\Models\Symptom;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KnowledgeRuleController extends Controller
{
    private const PER_PAGE = 10;

    /**
     * Function ini digunakan untuk menampilkan daftar aturan
     * yang menghubungkan gejala dengan gangguan.
     */
    public function index(): View
    {
        $rules = KnowledgeRule::query()
            ->with(['symptom', 'mentalDisorder'])
            ->latest()
            ->paginate(self::PER_PAGE);

        return view('admin.knowledge-rules.index', compact('rules'));
    }

    /**
     * Function ini digunakan untuk menampilkan form
     * pembuatan aturan basis pengetahuan baru.
     */
    public function create(): View
    {
        return view('admin.knowledge-rules.create', [
            'disorders' => $this->disorderOptions(),
            'symptoms' => $this->symptomOptions(),
        ]);
    }

    /**
     * Function ini digunakan untuk menyimpan aturan basis pengetahuan
     * setelah input admin berhasil divalidasi.
     */
    public function store(StoreKnowledgeRuleRequest $request): RedirectResponse
    {
        KnowledgeRule::create($request->validated());

        return redirect()
            ->route('admin.knowledge-rules.index')
            ->with('success', 'Basis pengetahuan berhasil ditambahkan.');
    }

    /**
     * Function ini digunakan untuk menampilkan detail aturan
     * beserta gejala dan gangguan yang terkait.
     */
    public function show(KnowledgeRule $knowledgeRule): View
    {
        return view('admin.knowledge-rules.show', ['rule' => $knowledgeRule->load(['symptom', 'mentalDisorder'])]);
    }

    /**
     * Function ini digunakan untuk menampilkan form edit
     * aturan basis pengetahuan yang dipilih admin.
     */
    public function edit(KnowledgeRule $knowledgeRule): View
    {
        return view('admin.knowledge-rules.edit', [
            'disorders' => $this->disorderOptions(),
            'rule' => $knowledgeRule,
            'symptoms' => $this->symptomOptions(),
        ]);
    }

    /**
     * Function ini digunakan untuk memperbarui aturan basis pengetahuan
     * berdasarkan input admin yang sudah divalidasi.
     */
    public function update(UpdateKnowledgeRuleRequest $request, KnowledgeRule $knowledgeRule): RedirectResponse
    {
        $knowledgeRule->update($request->validated());

        return redirect()
            ->route('admin.knowledge-rules.index')
            ->with('success', 'Basis pengetahuan berhasil diperbarui.');
    }

    /**
     * Function ini digunakan untuk menghapus aturan basis pengetahuan
     * dari sistem pakar.
     */
    public function destroy(KnowledgeRule $knowledgeRule): RedirectResponse
    {
        $knowledgeRule->delete();

        return redirect()
            ->route('admin.knowledge-rules.index')
            ->with('success', 'Basis pengetahuan berhasil dihapus.');
    }

    /**
     * Function ini digunakan untuk mengambil daftar gangguan
     * sebagai pilihan pada form basis pengetahuan.
     */
    private function disorderOptions(): Collection
    {
        return Disorder::query()
            ->orderBy('code')
            ->get();
    }

    /**
     * Function ini digunakan untuk mengambil daftar gejala
     * sebagai pilihan pada form basis pengetahuan.
     */
    private function symptomOptions(): Collection
    {
        return Symptom::query()
            ->orderBy('code')
            ->get();
    }
}
