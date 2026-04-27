@extends('layouts.admin', [
    'title' => 'Edit Aturan',
    'heading' => 'Edit Aturan',
    'subheading' => 'Perbarui relasi evidence dan nilai keyakinan.',
])

@section('content')
    @include('admin.knowledge-rules._form', ['rule' => $rule ?? null, 'symptoms' => $symptoms ?? [], 'disorders' => $disorders ?? []])
@endsection
