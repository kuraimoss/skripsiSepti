@extends('layouts.app', ['title' => 'Login Admin'])

@section('content')
    <section class="bg-white">
        <div class="mx-auto grid min-h-[calc(100vh-160px)] max-w-5xl items-center gap-8 px-4 py-12 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:px-8">
            <div>
            <img src="{{ asset('images/admin-login.svg') }}" alt="Ilustrasi akses aman admin kesehatan" class="mb-6 w-full rounded-lg border border-slate-200 bg-slate-50 object-cover shadow-sm">
                <p class="flex items-center gap-2 text-sm font-semibold text-teal-700">
                    <x-icon name="shield-check" class="size-4" />
                    Panel Admin
                </p>
                <h1 class="mt-3 max-w-xl text-3xl font-semibold leading-tight tracking-normal text-slate-950">Masuk untuk mengelola sistem.</h1>
                <p class="mt-4 max-w-xl text-sm leading-6 text-slate-600">Gunakan akun admin yang tersedia di database.</p>
            </div>

            <form method="POST" action="{{ route('login.store') }}" class="rounded-lg border border-slate-200 bg-slate-50 p-6">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700">Email</label>
                    <input id="email" name="email" value="{{ old('email', 'test@example.com') }}" type="email" autocomplete="email" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-950 shadow-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20">
                    @error('email') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
                </div>

                <div class="mt-4">
                    <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
                    <input id="password" name="password" value="password" type="password" autocomplete="current-password" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-950 shadow-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20">
                    @error('password') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
                </div>

                <label class="mt-4 flex items-center gap-3 text-sm font-medium text-slate-700">
                    <input type="checkbox" name="remember" value="1" class="size-4 rounded border-slate-300 text-teal-700 focus:ring-teal-600">
                    Ingat sesi login
                </label>

                <button type="submit" class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-md bg-teal-700 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-teal-800">
                    <x-icon name="log-in" class="size-4" />
                    Masuk Admin
                </button>
            </form>
        </div>
    </section>
@endsection
