<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'TechJournal') . ' — Software Engineering & Architecture')</title>
    <meta name="description" content="@yield('meta_description', 'Jurnal dan catatan seputar software engineering, web architecture, dan Laravel.')">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('title', 'TechJournal — Catatan tentang kode dan gagasan')">
    <meta property="og:description" content="@yield('meta_description', 'Jurnal dan catatan seputar software engineering, web architecture, dan Laravel.')">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:site_name" content="TechJournal">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'TechJournal — Catatan tentang kode dan gagasan')">
    <meta name="twitter:description" content="@yield('meta_description', 'Jurnal dan catatan seputar software engineering, web architecture, dan Laravel.')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/og-default.jpg'))">

    @include('feed::links')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Source+Serif+4:opsz,wght@8..60,400;8..60,500;8..60,600&family=JetBrains+Mono:ital,wght@0,400;0,500;0,700;1,400&display=swap" rel="stylesheet">

    <script>
        try {
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        } catch (error) {
            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.classList.add('dark');
            }
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-zinc-50 font-sans text-zinc-700 antialiased transition-colors duration-200 dark:bg-zinc-950 dark:text-zinc-300 selection:bg-zinc-300 selection:text-zinc-950 dark:selection:bg-zinc-700 dark:selection:text-white">
    <a href="#main-content" class="skip-link">Lewati ke konten utama</a>

    @include('layouts.partials.navbar')

    <main id="main-content" tabindex="-1" class="flex-grow">
        @yield('content')
    </main>

    @include('layouts.partials.footer')

    <script>
        function toggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            try {
                localStorage.theme = isDark ? 'dark' : 'light';
            } catch (error) {
                console.warn('Preferensi tema tidak dapat disimpan pada browser ini.', error);
            }
        }
    </script>

    @stack('scripts')
</body>
</html>
