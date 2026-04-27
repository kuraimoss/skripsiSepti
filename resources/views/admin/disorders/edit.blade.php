@extends('layouts.admin', [
    'title' => 'Edit Gangguan',
    'heading' => 'Edit Gangguan',
    'subheading' => 'Perbarui detail gangguan dan rekomendasi awal.',
])

@section('content')
    @include('admin.disorders._form', ['disorder' => $disorder ?? null])
@endsection
