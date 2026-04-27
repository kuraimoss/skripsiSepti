@extends('layouts.admin', [
    'title' => 'Detail Aturan',
    'heading' => 'Detail Aturan',
    'subheading' => 'Relasi gejala, gangguan, dan nilai belief.',
])

@section('header_actions')
    <a href="{{ route('admin.knowledge-rules.edit', $rule) }}" class="inline-flex items-center justify-center rounded-md bg-teal-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-teal-800">Edit</a>
@endsection

@section('content')
    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <dl class="grid gap-5 sm:grid-cols-2">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Kode aturan</dt>
                <dd class="mt-1 text-sm font-semibold text-slate-950">{{ $rule->rule_code }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Belief</dt>
                <dd class="mt-1 text-sm font-semibold text-slate-950">{{ $rule->belief }}</dd>
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
