@extends('layouts.admin', [
    'title' => 'Basis Pengetahuan',
    'heading' => 'Basis Pengetahuan',
    'subheading' => 'Kelola relasi gejala dan gangguan beserta nilai evidence untuk metode Dempster-Shafer.',
])

@php
    $ruleItems = is_object($rules ?? null) && method_exists($rules, 'items') ? collect($rules->items()) : collect($rules ?? []);
    $indexUrl = \Illuminate\Support\Facades\Route::has('admin.knowledge-rules.index') ? route('admin.knowledge-rules.index') : '#';
    $createUrl = \Illuminate\Support\Facades\Route::has('admin.knowledge-rules.create') ? route('admin.knowledge-rules.create') : '#';
@endphp

@section('header_actions')
    <a href="{{ $createUrl }}" class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-teal-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-teal-800 sm:w-auto">
        <x-icon name="plus" class="size-4" />
        Tambah Aturan
    </a>
@endsection

@section('content')
    <form method="GET" action="{{ $indexUrl }}" class="mb-5 grid gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-[1fr_220px_auto]">
        <input type="search" name="search" value="{{ request('search') }}" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20" placeholder="Cari gejala atau gangguan">
        <select name="status" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20">
            <option value="">Semua status</option>
            <option value="active" @selected(request('status') === 'active')>Aktif</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
        </select>
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
                        <th class="px-5 py-3 text-left font-semibold text-slate-600">Gejala</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-600">Gangguan</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-600">Belief</th>

                        <th class="px-5 py-3 text-left font-semibold text-slate-600">Status</th>
                        <th class="px-5 py-3 text-right font-semibold text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($ruleItems as $rule)
                        @php
                            $key = data_get($rule, 'id');
                            $editUrl = \Illuminate\Support\Facades\Route::has('admin.knowledge-rules.edit') && filled($key) ? route('admin.knowledge-rules.edit', $key) : '#';
                            $deleteUrl = \Illuminate\Support\Facades\Route::has('admin.knowledge-rules.destroy') && filled($key) ? route('admin.knowledge-rules.destroy', $key) : '#';
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-950">{{ data_get($rule, 'symptom.code', data_get($rule, 'symptom_code', '-')) }}</p>
                                <p class="mt-1 max-w-md text-xs leading-5 text-slate-500">{{ data_get($rule, 'symptom.name', data_get($rule, 'symptom_name', '-')) }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-950">{{ data_get($rule, 'mentalDisorder.code', data_get($rule, 'disorder.code', '-')) }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ data_get($rule, 'mentalDisorder.name', data_get($rule, 'disorder.name', '-')) }}</p>
                            </td>
                            <td class="px-5 py-4 text-slate-700">{{ data_get($rule, 'belief', '-') }}</td>

                            <td class="px-5 py-4">
                                <span class="rounded-md bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ data_get($rule, 'is_active', true) ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ $editUrl }}" class="inline-flex items-center gap-1.5 rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                                        <x-icon name="edit-3" class="size-3.5" />
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ $deleteUrl }}" onsubmit="return confirm('Hapus aturan ini?')">
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
                                <p class="text-sm font-semibold text-slate-900">Belum ada aturan basis pengetahuan.</p>
                                <p class="mt-1 text-sm text-slate-500">Tambahkan relasi gejala dan gangguan untuk menghitung belief.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if (is_object($rules ?? null) && method_exists($rules, 'links'))
        <div class="mt-5">{{ $rules->links() }}</div>
    @endif
@endsection
