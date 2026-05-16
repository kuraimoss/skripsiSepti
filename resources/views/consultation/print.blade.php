@php
    $appName = config('app.name', 'Sistem Pakar MentalCare');
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

    $results = collect($results ?? data_get($consultation ?? null, 'results', []));
    $primaryResult = $primaryResult ?? data_get($consultation ?? null, 'primary_result') ?? $results->first();
    $primaryName = data_get($consultation ?? null, 'display_result_name')
        ?: data_get($primaryResult, 'disorder.name', data_get($primaryResult, 'name', 'Hasil belum tersedia'));
    $selectedSymptoms = collect($selectedSymptoms ?? data_get($consultation ?? null, 'symptoms', []));
    $recommendations = collect($recommendations ?? data_get($primaryResult, 'recommendations', data_get($consultation ?? null, 'recommendations', [])));
    $createdAt = data_get($consultation ?? null, 'created_at');
    $createdLabel = $createdAt instanceof \DateTimeInterface ? $createdAt->format('d/m/Y H:i') : ($createdAt ?: date('d/m/Y H:i'));
    $additionalNotes = data_get($consultation ?? null, 'additional_notes');
    $confidence = data_get($consultation ?? null, 'display_confidence_percentage');
    $confidence = $confidence ?? data_get($primaryResult, 'belief', data_get($primaryResult, 'confidence', data_get($primaryResult, 'percentage', 0)));
    $confidenceScore = (float) $confidence;
    if ($confidenceScore > 1 && $confidenceScore <= 100) {
        $confidenceScore = $confidenceScore / 100;
    }
    $certaintyLabel = data_get($consultation ?? null, 'display_certainty_label') ?: match (true) {
        $confidenceScore >= 1.0 => 'Sangat Pasti',
        $confidenceScore >= 0.75 => 'Pasti',
        $confidenceScore >= 0.50 => 'Cukup Pasti',
        default => 'Kurang Pasti',
    };
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Cetak Hasil Konsultasi - {{ $appName }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-slate-100 text-slate-950 antialiased print:bg-white">
        <main class="mx-auto max-w-4xl bg-white px-6 py-8 shadow-sm print:max-w-none print:px-0 print:py-0 print:shadow-none">
            <div class="no-print mb-6 flex justify-end gap-3">
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 rounded-md bg-teal-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-teal-800">
                    <x-icon name="printer" class="size-4" />
                    Cetak
                </button>
                <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                    <x-icon name="arrow-right" class="size-4 rotate-180" />
                    Kembali
                </a>
            </div>

            <header class="border-b-2 border-slate-900 pb-5 text-center">
                <p class="text-sm font-semibold uppercase tracking-wide text-slate-600">Laporan hasil konsultasi</p>
                <h1 class="mt-2 text-2xl font-semibold tracking-normal">{{ $appName }}</h1>
                <p class="mt-2 text-sm text-slate-600">Deteksi awal gangguan kesehatan mental remaja akibat stres lingkungan keluarga</p>
            </header>

            <section class="mt-6 grid gap-4 sm:grid-cols-2">
                <div>
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Data pasien</h2>
                    <dl class="mt-3 space-y-2 text-sm">
                        <div class="flex justify-between gap-4"><dt>Nama</dt><dd class="font-semibold">{{ data_get($consultation ?? null, 'patient_name', '-') }}</dd></div>
                        <div class="flex justify-between gap-4"><dt>Usia</dt><dd class="font-semibold">{{ data_get($consultation ?? null, 'age', '-') }}</dd></div>
                        <div class="flex justify-between gap-4"><dt>Jenis kelamin</dt><dd class="font-semibold">{{ data_get($consultation ?? null, 'gender', '-') }}</dd></div>
                        <div class="flex justify-between gap-4"><dt>Telepon</dt><dd class="font-semibold">{{ data_get($consultation ?? null, 'phone', '-') }}</dd></div>
                        <div class="flex justify-between gap-4"><dt>Alamat</dt><dd class="font-semibold">{{ data_get($consultation ?? null, 'address', '-') }}</dd></div>
                        <div class="flex justify-between gap-4"><dt>Sekolah</dt><dd class="font-semibold">{{ data_get($consultation ?? null, 'school', '-') }}</dd></div>
                        <div class="flex justify-between gap-4"><dt>Orang tua/wali</dt><dd class="font-semibold">{{ data_get($consultation ?? null, 'parent_guardian', '-') }}</dd></div>
                    </dl>
                </div>
                <div>
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Konsultasi</h2>
                    <dl class="mt-3 space-y-2 text-sm">
                        <div class="flex justify-between gap-4"><dt>Tanggal</dt><dd class="font-semibold">{{ $createdLabel }}</dd></div>
                        <div class="flex justify-between gap-4"><dt>Pemicu stres</dt><dd class="font-semibold">{{ data_get($consultation ?? null, 'family_stressor', '-') }}</dd></div>
                        <div class="flex justify-between gap-4"><dt>Jumlah gejala</dt><dd class="font-semibold">{{ $selectedSymptoms->count() }}</dd></div>
                        @if (filled($additionalNotes))
                            <div class="flex justify-between gap-4"><dt>Catatan</dt><dd class="font-semibold">{{ $additionalNotes }}</dd></div>
                        @endif
                    </dl>
                </div>
            </section>

            <section class="print-break-inside-avoid mt-8 rounded-lg border border-slate-300 p-5">
                <p class="text-sm font-semibold uppercase tracking-wide text-slate-500">Hasil utama</p>
                <div class="mt-3 flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold">{{ $primaryName }}</h2>
                    </div>
                    <div class="text-right">
                        <p class="text-xl font-semibold">{{ $formatPercent($confidence) }}</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700">{{ $certaintyLabel }}</p>
                    </div>
                </div>
                <p class="mt-4 text-sm leading-6 text-slate-700">Tingkat kepastian: <span class="font-semibold">{{ $certaintyLabel }}</span>.</p>
            </section>

            <section class="mt-8">
                <h2 class="text-base font-semibold">Detail hasil perhitungan</h2>
                <div class="mt-3 overflow-hidden rounded-lg border border-slate-300">
                    <table class="min-w-full divide-y divide-slate-300 text-sm">
                        <thead class="bg-slate-100">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">Gangguan</th>
                                <th class="px-4 py-3 text-left font-semibold">Belief</th>
                                <th class="px-4 py-3 text-left font-semibold">Plausibility</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse ($results as $result)
                                <tr>
                                    <td class="px-4 py-3">{{ data_get($result, 'disorder.name', data_get($result, 'name', '-')) }}</td>
                                    <td class="px-4 py-3">{{ $formatPercent(data_get($result, 'belief', data_get($result, 'confidence', data_get($result, 'percentage', 0)))) }}</td>
                                    <td class="px-4 py-3">{{ data_get($result, 'plausibility') !== null ? $formatPercent(data_get($result, 'plausibility')) : '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-4 py-4 text-slate-500">Detail hasil belum tersedia.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="mt-8 grid gap-6 sm:grid-cols-2">
                <div class="print-break-inside-avoid">
                    <h2 class="text-base font-semibold">Gejala terpilih</h2>
                    <ul class="mt-3 space-y-2 text-sm leading-6">
                        @forelse ($selectedSymptoms as $symptom)
                            <li class="rounded-md border border-slate-200 px-3 py-2">{{ data_get($symptom, 'name', data_get($symptom, 'description', $symptom)) }}</li>
                        @empty
                            <li class="text-slate-500">Tidak ada data gejala.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="print-break-inside-avoid">
                    <h2 class="text-base font-semibold">Rekomendasi awal</h2>
                    <ul class="mt-3 space-y-2 text-sm leading-6">
                        @forelse ($recommendations as $recommendation)
                            <li class="rounded-md border border-slate-200 px-3 py-2">{{ is_scalar($recommendation) ? $recommendation : data_get($recommendation, 'text', '-') }}</li>
                        @empty
                            <li class="rounded-md border border-slate-200 px-3 py-2">Lakukan konsultasi lanjutan dengan tenaga profesional jika gejala berlanjut.</li>
                        @endforelse
                    </ul>
                </div>
            </section>

            <footer class="mt-10 border-t border-slate-300 pt-4 text-xs leading-5 text-slate-500">
                Laporan ini merupakan hasil deteksi awal berbasis sistem pakar dan tidak menggantikan diagnosis psikolog, psikiater, atau tenaga kesehatan profesional.
            </footer>
        </main>
    </body>
</html>
