@extends('layouts.admin', [
    'title' => 'Tambah Gejala',
    'heading' => 'Tambah Gejala',
    'subheading' => 'Tambahkan gejala baru sebagai evidence pada form konsultasi pasien.',
])

@section('content')
    @include('admin.symptoms._form')
@endsection
