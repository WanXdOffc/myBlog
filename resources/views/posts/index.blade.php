@extends('layouts.blog')

@section('title', 'TechJournal — Minimalist Tech & Engineering Blog')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- ─── Hero Section ────────────────────────────────────────────────── -->
    <div class="py-12 md:py-16 text-center max-w-3xl mx-auto">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-cyan-50 dark:bg-cyan-950/80 border border-cyan-200 dark:border-cyan-800 text-cyan-700 dark:text-cyan-300 text-xs font-semibold uppercase tracking-wider mb-6">
            <span class="w-2 h-2 rounded-full bg-cyan-500 animate-pulse"></span>
            Personal Tech & Software Engineering Blog
        </div>
        
        <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight">
            Architecting <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-500 to-blue-600">Modern Web</span> Applications & Ideas.
        </h1>
        
        <p class="mt-4 text-lg text-slate-600 dark:text-slate-400 font-normal leading-relaxed">
            Catatan mendalam, panduan arsitektur Laravel, tips performa, serta ulasan teknologi modern terkini untuk para developer.
        </p>

        <!-- Search input mobile / hero fallback -->
        <div class="mt-8 md:hidden max-w-md mx-auto">
            <form action="{{ route('home') }}" method="GET" class="relative">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Search articles..." 
                    class="w-full pl-10 pr-4 py-2.5 text-sm bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 rounded-full border border-slate-200 dark:border-slate-800 focus:border-cyan-500 dark:focus:border-cyan-400 focus:outline-none shadow-sm"
                >
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </form>
        </div>
    </div>

    <!-- ─── Category Filter Pills ────────────────────────────────────────── -->
    <div class="mb-10 flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4 overflow-x-auto no-scrollbar">
        <div class="flex items-center gap-2 min-w-max">
            <!-- All Categories -->
            <a 
                href="{{ route('home') }}" 
                class="px-4 py-2 rounded-full text-xs font-semibold transition-all {{ !request('category') ? 'bg-cyan-600 text-white shadow-sm shadow-cyan-600/30' : 'bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700' }}"
            >
                All Articles
            </a>

            @foreach($categories as $category)
                <a 
                    href="{{ route('home', ['category' => $category->slug]) }}" 
                    class="px-4 py-2 rounded-full text-xs font-semibold transition-all {{ request('category') === $category->slug ? 'bg-cyan-600 text-white shadow-sm shadow-cyan-600/30' : 'bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700' }}"
                >
                    {{ $category->name }}
                    <span class="ml-1 opacity-70">({{ $category->posts_count }})</span>
                </a>
            @endforeach
        </div>

        @if(request('search') || request('category') || request('tag'))
            <a href="{{ route('home') }}" class="text-xs font-medium text-rose-500 hover:text-rose-600 min-w-max ml-4 flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Reset Filter
            </a>
        @endif
    </div>

    <!-- Search / Filter active indicator -->
    @if(request('search'))
        <div class="mb-6 p-4 rounded-2xl bg-cyan-50 dark:bg-cyan-950/40 border border-cyan-200 dark:border-cyan-900/60 flex items-center justify-between text-sm text-cyan-800 dark:text-cyan-300">
            <span>Result matching "<strong>{{ request('search') }}</strong>" ({{ $posts->total() }} articles found)</span>
            <a href="{{ route('home') }}" class="text-xs font-semibold hover:underline">Clear Search</a>
        </div>
    @endif

    <!-- ─── Post Grid ────────────────────────────────────────────────────── -->
    @if($posts->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($posts as $post)
                <article class="group flex flex-col bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800/80 overflow-hidden shadow-sm hover:shadow-xl hover:border-cyan-500/50 dark:hover:border-cyan-500/40 transition-all duration-300">
                    
                    <!-- Thumbnail -->
                    <a href="{{ route('posts.show', $post->slug) }}" class="relative aspect-video overflow-hidden bg-slate-100 dark:bg-slate-800">
                        @if($post->featured_image)
                            <img 
                                src="{{ asset('storage/' . $post->featured_image) }}" 
                                alt="{{ $post->title }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            >
                        @else
                            <!-- Fallback Gradient Thumbnail -->
                            <div class="w-full h-full bg-gradient-to-br from-slate-800 to-slate-950 flex flex-col items-center justify-center p-6 text-center group-hover:scale-105 transition-transform duration-500">
                                <div class="w-10 h-10 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center font-mono font-bold text-lg mb-2 border border-cyan-500/30">
                                    &lt;/&gt;
                                </div>
                                <span class="text-xs font-medium text-slate-400 uppercase tracking-widest">{{ $post->category->name ?? 'Article' }}</span>
                            </div>
                        @endif

                        <!-- Reading Time Pill -->
                        <div class="absolute bottom-3 right-3 px-2.5 py-1 rounded-full bg-slate-900/70 backdrop-blur text-white text-[10px] font-medium flex items-center gap-1">
                            <svg class="w-3 h-3 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $post->reading_time }} min read
                        </div>
                    </a>

                    <!-- Card Body -->
                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <!-- Category Badge & Date -->
                            <div class="flex items-center justify-between text-xs mb-3">
                                @if($post->category)
                                    <a href="{{ route('home', ['category' => $post->category->slug]) }}" class="font-semibold text-cyan-600 dark:text-cyan-400 hover:underline">
                                        {{ $post->category->name }}
                                    </a>
                                @else
                                    <span class="text-slate-400">Uncategorized</span>
                                @endif

                                <time datetime="{{ $post->published_at?->toIso8601String() }}" class="text-slate-400 dark:text-slate-500">
                                    {{ $post->published_at?->format('M d, Y') }}
                                </time>
                            </div>

                            <!-- Title -->
                            <h2 class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors line-clamp-2 leading-snug">
                                <a href="{{ route('posts.show', $post->slug) }}">
                                    {{ $post->title }}
                                </a>
                            </h2>

                            <!-- Summary -->
                            <p class="mt-3 text-sm text-slate-600 dark:text-slate-400 line-clamp-3 leading-relaxed">
                                {{ $post->summary ?? Str::limit(strip_tags($post->content), 120) }}
                            </p>
                        </div>

                        <!-- Card Footer / Author -->
                        <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-cyan-600 text-white font-bold flex items-center justify-center text-[10px]">
                                    {{ strtoupper(substr($post->user->name ?? 'A', 0, 1)) }}
                                </div>
                                <span class="font-medium text-slate-700 dark:text-slate-300">
                                    {{ $post->user->name ?? 'Admin' }}
                                </span>
                            </div>

                            <a href="{{ route('posts.show', $post->slug) }}" class="font-semibold text-cyan-600 dark:text-cyan-400 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                                Read
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                </article>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-12">
            {{ $posts->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="py-20 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 max-w-lg mx-auto">
            <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Belum Ada Artikel</h3>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Tidak ada artikel yang cocok dengan pencarian atau kategori ini.</p>
            <a href="{{ route('home') }}" class="inline-block mt-4 px-4 py-2 bg-cyan-600 text-white font-semibold text-xs rounded-full hover:bg-cyan-500 transition-colors">
                Lihat Semua Artikel
            </a>
        </div>
    @endif

</div>
@endsection
