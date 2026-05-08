@extends('layouts.admin', [
    'title' => 'Riwayat Konsultasi',
    'heading' => 'Riwayat Konsultasi',
    'subheading' => 'Pantau hasil konsultasi pasien, hasil utama, dan riwayat perhitungan sistem pakar.',
])

@php
    $consultationItems = is_object($consultations ?? null) && method_exists($consultations, 'items') ? collect($consultations->items()) : collect($consultations ?? []);
    $indexUrl = \Illuminate\Support\Facades\Route::has('admin.consultations.index') ? route('admin.consultations.index') : '#';
@endphp

@section('content')
    <form method="GET" action="{{ $indexUrl }}" class="mb-5 grid gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-[1fr_180px_180px_auto]">
        <input type="search" name="search" value="{{ request('search') }}" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20" placeholder="Cari nama pasien">
        <input type="date" name="from" value="{{ request('from') }}" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20">
        <input type="date" name="to" value="{{ request('to') }}" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20">
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
                        <th class="px-5 py-3 text-left font-semibold text-slate-600">Pasien</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-600">Hasil utama</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-600">Belief</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-600">Gejala</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-600">Tanggal</th>
                        <th class="px-5 py-3 text-right font-semibold text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($consultationItems as $consultation)
                        @php
                            $key = data_get($consultation, 'id');
                            $showUrl = \Illuminate\Support\Facades\Route::has('admin.consultations.show') && filled($key) ? route('admin.consultations.show', $key) : '#';
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-950">{{ data_get($consultation, 'patient_name', '-') }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ data_get($consultation, 'age', '-') }} tahun</p>
                            </td>
                            <td class="px-5 py-4 text-slate-700">{{ data_get($consultation, 'primary_result.disorder.name', data_get($consultation, 'primary_result.name', '-')) }}</td>
                            <td class="px-5 py-4 text-slate-700">{{ data_get($consultation, 'primary_result.belief', data_get($consultation, 'confidence', '-')) }}</td>
                            <td class="px-5 py-4 text-slate-700">{{ data_get($consultation, 'symptoms_count', collect(data_get($consultation, 'symptoms', []))->count()) }}</td>
                            <td class="px-5 py-4 text-slate-700">{{ data_get($consultation, 'created_at', '-') }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ $showUrl }}" class="inline-flex items-center gap-1.5 rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                                        <x-icon name="book-open" class="size-3.5" />
                                        Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center">
                                <p class="text-sm font-semibold text-slate-900">Belum ada riwayat konsultasi.</p>
                                <p class="mt-1 text-sm text-slate-500">Riwayat akan terisi setelah pasien menyelesaikan konsultasi.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if (is_object($consultations ?? null) && method_exists($consultations, 'links'))
        <div class="mt-5">{{ $consultations->links() }}</div>
    @endif
@endsection
