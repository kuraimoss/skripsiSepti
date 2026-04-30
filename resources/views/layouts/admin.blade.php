@php
    $appName = config('app.name', 'Sistem Pakar MentalCare');
    $pageTitle = trim(($title ?? 'Admin') . '');
    $adminNav = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'layout-dashboard'],
        ['label' => 'Gejala', 'route' => 'admin.symptoms.index', 'icon' => 'activity'],
        ['label' => 'Gangguan', 'route' => 'admin.disorders.index', 'icon' => 'brain'],
        ['label' => 'Basis Pengetahuan', 'route' => 'admin.knowledge-rules.index', 'icon' => 'network'],
        ['label' => 'Konsultasi', 'route' => 'admin.consultations.index', 'icon' => 'history'],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $pageTitle ? $pageTitle . ' - Admin - ' . $appName : 'Admin - ' . $appName }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        @stack('head')
    </head>
    <body class="min-h-screen bg-white text-slate-950 antialiased">
        <div class="min-h-screen lg:flex">
            <aside class="no-print border-b border-slate-200 bg-slate-50 text-slate-950 lg:fixed lg:inset-y-0 lg:left-0 lg:w-72 lg:border-b-0 lg:border-r">
                <div class="flex items-center justify-between gap-3 px-4 py-4 lg:block lg:px-6">
                    <a href="{{ \Illuminate\Support\Facades\Route::has('admin.dashboard') ? route('admin.dashboard') : '#' }}" class="block">
                        <span class="block text-sm font-semibold">Admin Sistem Pakar</span>
                        <span class="block text-xs text-slate-500">Manajemen pengetahuan</span>
                    </a>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('home') ? route('home') : url('/') }}" class="inline-flex items-center gap-2 rounded-md border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100 lg:mt-6">
                        <x-icon name="external-link" class="size-3.5" />
                        Lihat Situs
                    </a>
                </div>

                <nav class="mobile-nav-scroll flex snap-x gap-2 overflow-x-auto px-4 pb-4 lg:block lg:space-y-1 lg:px-4" aria-label="Navigasi admin">
                    @foreach ($adminNav as $item)
                        @php
                            $href = \Illuminate\Support\Facades\Route::has($item['route']) ? route($item['route']) : '#';
                            $active = request()->routeIs($item['route']) || request()->routeIs(str_replace('.index', '.*', $item['route']));
                        @endphp
                        <a href="{{ $href }}" class="inline-flex shrink-0 snap-start items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition lg:flex {{ $active ? 'bg-teal-700 text-white' : 'text-slate-600 hover:bg-white hover:text-slate-950' }}">
                            <x-icon :name="$item['icon']" class="size-4" />
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>
            </aside>

            <div class="min-w-0 flex-1 lg:pl-72">
                <header class="no-print border-b border-slate-200 bg-white">
                    <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-5 sm:px-6 lg:px-8">
                        <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Panel Admin</p>
                        <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                            <div>
                                <h1 class="text-xl font-semibold tracking-normal text-slate-950 sm:text-2xl">{{ $heading ?? $pageTitle }}</h1>
                                @isset($subheading)
                                    <p class="mt-1 max-w-3xl text-sm leading-6 text-slate-500">{{ $subheading }}</p>
                                @endisset
                            </div>
                            @yield('header_actions')
                        </div>
                    </div>
                </header>

                <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    @if (session('status') || session('success'))
                        <div class="mb-5 flex items-center gap-3 rounded-lg border border-teal-200 bg-teal-50 px-4 py-3 text-sm font-medium text-teal-900">
                            <x-icon name="check-circle" class="size-4 shrink-0" />
                            {{ session('status') ?? session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                            <p class="font-semibold">Ada isian yang perlu diperbaiki.</p>
                            <ul class="mt-2 list-disc space-y-1 pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
