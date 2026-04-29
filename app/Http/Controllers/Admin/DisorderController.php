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

    /**
     * Function ini digunakan untuk menampilkan daftar gangguan
     * atau penyakit yang dipakai oleh sistem pakar.
     */
    public function index(): View
    {
        $disorders = Disorder::query()
            ->orderBy('code')
            ->paginate(self::PER_PAGE);

        return view('admin.disorders.index', compact('disorders'));
    }

    /**
     * Function ini digunakan untuk menampilkan form
     * penambahan data gangguan baru.
     */
    public function create(): View
    {
        return view('admin.disorders.create');
    }

    /**
     * Function ini digunakan untuk menyimpan data gangguan baru
     * setelah input admin berhasil divalidasi.
     */
    public function store(StoreDisorderRequest $request): RedirectResponse
    {
        Disorder::create($request->validated());

        return redirect()
            ->route('admin.disorders.index')
            ->with('success', 'Data penyakit berhasil ditambahkan.');
    }

    /**
     * Function ini digunakan untuk menampilkan detail
     * satu data gangguan atau penyakit.
     */
    public function show(Disorder $disorder): View
    {
        return view('admin.disorders.show', compact('disorder'));
    }

    /**
     * Function ini digunakan untuk menampilkan form edit
     * data gangguan yang dipilih admin.
     */
    public function edit(Disorder $disorder): View
    {
        return view('admin.disorders.edit', compact('disorder'));
    }

    /**
     * Function ini digunakan untuk memperbarui data gangguan
     * berdasarkan input admin yang sudah divalidasi.
     */
    public function update(UpdateDisorderRequest $request, Disorder $disorder): RedirectResponse
    {
        $disorder->update($request->validated());

        return redirect()
            ->route('admin.disorders.index')
            ->with('success', 'Data penyakit berhasil diperbarui.');
    }

    /**
     * Function ini digunakan untuk menghapus data gangguan
     * dari basis data sistem pakar.
     */
    public function destroy(Disorder $disorder): RedirectResponse
    {
        $disorder->delete();

        return redirect()
            ->route('admin.disorders.index')
            ->with('success', 'Data penyakit berhasil dihapus.');
    }
}
