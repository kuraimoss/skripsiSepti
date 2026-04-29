@extends('layouts.app', ['title' => 'Hasil Konsultasi'])

@php
    $formatPercent = function ($value) {
        if ($value === null || $value === '') {
            return '0%';
        }

        $number = (float) $value;
        if ($number <= 1) {
            $number *= 100;
        }

        return rtrim(rtrim(number_format($number, 2), '0'), '.') . '%';
    };

    $percentNumber = function ($value) {
        $number = (float) ($value ?? 0);
        if ($number <= 1) {
            $number *= 100;
        }

        return max(0, min(100, $number));
    };

    $results = collect($results ?? data_get($consultation ?? null, 'results', []));
    $primaryResult = $primaryResult ?? data_get($consultation ?? null, 'primary_result') ?? $results->first();
    $primaryName = data_get($primaryResult, 'disorder.name', data_get($primaryResult, 'name', 'Hasil belum tersedia'));
    $confidence = data_get($primaryResult, 'belief', data_get($primaryResult, 'confidence', data_get($primaryResult, 'percentage', 0)));
    $selectedSymptoms = collect($selectedSymptoms ?? data_get($consultation ?? null, 'symptoms', []));
    $consultationId = data_get($consultation ?? null, 'id');
    $createdAt = data_get($consultation ?? null, 'created_at');
    $createdLabel = $createdAt instanceof \DateTimeInterface ? $createdAt->format('d/m/Y H:i') : ($createdAt ?: date('d/m/Y H:i'));
    $printUrl = \Illuminate\Support\Facades\Route::has('consultation.print') && filled($consultationId) ? route('consultation.print', $consultationId) : '#';
    $additionalNotes = data_get($consultation ?? null, 'additional_notes');
    $certaintyLabel = data_get($consultation ?? null, 'certainty_label') ?: match (true) {
        (float) $confidence >= 1.0 => 'Sangat Pasti',
        (float) $confidence >= 0.75 => 'Pasti',
        (float) $confidence >= 0.50 => 'Cukup Pasti',
        default => 'Kurang Pasti',
    };
@endphp

@section('content')
    <section class="bg-white">
        <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="flex items-center gap-2 text-sm font-semibold text-teal-700">
                        <x-icon name="clipboard-check" class="size-4" />
                        Hasil konsultasi
                    </p>
                    <h1 class="mt-3 text-3xl font-semibold tracking-normal text-slate-950">Ringkasan deteksi awal</h1>
                    <p class="mt-2 text-sm leading-6 text-slate-500">{{ $createdLabel }}</p>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row">
                    <a href="{{ $printUrl }}" class="inline-flex items-center justify-center gap-2 rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                        <x-icon name="printer" class="size-4" />
                        Cetak
                    </a>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('consultation.create') ? route('consultation.create') : '#' }}" class="inline-flex items-center justify-center gap-2 rounded-md bg-teal-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-teal-800">
                        <x-icon name="refresh-cw" class="size-4" />
                        Konsultasi Baru
                    </a>
                </div>
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
                <section class="rounded-lg border border-teal-200 bg-teal-50 p-6">
                    <p class="flex items-center gap-2 text-sm font-semibold text-teal-800">
                        <x-icon name="chart-bar" class="size-4" />
                        Kemungkinan tertinggi
                    </p>
                    <div class="mt-4 flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-2xl font-semibold tracking-normal text-slate-950">{{ $primaryName }}</h2>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex rounded-md bg-white px-3 py-1 text-sm font-semibold text-teal-800">{{ $formatPercent($confidence) }}</span>
                            <p class="mt-2 text-sm font-semibold text-teal-800">{{ $certaintyLabel }}</p>
                        </div>
                    </div>
                    <div class="mt-6 h-3 overflow-hidden rounded-full bg-white">
                        <div class="h-full rounded-full bg-teal-700" style="width: {{ $percentNumber($confidence) }}%"></div>
                    </div>
                    <p class="mt-4 text-sm leading-6 text-slate-600">Tingkat kepastian: <span class="font-semibold text-slate-950">{{ $certaintyLabel }}</span>.</p>
                </section>

                <section class="rounded-lg border border-slate-200 bg-white p-6">
                    <h2 class="flex items-center gap-2 text-base font-semibold text-slate-950">
                        <x-icon name="user-round" class="size-5 text-teal-700" />
                        Data pasien
                    </h2>
                    <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs font-semibold text-slate-500">Nama</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-950">{{ data_get($consultation ?? null, 'patient_name', '-') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-500">Usia</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-950">{{ data_get($consultation ?? null, 'age', '-') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-500">Jenis kelamin</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-950">{{ data_get($consultation ?? null, 'gender', '-') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-500">Telepon</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-950">{{ data_get($consultation ?? null, 'phone', '-') }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-semibold text-slate-500">Alamat</dt>
                            <dd class="mt-1 text-sm leading-6 text-slate-950">{{ data_get($consultation ?? null, 'address', '-') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-500">Pemicu</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-950">{{ data_get($consultation ?? null, 'family_stressor', '-') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-500">Sekolah</dt>
                            <dd class="mt-1 text-sm font-medium text-slate-950">{{ data_get($consultation ?? null, 'school', '-') }}</dd>
                        </div>
                        @if (filled($additionalNotes))
                            <div class="sm:col-span-2">
                                <dt class="text-xs font-semibold text-slate-500">Catatan</dt>
                                <dd class="mt-1 text-sm leading-6 text-slate-950">{{ $additionalNotes }}</dd>
                            </div>
                        @endif
                    </dl>
                </section>
            </div>

            <div class="mt-6 grid gap-6 lg:grid-cols-[1.05fr_0.95fr]">
                <section class="rounded-lg border border-slate-200 bg-white p-5">
                    <h2 class="flex items-center gap-2 text-base font-semibold text-slate-950">
                        <x-icon name="list-check" class="size-5 text-teal-700" />
                        Gejala terpilih
                    </h2>
                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                        @forelse ($selectedSymptoms as $symptom)
                            <div class="rounded-md border border-slate-200 bg-slate-50 p-3">
                                <p class="text-sm leading-5 text-slate-900">{{ data_get($symptom, 'name', data_get($symptom, 'description', $symptom)) }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">Gejala terpilih belum tersedia.</p>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-lg border border-slate-200 bg-slate-50 p-5">
                    <div class="grid gap-5 sm:grid-cols-[140px_1fr] sm:items-center">
                <img src="{{ asset('images/result-summary.svg') }}" alt="Ilustrasi ringkasan hasil skrining kesehatan mental" class="w-full rounded-md border border-slate-200 bg-white object-cover">
                        <div>
                            <h2 class="flex items-center gap-2 text-base font-semibold text-slate-950">
                                <x-icon name="heart-handshake" class="size-5 text-teal-700" />
                                Rekomendasi awal
                            </h2>
                            <p class="mt-3 text-sm leading-6 text-slate-600">
                                Simpan hasil ini, diskusikan dengan wali atau guru BK, dan hubungi tenaga profesional jika gejala terasa berat atau berlanjut.
                            </p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
@endsection
