<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Title & SEO Meta Tags -->
    <title>@yield('title', config('app.name', 'TechJournal') . ' — Software Engineering & Architecture')</title>
    <meta name="description" content="@yield('meta_description', 'Jurnal & catatan seputar software engineering, web architecture, Laravel, dan ekosistem modern tech.')">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <!-- Open Graph / Social Media Meta Tags -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('title', 'TechJournal — Minimalist Tech & Engineering Blog')">
    <meta property="og:description" content="@yield('meta_description', 'Jurnal & catatan seputar software engineering, web architecture, Laravel, dan ekosistem modern tech.')">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:site_name" content="TechJournal">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'TechJournal — Minimalist Tech & Engineering Blog')">
    <meta name="twitter:description" content="@yield('meta_description', 'Jurnal & catatan seputar software engineering, web architecture, Laravel, dan ekosistem modern tech.')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/og-default.jpg'))">

    <!-- RSS Feed Link -->
    @include('feed::links')

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:ital,wght@0,400;0,500;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Syntax Highlighting (Highlight.js CDN) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">

    <!-- Theme Inline Script (Prevent FOUC) -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-slate-100 font-sans antialiased min-h-full flex flex-col transition-colors duration-200 selection:bg-cyan-500 selection:text-white">

    <!-- Navigation Bar -->
    @include('layouts.partials.navbar')

    <!-- Main Page Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    @include('layouts.partials.footer')

    <!-- Highlight.js CDN JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/php.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/bash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/sql.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/json.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize syntax highlighting
            hljs.highlightAll();

            // Setup Copy Code buttons
            document.querySelectorAll('.prose pre').forEach((preBlock) => {
                preBlock.classList.add('relative', 'group');

                const button = document.createElement('button');
                button.className = 'copy-code-button absolute top-3 right-3 px-2.5 py-1 text-xs font-medium text-slate-300 bg-slate-800/80 hover:bg-slate-700/90 rounded-md border border-slate-700 backdrop-blur opacity-0 group-hover:opacity-100 transition-all duration-200 flex items-center gap-1.5 cursor-pointer';
                button.innerHTML = `
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <span>Copy</span>
                `;

                button.addEventListener('click', () => {
                    const code = preBlock.querySelector('code')?.innerText || preBlock.innerText;
                    navigator.clipboard.writeText(code).then(() => {
                        const span = button.querySelector('span');
                        span.textContent = 'Copied!';
                        button.classList.add('text-emerald-400', 'border-emerald-500/50');
                        setTimeout(() => {
                            span.textContent = 'Copy';
                            button.classList.remove('text-emerald-400', 'border-emerald-500/50');
                        }, 2000);
                    });
                });

                preBlock.appendChild(button);
            });
        });

        // Theme Toggle Function
        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
        }
    </script>

    @stack('scripts')
</body>
</html>
