@extends('layouts.blog')

@section('title', 'Bookmark Artikel Saya — TechJournal')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-8">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-cyan-600 dark:text-slate-400 dark:hover:text-cyan-400 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Beranda
        </a>
    </div>

    <!-- Header Section -->
    <div class="mb-10 flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-6">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-100 dark:bg-cyan-950 text-cyan-700 dark:text-cyan-300 text-xs font-semibold mb-2">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/>
                </svg>
                Saved Articles
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">Bookmark Saya</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Daftar artikel yang telah Anda simpan untuk dibaca kembali.</p>
        </div>
    </div>

    <!-- Bookmarked Posts Grid -->
    @if($posts->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($posts as $post)
                <article class="group flex flex-col bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800/80 overflow-hidden shadow-sm hover:shadow-xl hover:border-cyan-500/50 transition-all duration-300">
                    
                    <a href="{{ route('posts.show', $post->slug) }}" class="relative aspect-video overflow-hidden bg-slate-100 dark:bg-slate-800">
                        @if($post->featured_image)
                            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-slate-800 to-slate-950 flex flex-col items-center justify-center p-6 text-center">
                                <div class="w-10 h-10 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center font-mono font-bold text-lg mb-2">
                                    &lt;/&gt;
                                </div>
                                <span class="text-xs font-medium text-slate-400 uppercase tracking-widest">{{ $post->category->name ?? 'Article' }}</span>
                            </div>
                        @endif
                    </a>

                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between text-xs mb-3">
                                <span class="font-semibold text-cyan-600 dark:text-cyan-400">{{ $post->category->name ?? 'Uncategorized' }}</span>
                                <time class="text-slate-400">{{ $post->published_at?->format('M d, Y') }}</time>
                            </div>

                            <h2 class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors line-clamp-2">
                                <a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a>
                            </h2>

                            <p class="mt-3 text-sm text-slate-600 dark:text-slate-400 line-clamp-2">
                                {{ $post->summary ?? Str::limit(strip_tags($post->content), 100) }}
                            </p>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                            <span class="text-slate-500">{{ $post->reading_time }} min read</span>
                            <a href="{{ route('posts.show', $post->slug) }}" class="font-semibold text-cyan-600 dark:text-cyan-400">Baca Sekarang &rarr;</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $posts->links() }}
        </div>
    @else
        <div class="py-20 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 max-w-lg mx-auto">
            <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Belum Ada Bookmark</h3>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Anda belum menyimpan artikel apapun. Klik tombol bookmark pada artikel untuk menyimpannya.</p>
            <a href="{{ route('home') }}" class="inline-block mt-4 px-4 py-2 bg-cyan-600 text-white font-semibold text-xs rounded-full hover:bg-cyan-500 transition-colors">
                Jelajahi Artikel
            </a>
        </div>
    @endif

</div>
@endsection
