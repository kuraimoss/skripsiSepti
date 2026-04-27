@extends('layouts.admin', [
    'title' => 'Tambah Gangguan',
    'heading' => 'Tambah Gangguan',
    'subheading' => 'Tambahkan hipotesis gangguan yang akan menjadi target perhitungan.',
])

@section('content')
    @include('admin.disorders._form')
@endsection
