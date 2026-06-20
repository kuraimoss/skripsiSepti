@extends('layouts.admin', [
    'title' => 'Data Gejala',
    'heading' => 'Data Gejala',
    'subheading' => 'Kelola indikator gejala yang dipakai sebagai evidence pada perhitungan Dempster-Shafer.',
])

@php
    $symptomItems = is_object($symptoms ?? null) && method_exists($symptoms, 'items') ? collect($symptoms->items()) : collect($symptoms ?? []);
    $indexUrl = \Illuminate\Support\Facades\Route::has('admin.symptoms.index') ? route('admin.symptoms.index') : '#';
    $createUrl = \Illuminate\Support\Facades\Route::has('admin.symptoms.create') ? route('admin.symptoms.create') : '#';
    $formatBelief = fn ($value) => is_numeric($value) ? rtrim(rtrim(number_format((float) $value, 4, '.', ''), '0'), '.') : '-';
@endphp

@section('header_actions')
    <a href="{{ $createUrl }}" class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-teal-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-teal-800 sm:w-auto">
        <x-icon name="plus" class="size-4" />
        Tambah Gejala
    </a>
@endsection

@section('content')
    <form method="GET" action="{{ $indexUrl }}" class="mb-5 grid gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-[1fr_220px_auto]">
        <input type="search" name="search" value="{{ request('search') }}" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20" placeholder="Cari kode atau nama gejala">
        <input type="text" name="category" value="{{ request('category') }}" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20" placeholder="Kategori">
        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md border border-slate-300 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
            <x-icon name="filter" class="size-4" />
            Filter
        </button>
    </form>

    <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left font-semibold text-slate-600">Kode</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-600">Gejala</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-600">Deskripsi</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-600">Belief</th>
                        <th class="px-5 py-3 text-right font-semibold text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($symptomItems as $symptom)
                        @php
                            $key = data_get($symptom, 'id', data_get($symptom, 'code'));
                            $editUrl = \Illuminate\Support\Facades\Route::has('admin.symptoms.edit') && filled($key) ? route('admin.symptoms.edit', $key) : '#';
                            $deleteUrl = \Illuminate\Support\Facades\Route::has('admin.symptoms.destroy') && filled($key) ? route('admin.symptoms.destroy', $key) : '#';
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-4 font-semibold text-slate-950">{{ data_get($symptom, 'code', '-') }}</td>
                            <td class="px-5 py-4">
                                <p class="font-medium text-slate-950">{{ data_get($symptom, 'name', data_get($symptom, 'description', '-')) }}</p>
                                @if (data_get($symptom, 'description') && data_get($symptom, 'name'))
                                    <p class="mt-1 max-w-xl text-xs leading-5 text-slate-500">{{ \Illuminate\Support\Str::limit(data_get($symptom, 'description'), 110) }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ \Illuminate\Support\Str::limit(data_get($symptom, 'description', '-'), 80) }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $formatBelief(data_get($symptom, 'belief')) }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ $editUrl }}" class="inline-flex items-center gap-1.5 rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                                        <x-icon name="edit-3" class="size-3.5" />
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ $deleteUrl }}" onsubmit="return confirm('Hapus gejala ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-md border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-50">
                                            <x-icon name="trash-2" class="size-3.5" />
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center">
                                <p class="text-sm font-semibold text-slate-900">Belum ada data gejala.</p>
                                <p class="mt-1 text-sm text-slate-500">Tambahkan gejala sebagai evidence untuk basis pengetahuan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if (is_object($symptoms ?? null) && method_exists($symptoms, 'links'))
        <div class="mt-5">{{ $symptoms->links() }}</div>
    @endif
@endsection
