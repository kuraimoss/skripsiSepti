@props(['name', 'strokeWidth' => 2])

<svg {{ $attributes->merge(['class' => 'size-5']) }} xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $strokeWidth }}" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('activity')
            <path d="M22 12h-4l-3 8-6-16-3 8H2" />
            @break

        @case('arrow-right')
            <path d="M5 12h14" />
            <path d="m12 5 7 7-7 7" />
            @break

        @case('book-open')
            <path d="M12 7v14" />
            <path d="M3 5a2 2 0 0 1 2-2h5a2 2 0 0 1 2 2v16a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2z" />
            <path d="M21 5a2 2 0 0 0-2-2h-5a2 2 0 0 0-2 2v16a2 2 0 0 1 2-2h5a2 2 0 0 1 2 2z" />
            @break

        @case('brain')
            <path d="M9 3a3 3 0 0 0-3 3v1a3 3 0 0 0 0 6v1a3 3 0 0 0 3 3" />
            <path d="M15 3a3 3 0 0 1 3 3v1a3 3 0 0 1 0 6v1a3 3 0 0 1-3 3" />
            <path d="M9 3v18" />
            <path d="M15 3v18" />
            <path d="M9 8h6" />
            <path d="M9 13h6" />
            @break

        @case('chart-bar')
            <path d="M3 3v18h18" />
            <path d="M8 17V9" />
            <path d="M13 17V5" />
            <path d="M18 17v-6" />
            @break

        @case('check-circle')
            <path d="M9 12l2 2 4-4" />
            <circle cx="12" cy="12" r="9" />
            @break

        @case('clipboard-check')
            <path d="M9 5h6" />
            <path d="M9 3h6v4H9z" />
            <path d="M8 5H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" />
            <path d="m9 14 2 2 4-5" />
            @break

        @case('clipboard-list')
            <path d="M9 5h6" />
            <path d="M9 3h6v4H9z" />
            <path d="M8 5H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" />
            <path d="M8 12h8" />
            <path d="M8 16h6" />
            @break

        @case('database')
            <ellipse cx="12" cy="5" rx="8" ry="3" />
            <path d="M4 5v6c0 1.7 3.6 3 8 3s8-1.3 8-3V5" />
            <path d="M4 11v6c0 1.7 3.6 3 8 3s8-1.3 8-3v-6" />
            @break

        @case('edit-3')
            <path d="M12 20h9" />
            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z" />
            @break

        @case('external-link')
            <path d="M15 3h6v6" />
            <path d="M10 14 21 3" />
            <path d="M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5" />
            @break

        @case('filter')
            <path d="M3 5h18" />
            <path d="M6 12h12" />
            <path d="M10 19h4" />
            @break

        @case('heart-handshake')
            <path d="M19 14c1.5-1.4 3-3.2 3-5.4A5.1 5.1 0 0 0 12 6.8 5.1 5.1 0 0 0 2 8.6c0 2.2 1.5 4 3 5.4l7 6z" />
            <path d="M8 13h3l2 2 3-3" />
            @break

        @case('history')
            <path d="M3 12a9 9 0 1 0 3-6.7" />
            <path d="M3 3v6h6" />
            <path d="M12 7v5l3 2" />
            @break

        @case('home')
            <path d="m3 10 9-7 9 7" />
            <path d="M5 10v10h14V10" />
            <path d="M10 20v-6h4v6" />
            @break

        @case('layout-dashboard')
            <rect width="7" height="9" x="3" y="3" rx="1" />
            <rect width="7" height="5" x="14" y="3" rx="1" />
            <rect width="7" height="9" x="14" y="12" rx="1" />
            <rect width="7" height="5" x="3" y="16" rx="1" />
            @break

        @case('list-check')
            <path d="m3 7 2 2 4-4" />
            <path d="M11 7h10" />
            <path d="m3 17 2 2 4-4" />
            <path d="M11 17h10" />
            @break

        @case('log-in')
            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
            <path d="M10 17l5-5-5-5" />
            <path d="M15 12H3" />
            @break

        @case('network')
            <rect width="6" height="6" x="3" y="3" rx="1" />
            <rect width="6" height="6" x="15" y="3" rx="1" />
            <rect width="6" height="6" x="9" y="15" rx="1" />
            <path d="M6 9v3h6" />
            <path d="M18 9v3h-6" />
            <path d="M12 12v3" />
            @break

        @case('plus')
            <path d="M12 5v14" />
            <path d="M5 12h14" />
            @break

        @case('printer')
            <path d="M6 9V2h12v7" />
            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
            <path d="M6 14h12v8H6z" />
            @break

        @case('refresh-cw')
            <path d="M21 12a9 9 0 0 1-15.3 6.4L3 16" />
            <path d="M3 21v-5h5" />
            <path d="M3 12a9 9 0 0 1 15.3-6.4L21 8" />
            <path d="M16 8h5V3" />
            @break

        @case('search')
            <circle cx="11" cy="11" r="8" />
            <path d="m21 21-4.3-4.3" />
            @break

        @case('shield-check')
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10" />
            <path d="m9 12 2 2 4-5" />
            @break

        @case('trash-2')
            <path d="M3 6h18" />
            <path d="M8 6V4h8v2" />
            <path d="M19 6l-1 15H6L5 6" />
            <path d="M10 11v6" />
            <path d="M14 11v6" />
            @break

        @case('user-round')
            <circle cx="12" cy="8" r="5" />
            <path d="M20 21a8 8 0 0 0-16 0" />
            @break

        @case('users')
            <path d="M16 21a6 6 0 0 0-12 0" />
            <circle cx="10" cy="8" r="4" />
            <path d="M22 21a5 5 0 0 0-4-4.9" />
            <path d="M17 4.1a4 4 0 0 1 0 7.8" />
            @break

        @default
            <circle cx="12" cy="12" r="9" />
    @endswitch
</svg>
