@extends('layouts.admin', [
    'title' => 'Tambah Aturan',
    'heading' => 'Tambah Aturan',
    'subheading' => 'Hubungkan gejala dengan gangguan dan masukkan nilai belief dari pakar.',
])

@section('content')
    @include('admin.knowledge-rules._form', ['symptoms' => $symptoms ?? [], 'disorders' => $disorders ?? []])
@endsection
