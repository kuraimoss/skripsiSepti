@extends('layouts.app', ['title' => 'Info Gangguan'])

@php
    $disorders = is_object($disorders ?? null) && method_exists($disorders, 'items') ? collect($disorders->items()) : collect($disorders ?? []);
@endphp

@section('content')
    <section class="bg-white">
        <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold text-teal-700">Info gangguan</p>
                <h1 class="mt-3 text-3xl font-semibold tracking-normal text-slate-950">Data gangguan yang dipakai sistem.</h1>
                <p class="mt-3 text-sm leading-6 text-slate-600">Ringkasan singkat dari basis pengetahuan. Hasil sistem tetap perlu ditinjau tenaga profesional.</p>
            </div>

            <div class="mt-8 grid gap-4 lg:grid-cols-3">
                @forelse ($disorders as $disorder)
                    <article class="rounded-lg border border-slate-200 bg-white p-5">
                        <p class="text-xs font-semibold text-slate-500">{{ data_get($disorder, 'code', '-') }}</p>
                        <h2 class="mt-2 text-lg font-semibold text-slate-950">{{ data_get($disorder, 'name') }}</h2>
                        @if (filled(data_get($disorder, 'scientific_name')))
                            <p class="mt-1 text-sm text-slate-500">{{ data_get($disorder, 'scientific_name') }}</p>
                        @endif
                        <p class="mt-4 text-sm leading-6 text-slate-600">{{ data_get($disorder, 'description', 'Deskripsi belum tersedia.') }}</p>
                        @if (filled(data_get($disorder, 'solution')))
                            <div class="mt-4 rounded-md bg-slate-50 p-3 text-sm leading-6 text-slate-700">
                                {{ data_get($disorder, 'solution') }}
                            </div>
                        @endif
                    </article>
                @empty
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-5 text-sm text-slate-600">
                        Data gangguan belum tersedia.
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
