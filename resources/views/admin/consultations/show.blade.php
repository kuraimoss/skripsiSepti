@extends('layouts.admin', [
    'title' => 'Detail Konsultasi',
    'heading' => 'Detail Konsultasi',
    'subheading' => 'Tinjau identitas pasien, evidence terpilih, dan hasil perhitungan sistem.',
])

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
    $selectedSymptoms = collect($selectedSymptoms ?? data_get($consultation ?? null, 'symptoms', []));
    $consultationId = data_get($consultation ?? null, 'id');
    $printUrl = \Illuminate\Support\Facades\Route::has('consultation.print') && filled($consultationId) ? route('consultation.print', $consultationId) : '#';
@endphp

@section('header_actions')
    <a href="{{ $printUrl }}" class="inline-flex w-full items-center justify-center gap-2 rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 sm:w-auto">
        <x-icon name="printer" class="size-4" />
        Cetak
    </a>
@endsection

@section('content')
    <div class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="flex items-center gap-2 text-base font-semibold text-slate-950">
                <x-icon name="user-round" class="size-5 text-teal-700" />
                Identitas pasien
            </h2>
            <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Nama</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-950">{{ data_get($consultation ?? null, 'patient_name', '-') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Usia</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-950">{{ data_get($consultation ?? null, 'age', '-') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Jenis kelamin</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-950">{{ data_get($consultation ?? null, 'gender', '-') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Telepon</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-950">{{ data_get($consultation ?? null, 'phone', '-') }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Alamat</dt>
                    <dd class="mt-1 text-sm leading-6 text-slate-700">{{ data_get($consultation ?? null, 'address', '-') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Sekolah</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-950">{{ data_get($consultation ?? null, 'school', '-') }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Konteks keluarga</dt>
                    <dd class="mt-1 text-sm leading-6 text-slate-700">{{ data_get($consultation ?? null, 'family_stressor', '-') }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Catatan</dt>
                    <dd class="mt-1 text-sm leading-6 text-slate-700">{{ data_get($consultation ?? null, 'notes', '-') }}</dd>
                </div>
            </dl>
        </section>

        <section class="rounded-lg border border-teal-200 bg-teal-50 p-5 shadow-sm">
            <p class="flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-teal-800">
                <x-icon name="chart-bar" class="size-4" />
                Hasil utama
            </p>
            <div class="mt-4 flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-semibold tracking-normal text-teal-950">{{ data_get($primaryResult, 'disorder.name', data_get($primaryResult, 'name', 'Hasil belum tersedia')) }}</h2>
                    <p class="mt-1 text-sm font-medium text-teal-800">Kode: {{ data_get($primaryResult, 'disorder.code', data_get($primaryResult, 'code', '-')) }}</p>
                </div>
                <span class="rounded-md bg-white px-3 py-1 text-sm font-semibold text-teal-800 shadow-sm">{{ $formatPercent(data_get($primaryResult, 'belief', data_get($primaryResult, 'confidence', data_get($primaryResult, 'percentage', 0)))) }}</span>
            </div>
            <div class="mt-6 h-3 overflow-hidden rounded-full bg-white">
                <div class="h-full rounded-full bg-teal-700" style="width: {{ $percentNumber(data_get($primaryResult, 'belief', data_get($primaryResult, 'confidence', data_get($primaryResult, 'percentage', 0)))) }}%"></div>
            </div>
            <p class="mt-4 text-sm leading-6 text-teal-900">{{ data_get($primaryResult, 'description', 'Interpretasi utama berdasarkan evidence yang dipilih pasien.') }}</p>
        </section>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
        <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 p-5">
                <h2 class="flex items-center gap-2 text-base font-semibold text-slate-950">
                    <x-icon name="clipboard-check" class="size-5 text-teal-700" />
                    Detail hasil
                </h2>
            </div>
            <div class="divide-y divide-slate-200">
                @forelse ($results as $result)
                    @php
                        $belief = data_get($result, 'belief', data_get($result, 'confidence', data_get($result, 'percentage', 0)));
                    @endphp
                    <div class="p-5">
                        <div class="flex justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-950">{{ data_get($result, 'disorder.name', data_get($result, 'name', '-')) }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ data_get($result, 'disorder.code', data_get($result, 'code', '-')) }}</p>
                            </div>
                            <p class="text-sm font-semibold text-slate-900">{{ $formatPercent($belief) }}</p>
                        </div>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-slate-800" style="width: {{ $percentNumber($belief) }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-sm text-slate-500">Detail hasil belum tersedia.</div>
                @endforelse
            </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="flex items-center gap-2 text-base font-semibold text-slate-950">
                <x-icon name="list-check" class="size-5 text-teal-700" />
                Gejala terpilih
            </h2>
            <div class="mt-4 space-y-3">
                @forelse ($selectedSymptoms as $symptom)
                    <div class="rounded-md border border-slate-200 bg-slate-50 p-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ data_get($symptom, 'code', data_get($symptom, 'id', '-')) }}</p>
                        <p class="mt-1 text-sm font-medium leading-6 text-slate-900">{{ data_get($symptom, 'name', data_get($symptom, 'description', $symptom)) }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Tidak ada data gejala terpilih.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
