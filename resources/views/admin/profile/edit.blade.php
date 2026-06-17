@extends('layouts.admin', [
    'title' => 'Profil Admin',
    'heading' => 'Profil Admin',
    'subheading' => 'Ubah email dan password akun admin yang sedang digunakan.',
])

@section('content')
    <form method="POST" action="{{ route('admin.profile.update') }}" class="max-w-3xl space-y-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        @csrf
        @method('PUT')

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700">Nama admin</label>
                <input id="name" value="{{ $admin->name }}" type="text" disabled class="mt-2 block w-full rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-500 shadow-sm">
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700">Email login</label>
                <input id="email" name="email" value="{{ old('email', $admin->email) }}" type="email" autocomplete="email" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20">
                @error('email') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-sm leading-6 text-amber-900">
            Isi password baru hanya jika ingin mengganti password. Password saat ini tetap wajib diisi untuk menyimpan perubahan akun.
        </div>

        <div>
            <label for="current_password" class="block text-sm font-semibold text-slate-700">Password saat ini</label>
            <input id="current_password" name="current_password" type="password" autocomplete="current-password" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20">
            @error('current_password') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="password" class="block text-sm font-semibold text-slate-700">Password baru</label>
                <input id="password" name="password" type="password" autocomplete="new-password" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20">
                @error('password') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-slate-700">Konfirmasi password baru</label>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20">
            </div>
        </div>

        <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                <x-icon name="arrow-right" class="size-4 rotate-180" />
                Batal
            </a>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-teal-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-teal-800">
                <x-icon name="check-circle" class="size-4" />
                Simpan Akun
            </button>
        </div>
    </form>
@endsection
