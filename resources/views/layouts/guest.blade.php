<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="google-site-verification" content="kqEhVNvUWQCklGA-taVSa5Md5SlUawyWmC3lbeEu_Lo" />

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer" />
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen bg-gradient-to-br from-emerald-950 via-green-950 to-emerald-900 flex items-center justify-center p-4">
            <div class="w-full max-w-md">
                <div class="bg-white/10 backdrop-blur-xl border border-white/10 shadow-2xl rounded-2xl overflow-hidden">
                    <div class="px-6 py-6 sm:px-8">
                        <a href="/" class="flex flex-col items-center text-center gap-3 mb-6">
                            <img src="{{ asset('img/lo.jpeg') }}" alt="Logo Kampus" class="h-20 w-auto max-w-[320px] rounded-2xl object-contain bg-white px-6 py-4 shadow-lg ring-1 ring-black/10" />
                            <div class="leading-tight">
                                <div class="text-white text-lg font-semibold">{{ config('app.name') }}</div>
                                <div class="text-emerald-100/80 text-sm mt-1">Sistem Informasi Akademik</div>
                            </div>
                        </a>
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
