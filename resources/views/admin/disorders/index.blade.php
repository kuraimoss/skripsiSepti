@extends('layouts.admin', [
    'title' => 'Data Gangguan',
    'heading' => 'Data Gangguan',
    'subheading' => 'Kelola daftar gangguan atau kondisi yang menjadi hipotesis pada sistem pakar.',
])

@php
    $disorderItems = is_object($disorders ?? null) && method_exists($disorders, 'items') ? collect($disorders->items()) : collect($disorders ?? []);
    $indexUrl = \Illuminate\Support\Facades\Route::has('admin.disorders.index') ? route('admin.disorders.index') : '#';
    $createUrl = \Illuminate\Support\Facades\Route::has('admin.disorders.create') ? route('admin.disorders.create') : '#';
@endphp

@section('header_actions')
    <a href="{{ $createUrl }}" class="inline-flex items-center justify-center gap-2 rounded-md bg-teal-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-teal-800">
        <x-icon name="plus" class="size-4" />
        Tambah Gangguan
    </a>
@endsection

@section('content')
    <form method="GET" action="{{ $indexUrl }}" class="mb-5 grid gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-[1fr_auto]">
        <input type="search" name="search" value="{{ request('search') }}" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20" placeholder="Cari kode atau nama gangguan">
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
                        <th class="px-5 py-3 text-left font-semibold text-slate-600">Gangguan</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-600">Nama ilmiah</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-600">Solusi</th>
                        <th class="px-5 py-3 text-right font-semibold text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($disorderItems as $disorder)
                        @php
                            $key = data_get($disorder, 'id', data_get($disorder, 'code'));
                            $editUrl = \Illuminate\Support\Facades\Route::has('admin.disorders.edit') && filled($key) ? route('admin.disorders.edit', $key) : '#';
                            $deleteUrl = \Illuminate\Support\Facades\Route::has('admin.disorders.destroy') && filled($key) ? route('admin.disorders.destroy', $key) : '#';
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-4 font-semibold text-slate-950">{{ data_get($disorder, 'code', '-') }}</td>
                            <td class="px-5 py-4">
                                <p class="font-medium text-slate-950">{{ data_get($disorder, 'name', '-') }}</p>
                                <p class="mt-1 max-w-xl text-xs leading-5 text-slate-500">{{ \Illuminate\Support\Str::limit(data_get($disorder, 'description', '-'), 120) }}</p>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ data_get($disorder, 'scientific_name', '-') }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ \Illuminate\Support\Str::limit(data_get($disorder, 'solution', '-'), 80) }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ $editUrl }}" class="inline-flex items-center gap-1.5 rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                                        <x-icon name="edit-3" class="size-3.5" />
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ $deleteUrl }}" onsubmit="return confirm('Hapus gangguan ini?')">
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
                                <p class="text-sm font-semibold text-slate-900">Belum ada data gangguan.</p>
                                <p class="mt-1 text-sm text-slate-500">Tambahkan hipotesis gangguan untuk basis perhitungan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if (is_object($disorders ?? null) && method_exists($disorders, 'links'))
        <div class="mt-5">{{ $disorders->links() }}</div>
    @endif
@endsection
