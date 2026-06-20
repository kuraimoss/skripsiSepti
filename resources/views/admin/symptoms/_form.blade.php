@php
    $symptom = $symptom ?? null;
    $key = data_get($symptom, 'id', data_get($symptom, 'code'));
    $isEdit = filled($key);
    $action = $isEdit
        ? (\Illuminate\Support\Facades\Route::has('admin.symptoms.update') ? route('admin.symptoms.update', $key) : '#')
        : (\Illuminate\Support\Facades\Route::has('admin.symptoms.store') ? route('admin.symptoms.store') : '#');
@endphp

<form method="POST" action="{{ $action }}" class="space-y-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="code" class="block text-sm font-semibold text-slate-700">Kode gejala</label>
            <input id="code" name="code" value="{{ old('code', data_get($symptom, 'code')) }}" type="text" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20" placeholder="G01">
            @error('code') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="belief" class="block text-sm font-semibold text-slate-700">Nilai belief</label>
            <input id="belief" name="belief" value="{{ old('belief', data_get($symptom, 'belief')) }}" type="text" inputmode="decimal" pattern="[0-9.]*" maxlength="4" data-decimal-only class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20" placeholder="0.40">
            @error('belief') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="name" class="block text-sm font-semibold text-slate-700">Nama gejala</label>
        <input id="name" name="name" value="{{ old('name', data_get($symptom, 'name')) }}" type="text" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20" placeholder="Mudah cemas ketika berada di rumah">
        @error('name') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="description" class="block text-sm font-semibold text-slate-700">Deskripsi</label>
        <textarea id="description" name="description" rows="4" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20" placeholder="Penjelasan gejala untuk pasien dan admin.">{{ old('description', data_get($symptom, 'description')) }}</textarea>
        @error('description') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <p class="rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-sm leading-6 text-amber-900 md:col-span-2">
            Nilai belief mengikuti rentang 0.1 sampai 1. Data awal dari dokumen memakai contoh 0.2 sampai 0.8.
        </p>
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
        <a href="{{ \Illuminate\Support\Facades\Route::has('admin.symptoms.index') ? route('admin.symptoms.index') : '#' }}" class="inline-flex items-center justify-center gap-2 rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
            <x-icon name="arrow-right" class="size-4 rotate-180" />
            Batal
        </a>
        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-teal-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-teal-800">
            <x-icon name="check-circle" class="size-4" />
            Simpan Gejala
        </button>
    </div>
</form>
