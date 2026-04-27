@php
    $disorder = $disorder ?? null;
    $key = data_get($disorder, 'id', data_get($disorder, 'code'));
    $isEdit = filled($key);
    $action = $isEdit
        ? (\Illuminate\Support\Facades\Route::has('admin.disorders.update') ? route('admin.disorders.update', $key) : '#')
        : (\Illuminate\Support\Facades\Route::has('admin.disorders.store') ? route('admin.disorders.store') : '#');
@endphp

<form method="POST" action="{{ $action }}" class="space-y-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="code" class="block text-sm font-semibold text-slate-700">Kode gangguan</label>
            <input id="code" name="code" value="{{ old('code', data_get($disorder, 'code')) }}" type="text" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20" placeholder="P01">
            @error('code') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="scientific_name" class="block text-sm font-semibold text-slate-700">Nama ilmiah / istilah</label>
            <input id="scientific_name" name="scientific_name" value="{{ old('scientific_name', data_get($disorder, 'scientific_name')) }}" type="text" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20" placeholder="Depressive disorder">
            @error('scientific_name') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="name" class="block text-sm font-semibold text-slate-700">Nama gangguan</label>
        <input id="name" name="name" value="{{ old('name', data_get($disorder, 'name')) }}" type="text" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20" placeholder="Stres akut">
        @error('name') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="description" class="block text-sm font-semibold text-slate-700">Deskripsi</label>
        <textarea id="description" name="description" rows="4" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20" placeholder="Penjelasan kondisi untuk halaman info dan laporan.">{{ old('description', data_get($disorder, 'description')) }}</textarea>
        @error('description') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="solution" class="block text-sm font-semibold text-slate-700">Solusi / rekomendasi awal</label>
        <textarea id="solution" name="solution" rows="5" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20" placeholder="Contoh: Segera hubungi psikolog.">{{ old('solution', data_get($disorder, 'solution')) }}</textarea>
        @error('solution') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
        <a href="{{ \Illuminate\Support\Facades\Route::has('admin.disorders.index') ? route('admin.disorders.index') : '#' }}" class="inline-flex justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">Batal</a>
        <button type="submit" class="inline-flex justify-center rounded-md bg-teal-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-teal-800">Simpan Gangguan</button>
    </div>
</form>
