@extends('layouts.app', ['title' => 'Profil Pakar'])

@php
    $expert = $expert ?? [
        'name' => 'Pakar Kesehatan Mental Remaja',
        'role' => 'Validator basis pengetahuan',
        'organization' => 'Program studi / sekolah mitra',
        'summary' => 'Pakar meninjau gejala, gangguan, nilai belief, dan rekomendasi agar sistem tetap sesuai untuk deteksi awal.',
    ];
@endphp

@section('content')
    <section class="bg-white">
        <div class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-8 rounded-lg border border-slate-200 bg-slate-50 p-6 md:p-8 lg:grid-cols-[1fr_320px] lg:items-center">
                <div>
                    <p class="flex items-center gap-2 text-sm font-semibold text-teal-700">
                        <x-icon name="user-round" class="size-4" />
                        Profil pakar
                    </p>
                    <h1 class="mt-3 text-2xl font-semibold tracking-normal text-slate-950 sm:text-3xl">{{ data_get($expert, 'name') }}</h1>
                    <p class="mt-2 text-sm font-medium text-slate-700">{{ data_get($expert, 'role') }} - {{ data_get($expert, 'organization') }}</p>
                    <p class="mt-5 max-w-3xl text-sm leading-7 text-slate-600">{{ data_get($expert, 'summary') }}</p>
                </div>
            <img src="{{ asset('images/expert-profile.svg') }}" alt="Ilustrasi pakar kesehatan memvalidasi basis pengetahuan" class="w-full rounded-lg border border-slate-200 bg-white object-cover shadow-sm">
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-3">
                <article class="rounded-lg border border-slate-200 bg-white p-5">
                    <div class="grid size-10 place-items-center rounded-md bg-teal-50 text-teal-800">
                        <x-icon name="activity" class="size-5" />
                    </div>
                    <h2 class="mt-4 text-base font-semibold text-slate-950">Gejala</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Meninjau indikator yang dipakai dalam konsultasi.</p>
                </article>
                <article class="rounded-lg border border-slate-200 bg-white p-5">
                    <div class="grid size-10 place-items-center rounded-md bg-amber-100 text-amber-900">
                        <x-icon name="chart-bar" class="size-5" />
                    </div>
                    <h2 class="mt-4 text-base font-semibold text-slate-950">Belief</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Memvalidasi nilai evidence untuk perhitungan.</p>
                </article>
                <article class="rounded-lg border border-slate-200 bg-white p-5">
                    <div class="grid size-10 place-items-center rounded-md bg-teal-50 text-teal-800">
                        <x-icon name="clipboard-check" class="size-5" />
                    </div>
                    <h2 class="mt-4 text-base font-semibold text-slate-950">Hasil</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Menjaga rekomendasi tetap mudah dipahami.</p>
                </article>
            </div>
        </div>
    </section>
@endsection
