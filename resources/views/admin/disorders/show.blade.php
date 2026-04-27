@extends('layouts.admin', [
    'title' => 'Detail Gangguan',
    'heading' => 'Detail Gangguan',
    'subheading' => 'Detail hipotesis gangguan dan solusi awal.',
])

@section('header_actions')
    <a href="{{ route('admin.disorders.edit', $disorder) }}" class="inline-flex items-center justify-center rounded-md bg-teal-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-teal-800">Edit</a>
@endsection

@section('content')
    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <dl class="grid gap-5 sm:grid-cols-2">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Kode</dt>
                <dd class="mt-1 text-sm font-semibold text-slate-950">{{ $disorder->code }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Nama ilmiah</dt>
                <dd class="mt-1 text-sm font-semibold text-slate-950">{{ $disorder->scientific_name ?: '-' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Nama gangguan</dt>
                <dd class="mt-1 text-sm text-slate-700">{{ $disorder->name }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Solusi</dt>
                <dd class="mt-1 text-sm leading-6 text-slate-700">{{ $disorder->solution ?: '-' }}</dd>
            </div>
        </dl>
    </section>
@endsection
