@extends('layouts.blog')

@section('title', $post->title . ' — TechJournal')
@section('meta_description', Str::limit($post->summary ?? strip_tags($post->content), 150))
@section('og_type', 'article')
@section('og_image', $post->featured_image ? asset('storage/' . $post->featured_image) : asset('images/og-default.jpg'))

@section('content')
<div
    id="reading-progress"
    role="progressbar"
    aria-label="Kemajuan membaca artikel"
    aria-valuemin="0"
    aria-valuemax="100"
    aria-valuenow="0"
    class="reading-progress"
>
    <span class="reading-progress__bar"></span>
</div>

<div class="mx-auto max-w-6xl px-5 py-8 sm:px-8 sm:py-12" x-data="{
    liked: {{ $isLiked ? 'true' : 'false' }}, 
    likesCount: {{ $post->likes_count ?? 0 }},
    bookmarked: {{ $isBookmarked ? 'true' : 'false' }},
    async toggleLike() {
        @guest
            window.location.href = '{{ route('login') }}';
            return;
        @endguest
        const res = await fetch('{{ route('posts.like', $post->slug) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        const data = await res.json();
        this.liked = data.liked;
        this.likesCount = data.likes_count;
    },
    async toggleBookmark() {
        @guest
            window.location.href = '{{ route('login') }}';
            return;
        @endguest
        const res = await fetch('{{ route('posts.bookmark', $post->slug) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        const data = await res.json();
        this.bookmarked = data.bookmarked;
    }
}">

    <!-- Back to Articles button -->
    <div class="mx-auto mb-8 flex max-w-5xl items-center justify-between gap-4">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-medium text-zinc-600 transition-colors hover:text-zinc-950 dark:text-zinc-300 dark:hover:text-white">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Beranda
        </a>

        <!-- Like & Bookmark Action Header Buttons -->
        <div class="flex items-center gap-2">
            <!-- Like Button -->
            <button 
                @click="toggleLike()" 
                type="button"
                aria-label="Sukai artikel"
                :aria-pressed="liked"
                class="flex items-center gap-1.5 rounded-full border border-zinc-200 bg-white px-3.5 py-2 text-xs font-medium text-zinc-700 transition-colors hover:border-zinc-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200"
                :class="liked ? 'bg-rose-50 dark:bg-rose-950/60 border-rose-200 dark:border-rose-800 text-rose-600 dark:text-rose-400' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-600 dark:text-stone-300 hover:border-rose-300'"
            >
                <svg class="w-4 h-4" :fill="liked ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                <span x-text="likesCount"></span> Likes
            </button>

            <!-- Bookmark Button -->
            <button 
                @click="toggleBookmark()" 
                type="button"
                aria-label="Simpan artikel ke bookmark"
                :aria-pressed="bookmarked"
                class="flex items-center gap-1.5 rounded-full border border-zinc-200 bg-white px-3.5 py-2 text-xs font-medium text-zinc-700 transition-colors hover:border-zinc-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200"
                :class="bookmarked ? 'bg-terracotta/10 dark:bg-terracotta/15 border-terracotta/30 dark:border-terracotta-light/40 text-terracotta dark:text-terracotta-light' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-600 dark:text-stone-300 hover:border-terracotta'"
            >
                <svg class="w-4 h-4" :fill="bookmarked ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                </svg>
                <span x-text="bookmarked ? 'Saved' : 'Bookmark'"></span>
            </button>
        </div>
    </div>

    <!-- ─── Post Header ────────────────────────────────────────────────────── -->
    <header class="mx-auto mb-10 max-w-3xl border-b border-zinc-200 pb-8 text-left dark:border-zinc-800 sm:mb-12">
        <div class="mb-5 flex flex-wrap items-center gap-3 font-mono text-xs text-zinc-500 dark:text-zinc-400">
            @if($post->category)
                <a href="{{ route('home', ['category' => $post->category->slug]) }}" class="font-medium text-zinc-700 hover:text-zinc-950 hover:underline dark:text-zinc-200 dark:hover:text-white">
                    {{ $post->category->name }}
                </a>
            @endif
            <span aria-hidden="true">/</span>
            <time datetime="{{ $post->published_at?->toIso8601String() }}">{{ $post->published_at?->format('d M Y') }}</time>
            <span aria-hidden="true">/</span>
            <span>{{ $post->reading_time }} menit baca</span>
        </div>

        <h1 class="text-3xl font-semibold leading-[1.12] tracking-[-0.04em] text-zinc-900 dark:text-zinc-100 sm:text-4xl md:text-5xl">
            {{ $post->title }}
        </h1>

        <div class="mt-6 flex items-center gap-4 text-sm">
            <div class="flex items-center gap-2">
                @if($post->user->avatar)
                    <img src="{{ $post->user->avatar }}" alt="{{ $post->user->name }}" class="w-8 h-8 rounded-full object-cover border border-cyan-500">
                @else
                    <div class="w-8 h-8 rounded-full bg-cyan-600 text-white font-bold flex items-center justify-center text-xs shadow-sm">
                        {{ strtoupper(substr($post->user->name ?? 'A', 0, 1)) }}
                    </div>
                @endif
                <div class="text-left">
                    <p class="font-medium text-zinc-800 dark:text-zinc-100">{{ $post->user->name ?? 'Admin' }}</p>
                    <p class="font-mono text-xs text-zinc-500 dark:text-zinc-400">TechJournal</p>
                </div>
            </div>
        </div>
    </header>

    <!-- ─── Featured Image Banner ────────────────────────────────────────── -->
    @if($post->featured_image)
        <div class="max-w-4xl mx-auto mb-12 rounded-3xl overflow-hidden shadow-lg border border-slate-200/80 dark:border-slate-800">
            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full max-h-[480px] object-cover">
        </div>
    @endif

    <!-- ─── Main Content & Table of Contents Grid ────────────────────────── -->
    <div class="mx-auto grid max-w-5xl grid-cols-1 items-start gap-12 lg:grid-cols-[minmax(0,1fr)_15rem]">
        
        <div class="min-w-0 w-full">
            
            <article id="article-content" class="article-prose prose prose-zinc dark:prose-invert prose-lg max-w-3xl leading-relaxed prose-headings:scroll-mt-24 prose-headings:font-sans prose-headings:tracking-tight prose-code:font-mono prose-pre:font-mono prose-img:rounded-lg">
                {!! Str::markdown($post->content) !!}
            </article>

            <!-- ─── Post Tags & Social Share ────────────────────────────── -->
            <div class="mt-12 pt-8 border-t border-slate-200 dark:border-slate-800 space-y-6">
                
                @if($post->tags->count() > 0)
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-semibold text-slate-600 dark:text-stone-300 uppercase tracking-wider mr-2">Tags:</span>
                        @foreach($post->tags as $tag)
                            <a href="{{ route('home', ['tag' => $tag->slug]) }}" class="px-3 py-1 rounded-lg bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 text-xs font-medium hover:bg-terracotta/10 dark:hover:bg-terracotta/20 hover:text-terracotta dark:hover:text-terracotta-light transition-colors">
                                #{{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                @endif

                <div class="flex flex-wrap items-center justify-between gap-4 p-4 rounded-2xl bg-slate-100/70 dark:bg-slate-900/70 border border-slate-200/80 dark:border-slate-800">
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Bagikan Artikel Ini:</span>

                    <div class="flex items-center gap-2">
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}" target="_blank" rel="noopener" aria-label="Bagikan ke X" class="rounded-xl border border-slate-200/80 bg-white p-2 text-slate-700 shadow-sm transition-transform hover:-translate-y-0.5 hover:text-terracotta focus-visible:outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:text-terracotta-light">
                            <svg aria-hidden="true" class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>

                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener" aria-label="Bagikan ke LinkedIn" class="rounded-xl border border-slate-200/80 bg-white p-2 text-slate-700 shadow-sm transition-transform hover:-translate-y-0.5 hover:text-terracotta focus-visible:outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:text-terracotta-light">
                            <svg aria-hidden="true" class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 10.9v8.37H9.25V10.9H6.46M7.86 6.72a1.47 1.47 0 1 0 0 2.94 1.47 1.47 0 0 0 0-2.94z"/></svg>
                        </a>

                        <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' ' . url()->current()) }}" target="_blank" rel="noopener" aria-label="Bagikan ke WhatsApp" class="rounded-xl border border-slate-200/80 bg-white p-2 text-slate-700 shadow-sm transition-transform hover:-translate-y-0.5 hover:text-terracotta focus-visible:outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:text-terracotta-light">
                            <svg aria-hidden="true" class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                        </a>

                        <button type="button" aria-label="Salin tautan artikel" onclick="navigator.clipboard.writeText('{{ url()->current() }}'); alert('Link artikel berhasil disalin!');" class="rounded-xl border border-slate-200/80 bg-white p-2 text-slate-700 shadow-sm transition-transform hover:-translate-y-0.5 hover:text-terracotta focus-visible:outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:text-terracotta-light">
                            <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        </button>
                    </div>
                </div>

            </div>

            <!-- ─── Comments Section ─────────────────────────────────────── -->
            <section class="mt-14 pt-10 border-t border-slate-200 dark:border-slate-800">
                <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white mb-8 flex items-center gap-2">
                    <svg class="w-6 h-6 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    Komentar ({{ $post->comments->count() }})
                </h3>

                @if(session('success'))
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @auth
                    <!-- Logged in Comment Form -->
                    <form action="{{ route('posts.comments.store', $post->slug) }}" method="POST" class="mb-10">
                        @csrf
                        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                            <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-800 pb-3">
                                @if(auth()->user()->avatar)
                                    <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full object-cover border border-cyan-500">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-cyan-600 text-white font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                @endif
                                <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Berkomentar sebagai <strong>{{ auth()->user()->name }}</strong></span>
                            </div>
                            <textarea name="body" rows="3" required placeholder="Tulis komentar atau tanggapan Anda..." class="w-full text-sm bg-transparent border-none focus:ring-0 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none resize-none"></textarea>
                            <div class="flex justify-end pt-2 border-t border-slate-100 dark:border-slate-800">
                                <button type="submit" class="px-5 py-2 bg-cyan-600 hover:bg-cyan-500 text-white font-semibold text-xs rounded-full shadow-sm transition-all">
                                    Kirim Komentar
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <!-- Guest Login CTA Box -->
                    <div class="mb-10 p-6 rounded-3xl bg-gradient-to-r from-slate-900 to-slate-950 border border-slate-800 text-center shadow-lg">
                        <div class="w-12 h-12 rounded-2xl bg-terracotta/10 text-terracotta dark:text-terracotta-light flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                        </div>
                        <h4 class="text-base font-bold text-white">Ingin Mengirim Komentar?</h4>
                        <p class="text-xs text-slate-600 dark:text-stone-300 mt-1 mb-5">Silakan masuk menggunakan akun Google Anda atau akun terdaftar untuk berdiskusi.</p>
                        
                        <div class="flex flex-wrap items-center justify-center gap-3">
                            <!-- Login via Google Button -->
                            <a href="{{ route('auth.google') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white text-slate-800 hover:bg-slate-100 font-semibold text-xs rounded-full shadow transition-all">
                                <svg class="w-4 h-4" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                                </svg>
                                Login via Google
                            </a>

                            <!-- Regular Login Link -->
                            <a href="{{ route('login') }}" class="px-4 py-2 border border-slate-700 text-slate-300 hover:text-white font-semibold text-xs rounded-full transition-colors">
                                Log In Manual
                            </a>
                        </div>
                    </div>
                @endauth

                <!-- Comment List -->
                <div class="space-y-4">
                    @forelse($post->comments as $comment)
                        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    @if($comment->user->avatar)
                                        <img src="{{ $comment->user->avatar }}" alt="{{ $comment->user->name }}" class="w-7 h-7 rounded-full object-cover border border-cyan-500">
                                    @else
                                        <div class="w-7 h-7 rounded-full bg-cyan-600 text-white text-xs font-bold flex items-center justify-center">
                                            {{ strtoupper(substr($comment->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                    <span class="font-bold text-sm text-slate-800 dark:text-slate-200">{{ $comment->user->name ?? 'User' }}</span>
                                </div>
                                <time class="text-xs text-slate-600 dark:text-stone-300">{{ $comment->created_at->diffForHumans() }}</time>
                            </div>
                            <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed pl-9">
                                {{ $comment->body }}
                            </p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-600 dark:text-stone-300 italic">Belum ada komentar. Jadilah yang pertama memberikan tanggapan!</p>
                    @endforelse
                </div>
            </section>

        </div>

        <!-- ─── Right Sticky Sidebar: Table of Contents (TOC) ─────────────── -->
        <aside class="hidden lg:sticky lg:top-24 lg:block">
            <div class="space-y-4 border-l border-zinc-200 pl-4 dark:border-zinc-800">
                <h2 class="font-mono text-[11px] font-medium uppercase tracking-[0.12em] text-zinc-500 dark:text-zinc-400">
                    Dalam artikel ini
                </h2>
                <nav id="toc-nav" aria-label="Daftar isi artikel" class="space-y-1.5 text-sm">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Menyiapkan daftar isi…</p>
                </nav>
            </div>
        </aside>

    </div>

</div>

@endsection
