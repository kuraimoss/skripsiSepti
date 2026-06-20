@extends('layouts.admin', [
    'title' => 'Detail Aturan',
    'heading' => 'Detail Aturan',
    'subheading' => 'Relasi gejala, gangguan, dan nilai belief.',
])

@section('header_actions')
    <a href="{{ route('admin.knowledge-rules.edit', $rule) }}" class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-teal-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-teal-800 sm:w-auto">
        <x-icon name="edit-3" class="size-4" />
        Edit
    </a>
@endsection

@php
    $formatBelief = fn ($value) => is_numeric($value) ? rtrim(rtrim(number_format((float) $value, 4, '.', ''), '0'), '.') : '-';
@endphp

@section('content')
    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="mb-5 flex items-center gap-2 text-base font-semibold text-slate-950">
            <x-icon name="network" class="size-5 text-teal-700" />
            Informasi aturan
        </h2>
        <dl class="grid gap-5 sm:grid-cols-2">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Kode aturan</dt>
                <dd class="mt-1 text-sm font-semibold text-slate-950">{{ $rule->rule_code }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Belief</dt>
                <dd class="mt-1 text-sm font-semibold text-slate-950">{{ $formatBelief($rule->belief) }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Gejala</dt>
                <dd class="mt-1 text-sm text-slate-700">{{ $rule->symptom?->code }} - {{ $rule->symptom?->name }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Gangguan</dt>
                <dd class="mt-1 text-sm text-slate-700">{{ $rule->mentalDisorder?->code }} - {{ $rule->mentalDisorder?->name }}</dd>
            </div>
        </dl>
    </section>
@endsection
