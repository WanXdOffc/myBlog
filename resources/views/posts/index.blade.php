@extends('layouts.blog')

@section('title', 'TechJournal — Catatan tentang kode dan gagasan')
@section('meta_description', 'Tulisan tentang software engineering, Laravel, dan proses membangun produk digital.')

@section('content')
<div class="mx-auto max-w-4xl px-5 pb-20 pt-14 sm:px-8 sm:pt-20">
    <header class="mb-12 max-w-3xl">
        <p class="mb-5 font-mono text-xs uppercase tracking-[0.16em] text-zinc-500 dark:text-zinc-400">
            Jurnal pribadi <span aria-hidden="true">/</span> Software &amp; proses
        </p>
        <h1 id="page-title" class="text-4xl font-semibold leading-[1.08] tracking-[-0.04em] text-zinc-900 dark:text-zinc-100 sm:text-5xl">
            Membangun sesuatu yang berarti, satu baris kode pada satu waktu.
        </h1>
        <p class="mt-5 max-w-2xl text-lg leading-8 text-zinc-600 dark:text-zinc-300">
            Catatan tentang software engineering, Laravel, dan pelajaran kecil dari proses membuat produk.
        </p>

        <form action="{{ route('home') }}" method="GET" role="search" class="mt-7 flex max-w-lg gap-2">
            <label for="article-search" class="sr-only">Cari artikel</label>
            <input
                id="article-search"
                type="search"
                name="search"
                value="{{ $search }}"
                placeholder="Cari artikel…"
                class="min-w-0 flex-1 rounded-lg border border-zinc-200 bg-white px-4 py-2.5 text-sm text-zinc-800 placeholder:text-zinc-500 focus:border-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-400/30 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:placeholder:text-zinc-400"
            >
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            @if(request('tag'))
                <input type="hidden" name="tag" value="{{ request('tag') }}">
            @endif
            <button type="submit" class="rounded-lg bg-zinc-900 px-4 py-2.5 text-sm font-medium text-zinc-100 transition-colors hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-zinc-500 focus:ring-offset-2 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white">
                Cari
            </button>
        </form>
    </header>

    <nav aria-label="Filter kategori artikel" class="category-scroll -mx-5 mb-4 overflow-x-auto border-y border-zinc-200 px-5 py-3 dark:border-zinc-800 sm:-mx-8 sm:px-8">
        <ul class="flex w-max min-w-full items-center gap-2 text-sm">
            <li>
                <a
                    href="{{ route('home') }}"
                    @if(!request('category') && !request('tag')) aria-current="page" @endif
                    class="inline-flex whitespace-nowrap rounded-full px-3.5 py-1.5 font-medium transition-colors {{ !request('category') && !request('tag') ? 'bg-zinc-900 text-zinc-100 dark:bg-zinc-100 dark:text-zinc-900' : 'text-zinc-600 hover:bg-zinc-200 hover:text-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-zinc-100' }}"
                >Semua tulisan</a>
            </li>
            @foreach($categories as $category)
                <li>
                    <a
                        href="{{ route('home', ['category' => $category->slug]) }}"
                        @if(request('category') === $category->slug) aria-current="page" @endif
                        class="inline-flex whitespace-nowrap rounded-full px-3.5 py-1.5 font-medium transition-colors {{ request('category') === $category->slug ? 'bg-zinc-900 text-zinc-100 dark:bg-zinc-100 dark:text-zinc-900' : 'text-zinc-600 hover:bg-zinc-200 hover:text-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-zinc-100' }}"
                    >{{ $category->name }} <span class="ml-1 font-mono text-xs opacity-70">{{ $category->posts_count }}</span></a>
                </li>
            @endforeach
        </ul>
    </nav>

    @if($search || $activeCategory || $activeTag)
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-zinc-200 py-4 text-sm dark:border-zinc-800" role="status">
            <p class="text-zinc-600 dark:text-zinc-300">
                @if($search)
                    Hasil pencarian untuk <strong class="text-zinc-900 dark:text-zinc-100">“{{ $search }}”</strong>
                @endif
                @if($activeCategory)
                    {{ $search ? ' · ' : '' }}Kategori: <strong class="text-zinc-900 dark:text-zinc-100">{{ $activeCategory->name }}</strong>
                @endif
                @if($activeTag)
                    {{ ($search || $activeCategory) ? ' · ' : '' }}Tag: <strong class="text-zinc-900 dark:text-zinc-100">#{{ $activeTag->name }}</strong>
                @endif
            </p>
            <a href="{{ route('home') }}" class="font-medium text-zinc-700 underline underline-offset-4 hover:text-zinc-950 focus:outline-none focus:ring-2 focus:ring-zinc-500 dark:text-zinc-200 dark:hover:text-white">
                Hapus filter
            </a>
        </div>
    @endif

    <section aria-labelledby="articles-heading" class="mt-7">
        <div class="mb-1 flex items-baseline justify-between gap-4">
            <h2 id="articles-heading" class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Tulisan</h2>
            <p class="font-mono text-xs text-zinc-500 dark:text-zinc-400">{{ $posts->total() }} artikel</p>
        </div>

        @if($posts->count() > 0)
            <ol class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @foreach($posts as $post)
                    <li>
                        <article class="group grid grid-cols-[5.5rem_minmax(0,1fr)_1.25rem] items-start gap-x-4 py-6 sm:grid-cols-[7rem_minmax(0,1fr)_1.5rem] sm:gap-x-6 sm:py-7">
                            <div class="pt-1">
                                <time datetime="{{ $post->published_at?->toIso8601String() }}" class="font-mono text-[11px] leading-5 text-zinc-500 dark:text-zinc-400 sm:text-xs">
                                    {{ $post->published_at?->format('d M Y') }}
                                </time>
                            </div>

                            <div class="min-w-0">
                                <p class="mb-2 flex flex-wrap items-center gap-x-2 gap-y-1 font-mono text-[11px] text-zinc-500 dark:text-zinc-400">
                                    @if($post->category)
                                        <a href="{{ route('home', ['category' => $post->category->slug]) }}" class="rounded-sm text-zinc-700 hover:text-zinc-950 hover:underline focus:outline-none focus:ring-2 focus:ring-zinc-500 dark:text-zinc-200 dark:hover:text-white">
                                            {{ $post->category->name }}
                                        </a>
                                        <span aria-hidden="true">/</span>
                                    @endif
                                    <span>{{ $post->reading_time }} menit baca</span>
                                </p>
                                <h3 class="text-lg font-semibold leading-snug tracking-tight text-zinc-900 transition-colors group-hover:text-zinc-600 dark:text-zinc-100 dark:group-hover:text-zinc-300 sm:text-xl">
                                    <a href="{{ route('posts.show', $post->slug) }}" class="rounded-sm focus:outline-none focus:ring-2 focus:ring-zinc-500">
                                        {{ $post->title }}
                                    </a>
                                </h3>
                                <p class="mt-2 line-clamp-2 text-sm leading-6 text-zinc-600 dark:text-zinc-300 sm:text-base">
                                    {{ $post->summary ?? Str::limit(strip_tags($post->content), 160) }}
                                </p>
                                <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $post->user->name ?? 'Admin' }}
                                </p>
                            </div>

                            <span aria-hidden="true" class="mt-1 justify-self-end text-zinc-400 transition-all duration-200 group-hover:translate-x-1 group-hover:text-zinc-800 dark:text-zinc-500 dark:group-hover:text-zinc-100">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M5 12h14m-6-6 6 6-6 6"></path>
                                </svg>
                            </span>
                        </article>
                    </li>
                @endforeach
            </ol>

            <nav aria-label="Navigasi halaman artikel" class="mt-8 border-t border-zinc-200 pt-6 dark:border-zinc-800">
                {{ $posts->links() }}
            </nav>
        @else
            <div class="border-y border-zinc-200 py-16 text-center dark:border-zinc-800">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Belum ada tulisan yang cocok.</h3>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Coba kata kunci atau kategori lain.</p>
                <a href="{{ route('home') }}" class="mt-5 inline-flex rounded-sm font-medium text-zinc-700 underline underline-offset-4 hover:text-zinc-950 focus:outline-none focus:ring-2 focus:ring-zinc-500 dark:text-zinc-200 dark:hover:text-white">
                    Lihat semua tulisan
                </a>
            </div>
        @endif
    </section>
</div>
@endsection
