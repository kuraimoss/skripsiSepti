@extends('layouts.admin', [
    'title' => 'Edit Gejala',
    'heading' => 'Edit Gejala',
    'subheading' => 'Perbarui kode, kategori, dan deskripsi gejala.',
])

@section('content')
    @include('admin.symptoms._form', ['symptom' => $symptom ?? null])
@endsection
