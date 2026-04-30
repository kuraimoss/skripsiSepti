@extends('layouts.admin', [
    'title' => 'Detail Gangguan',
    'heading' => 'Detail Gangguan',
    'subheading' => 'Detail hipotesis gangguan dan solusi awal.',
])

@section('header_actions')
    <a href="{{ route('admin.disorders.edit', $disorder) }}" class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-teal-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-teal-800 sm:w-auto">
        <x-icon name="edit-3" class="size-4" />
        Edit
    </a>
@endsection

@section('content')
    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="mb-5 flex items-center gap-2 text-base font-semibold text-slate-950">
            <x-icon name="brain" class="size-5 text-teal-700" />
            Informasi gangguan
        </h2>
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
