@php
    $appName = config('app.name', 'Sistem Pakar MentalCare');
    $pageTitle = trim(($title ?? '') . '');
    $publicNav = [
        ['label' => 'Beranda', 'route' => 'home', 'fallback' => url('/'), 'icon' => 'home'],
        ['label' => 'Konsultasi', 'route' => 'consultation.create', 'fallback' => '#konsultasi', 'icon' => 'clipboard-list'],
        ['label' => 'Info Penyakit', 'route' => 'info', 'fallback' => '#info', 'icon' => 'book-open'],
        ['label' => 'Profil Pakar', 'route' => 'expert-profile', 'fallback' => '#pakar', 'icon' => 'user-round'],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $pageTitle ? $pageTitle . ' - ' . $appName : $appName }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        @stack('head')
    </head>
    <body class="min-h-screen bg-white text-slate-950 antialiased">
        <div class="flex min-h-screen flex-col">
            <header class="no-print sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur">
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                    <a href="{{ \Illuminate\Support\Facades\Route::has('home') ? route('home') : url('/') }}" class="block min-w-0">
                        <span class="block truncate text-base font-semibold leading-5 text-slate-950">Sistem Pakar</span>
                        <span class="block truncate text-xs leading-4 text-slate-500">Deteksi awal kesehatan mental remaja</span>
                    </a>

                    <nav class="hidden items-center gap-1 md:flex" aria-label="Navigasi utama">
                        @foreach ($publicNav as $item)
                            @php
                                $href = \Illuminate\Support\Facades\Route::has($item['route']) ? route($item['route']) : $item['fallback'];
                                $active = request()->routeIs($item['route']);
                            @endphp
                            <a href="{{ $href }}" class="inline-flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition {{ $active ? 'bg-teal-50 text-teal-800' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">
                                <x-icon :name="$item['icon']" class="size-4" />
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </nav>

                    <div class="hidden items-center gap-2 sm:flex">
                        <a href="{{ \Illuminate\Support\Facades\Route::has('admin.dashboard') ? route('admin.dashboard') : '#' }}" class="inline-flex items-center gap-2 rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-100">
                            <x-icon name="shield-check" class="size-4" />
                            Admin
                        </a>
                        <a href="{{ \Illuminate\Support\Facades\Route::has('consultation.create') ? route('consultation.create') : '#konsultasi' }}" class="inline-flex items-center gap-2 rounded-md bg-teal-700 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-teal-800">
                            Mulai Konsultasi
                            <x-icon name="arrow-right" class="size-4" />
                        </a>
                    </div>
                </div>

                <nav class="mx-auto flex max-w-7xl gap-2 overflow-x-auto px-4 pb-3 sm:px-6 md:hidden" aria-label="Navigasi mobile">
                    @foreach ($publicNav as $item)
                        @php
                            $href = \Illuminate\Support\Facades\Route::has($item['route']) ? route($item['route']) : $item['fallback'];
                        @endphp
                        <a href="{{ $href }}" class="inline-flex shrink-0 items-center gap-2 rounded-md border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700">
                            <x-icon :name="$item['icon']" class="size-3.5" />
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>
            </header>

            <main class="flex-1">
                @if (session('status') || session('success'))
                    <div class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
                        <div class="rounded-lg border border-teal-200 bg-teal-50 px-4 py-3 text-sm font-medium text-teal-900">
                            {{ session('status') ?? session('success') }}
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
                        <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                            <p class="font-semibold">Periksa kembali isian berikut.</p>
                            <ul class="mt-2 list-disc space-y-1 pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>

            <footer class="no-print border-t border-slate-200 bg-white">
                <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-5 text-sm text-slate-500 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
                    <p>&copy; {{ date('Y') }} {{ $appName }}</p>
                    <p>
                        Deteksi awal, bukan diagnosis klinis.
                        <span class="block md:inline">Ilustrasi oleh <a href="https://storyset.com/medical" class="font-medium text-slate-600 underline decoration-slate-300 underline-offset-4 hover:text-slate-900" target="_blank" rel="noopener noreferrer">Storyset</a>.</span>
                    </p>
                </div>
            </footer>
        </div>
        @stack('scripts')
    </body>
</html>
