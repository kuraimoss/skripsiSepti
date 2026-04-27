@extends('layouts.admin', [
    'title' => 'Dashboard',
    'heading' => 'Dashboard',
    'subheading' => 'Ringkasan data utama sistem.',
])

@php
    $stats = collect($stats ?? [
        ['label' => 'Total gejala', 'value' => data_get($summary ?? [], 'symptoms_count', 0), 'tone' => 'teal'],
        ['label' => 'Total gangguan', 'value' => data_get($summary ?? [], 'disorders_count', 0), 'tone' => 'slate'],
        ['label' => 'Aturan aktif', 'value' => data_get($summary ?? [], 'rules_count', 0), 'tone' => 'amber'],
        ['label' => 'Konsultasi', 'value' => data_get($summary ?? [], 'consultations_count', 0), 'tone' => 'rose'],
    ]);
    $recentConsultations = collect($recentConsultations ?? []);
    $topDisorders = collect($topDisorders ?? []);
@endphp

@section('header_actions')
    <a href="{{ \Illuminate\Support\Facades\Route::has('admin.knowledge-rules.create') ? route('admin.knowledge-rules.create') : '#' }}" class="inline-flex items-center justify-center rounded-md bg-teal-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-teal-800">
        Tambah Aturan
    </a>
@endsection

@section('content')
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as $stat)
            @php
                $tone = data_get($stat, 'tone', 'slate');
                $toneClass = match ($tone) {
                    'teal' => 'border-teal-200 bg-teal-50 text-teal-950',
                    'amber' => 'border-amber-200 bg-amber-50 text-amber-950',
                    'rose' => 'border-rose-200 bg-rose-50 text-rose-950',
                    default => 'border-slate-200 bg-white text-slate-950',
                };
            @endphp
            <section class="rounded-lg border p-5 {{ $toneClass }}">
                <p class="text-sm font-medium opacity-80">{{ data_get($stat, 'label') }}</p>
                <p class="mt-3 text-3xl font-semibold tracking-normal">{{ data_get($stat, 'value', 0) }}</p>
                @if (data_get($stat, 'note'))
                    <p class="mt-2 text-sm leading-6 opacity-75">{{ data_get($stat, 'note') }}</p>
                @endif
            </section>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
        <section class="rounded-lg border border-slate-200 bg-white">
            <div class="flex items-center justify-between gap-4 border-b border-slate-200 p-5">
                <div>
                    <h2 class="text-base font-semibold text-slate-950">Konsultasi terbaru</h2>
                    <p class="mt-1 text-sm text-slate-500">Data terakhir yang masuk.</p>
                </div>
                <a href="{{ \Illuminate\Support\Facades\Route::has('admin.consultations.index') ? route('admin.consultations.index') : '#' }}" class="text-sm font-semibold text-teal-700 hover:text-teal-900">Lihat semua</a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600">Pasien</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600">Hasil utama</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600">Belief</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($recentConsultations as $consultation)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 font-medium text-slate-950">{{ data_get($consultation, 'patient_name', '-') }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ data_get($consultation, 'primary_result.disorder.name', data_get($consultation, 'primary_result.name', '-')) }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ data_get($consultation, 'primary_result.belief', data_get($consultation, 'confidence', '-')) }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ data_get($consultation, 'created_at', '-') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-sm text-slate-500">Belum ada data konsultasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-950">Distribusi hasil</h2>
                    <p class="mt-1 text-sm text-slate-500">Gangguan yang paling sering muncul.</p>
                </div>
            </div>

            <div class="mt-5 space-y-4">
                @forelse ($topDisorders as $item)
                    @php
                        $count = (int) data_get($item, 'count', 0);
                        $percent = max(0, min(100, (float) data_get($item, 'percentage', $count)));
                    @endphp
                    <div>
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-sm font-medium text-slate-800">{{ data_get($item, 'name', data_get($item, 'disorder.name', '-')) }}</p>
                            <p class="text-sm font-semibold text-slate-950">{{ $count }}</p>
                        </div>
                        <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-teal-700" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-md border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500">
                        Distribusi hasil akan muncul setelah konsultasi diproses.
                    </div>
                @endforelse
            </div>
        </section>
    </div>

@endsection
