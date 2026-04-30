@extends('layouts.app', ['title' => 'Konsultasi'])

@php
    $symptoms = is_object($symptoms ?? null) && method_exists($symptoms, 'items') ? collect($symptoms->items()) : collect($symptoms ?? []);
    $groupedSymptoms = $symptoms->groupBy(fn ($symptom) => data_get($symptom, 'category', 'Gejala'));
    $selectedSymptoms = collect(old('symptoms', []))->map(fn ($value) => (string) $value)->all();
    $submitUrl = \Illuminate\Support\Facades\Route::has('consultation.store') ? route('consultation.store') : '#';
@endphp

@section('content')
    <section class="bg-white">
        <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-[1fr_360px] lg:items-center">
                <div class="max-w-3xl">
                    <p class="flex items-center gap-2 text-sm font-semibold text-teal-700">
                        <x-icon name="clipboard-list" class="size-4" />
                        Konsultasi
                    </p>
                    <h1 class="mt-3 text-2xl font-semibold leading-tight tracking-normal text-slate-950 sm:text-3xl">Isi data singkat dan pilih gejala.</h1>
                    <p class="mt-3 text-sm leading-6 text-slate-600">Hasil hanya sebagai deteksi awal, bukan diagnosis klinis.</p>
                </div>
            <img src="{{ asset('images/consultation-form.svg') }}" alt="Ilustrasi form skrining kesehatan dan stetoskop" class="w-full rounded-lg border border-slate-200 bg-slate-50 object-cover shadow-sm">
            </div>

            <form method="POST" action="{{ $submitUrl }}" class="mt-8 space-y-6">
                @csrf

                <section class="rounded-lg border border-slate-200 bg-slate-50 p-5">
                    <h2 class="flex items-center gap-2 text-base font-semibold text-slate-950">
                        <x-icon name="user-round" class="size-5 text-teal-700" />
                        Data pasien
                    </h2>
                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-slate-700">Nama pasien</label>
                            <input id="name" name="name" value="{{ old('name', old('patient_name')) }}" type="text" autocomplete="name" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-950 shadow-sm transition placeholder:text-slate-400 focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20" placeholder="Nama lengkap">
                            @error('name') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="age" class="block text-sm font-semibold text-slate-700">Usia</label>
                            <input id="age" name="age" value="{{ old('age') }}" type="number" min="10" max="24" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-950 shadow-sm transition placeholder:text-slate-400 focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20" placeholder="Contoh: 16">
                            @error('age') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="gender" class="block text-sm font-semibold text-slate-700">Jenis kelamin</label>
                            <select id="gender" name="gender" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-950 shadow-sm transition focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20">
                                <option value="">Pilih jenis kelamin</option>
                                <option value="laki-laki" @selected(in_array(old('gender'), ['laki-laki', 'L'], true))>Laki-laki</option>
                                <option value="perempuan" @selected(in_array(old('gender'), ['perempuan', 'P'], true))>Perempuan</option>
                            </select>
                            @error('gender') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-semibold text-slate-700">Telepon</label>
                            <input id="phone" name="phone" value="{{ old('phone') }}" type="text" inputmode="tel" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-950 shadow-sm transition placeholder:text-slate-400 focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20" placeholder="Opsional">
                            @error('phone') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-semibold text-slate-700">Alamat</label>
                            <textarea id="address" name="address" rows="2" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-950 shadow-sm transition placeholder:text-slate-400 focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20" placeholder="Opsional">{{ old('address') }}</textarea>
                            @error('address') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="school" class="block text-sm font-semibold text-slate-700">Sekolah / institusi</label>
                            <input id="school" name="school" value="{{ old('school') }}" type="text" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-950 shadow-sm transition placeholder:text-slate-400 focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20" placeholder="Opsional">
                            @error('school') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                <section class="rounded-lg border border-slate-200 bg-white p-5">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <h2 class="flex items-center gap-2 text-base font-semibold text-slate-950">
                            <x-icon name="list-check" class="size-5 text-teal-700" />
                            Gejala
                        </h2>
                        <span class="text-sm text-slate-500">{{ $symptoms->count() }} gejala tersedia</span>
                    </div>

                    <div class="mt-5 space-y-5">
                        @foreach ($groupedSymptoms as $category => $items)
                            <section class="print-break-inside-avoid">
                                <h3 class="text-sm font-semibold text-slate-700">{{ $category }}</h3>
                                <div class="mt-3 grid gap-3 md:grid-cols-2">
                                    @foreach ($items as $symptom)
                                        @php
                                            $value = (string) data_get($symptom, 'id', data_get($symptom, 'code'));
                                        @endphp
                                        <label class="flex min-h-16 cursor-pointer gap-3 rounded-md border border-slate-200 bg-slate-50 p-3 transition hover:border-teal-300 hover:bg-teal-50">
                                            <input type="checkbox" name="symptoms[]" value="{{ $value }}" @checked(in_array($value, $selectedSymptoms, true)) class="mt-1 size-4 rounded border-slate-300 text-teal-700 focus:ring-teal-600">
                                            <span>
                                                <span class="block text-sm font-medium leading-5 text-slate-900">{{ data_get($symptom, 'name', data_get($symptom, 'description')) }}</span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </section>
                        @endforeach
                    </div>
                    @error('symptoms') <p class="mt-3 text-sm text-rose-700">{{ $message }}</p> @enderror
                </section>

                <section class="rounded-lg border border-slate-200 bg-slate-50 p-5">
                    <h2 class="flex items-center gap-2 text-base font-semibold text-slate-950">
                        <x-icon name="heart-handshake" class="size-5 text-teal-700" />
                        Konteks keluarga
                    </h2>
                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="parent_guardian" class="block text-sm font-semibold text-slate-700">Orang tua/wali</label>
                            <input id="parent_guardian" name="parent_guardian" value="{{ old('parent_guardian') }}" type="text" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-950 shadow-sm transition placeholder:text-slate-400 focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20" placeholder="Opsional">
                        </div>

                        <div>
                            <label for="family_stressor" class="block text-sm font-semibold text-slate-700">Pemicu dominan</label>
                            <select id="family_stressor" name="family_stressor" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-950 shadow-sm transition focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20">
                                <option value="">Pilih konteks</option>
                                <option value="konflik" @selected(old('family_stressor') === 'konflik')>Konflik keluarga</option>
                                <option value="komunikasi" @selected(old('family_stressor') === 'komunikasi')>Komunikasi kurang baik</option>
                                <option value="ekonomi" @selected(old('family_stressor') === 'ekonomi')>Tekanan ekonomi keluarga</option>
                                <option value="pengasuhan" @selected(old('family_stressor') === 'pengasuhan')>Pola asuh menekan</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="notes" class="block text-sm font-semibold text-slate-700">Catatan</label>
                        <textarea id="notes" name="notes" rows="3" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-950 shadow-sm transition placeholder:text-slate-400 focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20" placeholder="Opsional">{{ old('notes') }}</textarea>
                    </div>
                </section>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:justify-end">
                    <a href="{{ \Illuminate\Support\Facades\Route::has('home') ? route('home') : url('/') }}" class="inline-flex items-center justify-center gap-2 rounded-md border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                        <x-icon name="home" class="size-4" />
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-teal-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-teal-800">
                        Proses Konsultasi
                        <x-icon name="arrow-right" class="size-4" />
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection
