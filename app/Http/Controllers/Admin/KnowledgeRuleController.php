<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreKnowledgeRuleRequest;
use App\Http\Requests\Admin\UpdateKnowledgeRuleRequest;
use App\Models\Disorder;
use App\Models\KnowledgeRule;
use App\Models\Symptom;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KnowledgeRuleController extends Controller
{
    private const PER_PAGE = 10;

    public function index(): View
    {
        $rules = KnowledgeRule::query()
            ->with(['symptom', 'mentalDisorder'])
            ->latest()
            ->paginate(self::PER_PAGE);

        return view('admin.knowledge-rules.index', compact('rules'));
    }

    public function create(): View
    {
        return view('admin.knowledge-rules.create', [
            'disorders' => $this->disorderOptions(),
            'symptoms' => $this->symptomOptions(),
        ]);
    }

    public function store(StoreKnowledgeRuleRequest $request): RedirectResponse
    {
        KnowledgeRule::create($request->validated());

        return redirect()
            ->route('admin.knowledge-rules.index')
            ->with('success', 'Basis pengetahuan berhasil ditambahkan.');
    }

    public function show(KnowledgeRule $knowledgeRule): View
    {
        return view('admin.knowledge-rules.show', ['rule' => $knowledgeRule->load(['symptom', 'mentalDisorder'])]);
    }

    public function edit(KnowledgeRule $knowledgeRule): View
    {
        return view('admin.knowledge-rules.edit', [
            'disorders' => $this->disorderOptions(),
            'rule' => $knowledgeRule,
            'symptoms' => $this->symptomOptions(),
        ]);
    }

    public function update(UpdateKnowledgeRuleRequest $request, KnowledgeRule $knowledgeRule): RedirectResponse
    {
        $knowledgeRule->update($request->validated());

        return redirect()
            ->route('admin.knowledge-rules.index')
            ->with('success', 'Basis pengetahuan berhasil diperbarui.');
    }

    public function destroy(KnowledgeRule $knowledgeRule): RedirectResponse
    {
        $knowledgeRule->delete();

        return redirect()
            ->route('admin.knowledge-rules.index')
            ->with('success', 'Basis pengetahuan berhasil dihapus.');
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
