<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDisorderRequest;
use App\Http\Requests\Admin\UpdateDisorderRequest;
use App\Models\Disorder;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DisorderController extends Controller
{
    private const PER_PAGE = 10;

    public function index(): View
    {
        $disorders = Disorder::query()
            ->orderBy('code')
            ->paginate(self::PER_PAGE);

        return view('admin.disorders.index', compact('disorders'));
    }

    public function create(): View
    {
        return view('admin.disorders.create');
    }

    public function store(StoreDisorderRequest $request): RedirectResponse
    {
        Disorder::create($request->validated());

        return redirect()
            ->route('admin.disorders.index')
            ->with('success', 'Data penyakit berhasil ditambahkan.');
    }

    public function show(Disorder $disorder): View
    {
        return view('admin.disorders.show', compact('disorder'));
    }

    public function edit(Disorder $disorder): View
    {
        return view('admin.disorders.edit', compact('disorder'));
    }

    public function update(UpdateDisorderRequest $request, Disorder $disorder): RedirectResponse
    {
        $disorder->update($request->validated());

        return redirect()
            ->route('admin.disorders.index')
            ->with('success', 'Data penyakit berhasil diperbarui.');
    }

    public function destroy(Disorder $disorder): RedirectResponse
    {
        $disorder->delete();

        return redirect()
            ->route('admin.disorders.index')
            ->with('success', 'Data penyakit berhasil dihapus.');
    }
}
