<header class="sticky top-0 z-50 border-b border-zinc-200 bg-zinc-50/95 backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/95">
    <nav aria-label="Navigasi utama">
    <div class="mx-auto flex h-16 max-w-4xl items-center justify-between gap-4 px-5 sm:px-8">
        
        <!-- Brand Logo -->
        <a href="{{ route('home') }}" class="flex items-center gap-2 group">
            <span class="font-semibold text-xl tracking-tight text-zinc-900 dark:text-zinc-100">
                TechJournal
            </span>
        </a>

        <!-- Live Search Bar (Center / Desktop powered by Scout & AlpineJS) -->
        <div role="search" class="{{ request()->routeIs('home') ? 'hidden' : 'hidden md:flex' }} relative mx-6 max-w-md flex-1" x-data="{
            query: '{{ request('search') }}',
            results: [],
            open: false,
            loading: false,
            async performSearch() {
                if (this.query.trim().length < 2) {
                    this.results = [];
                    this.open = false;
                    return;
                }
                this.loading = true;
                this.open = true;
                try {
                    const res = await fetch(`/api/search?q=${encodeURIComponent(this.query)}`);
                    const data = await res.json();
                    this.results = data.results || [];
                } catch (e) {
                    this.results = [];
                } finally {
                    this.loading = false;
                }
            }
        }">
            <form action="{{ route('home') }}" method="GET" class="w-full relative" @submit.prevent="window.location.href = '{{ route('home') }}?search=' + encodeURIComponent(query)">
                <label for="nav-search" class="sr-only">Cari artikel</label>
                <input
                    id="nav-search"
                    type="text" 
                    name="search" 
                    x-model="query"
                    @input.debounce.300ms="performSearch()"
                    @focus="if(results.length > 0) open = true"
                    @click.outside="open = false"
                    placeholder="Cari artikel…"
                    class="w-full rounded-full border border-zinc-200 bg-white py-2 pl-10 pr-4 text-sm text-zinc-800 placeholder:text-zinc-500 transition-colors focus:border-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-400/30 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:placeholder:text-zinc-400"
                >
                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-zinc-500 dark:text-zinc-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </form>

            <!-- Live Search Dropdown Results -->
            <div 
                x-show="open" 
                x-transition
                class="absolute left-0 right-0 top-full z-50 mt-2 max-h-80 overflow-y-auto rounded-xl border border-zinc-200 bg-white py-2 shadow-xl dark:border-zinc-700 dark:bg-zinc-900"
                style="display: none;"
            >
                <div x-show="loading" class="px-4 py-3 text-xs text-slate-600 dark:text-stone-300 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 animate-spin text-zinc-600 dark:text-zinc-300" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Mencari artikel...
                </div>

                <template x-if="!loading && results.length > 0">
                    <div>
                        <div class="px-4 py-1.5 text-[10px] font-bold text-slate-600 dark:text-stone-300 uppercase tracking-wider">Hasil Pencarian Live</div>
                        <template x-for="item in results" :key="item.id">
                            <a :href="item.url" class="block border-b border-zinc-100 px-4 py-2.5 transition-colors hover:bg-zinc-50 dark:border-zinc-800 dark:hover:bg-zinc-800/80 last:border-none">
                                <span class="block text-[10px] font-medium text-zinc-500 dark:text-zinc-400" x-text="item.category_name"></span>
                                <h4 class="truncate text-xs font-semibold text-zinc-800 dark:text-zinc-100" x-text="item.title"></h4>
                            </a>
                        </template>
                    </div>
                </template>

                <div x-show="!loading && results.length === 0 && query.trim().length >= 2" class="px-4 py-3 text-xs text-slate-600 dark:text-stone-300">
                    Tidak ditemukan artikel untuk "<span x-text="query"></span>".
                </div>
            </div>
        </div>

        <!-- Right Action Items (Theme Switch & Auth) -->
        <div class="flex items-center gap-2 sm:gap-3">
            
            <!-- Dark / Light Theme Toggle -->
            <button
                type="button"
                aria-label="Ganti tema warna"
                x-data="{ dark: document.documentElement.classList.contains('dark') }"
                @click="toggleTheme(); dark = document.documentElement.classList.contains('dark')"
                :aria-pressed="dark"
                class="rounded-lg p-2 text-zinc-600 transition-colors hover:bg-zinc-200 focus:outline-none dark:text-zinc-300 dark:hover:bg-zinc-800"
            >
                <svg class="hidden h-5 w-5 text-zinc-200 dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <svg class="block h-5 w-5 text-zinc-700 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                </svg>
            </button>

            <!-- Authentication Status -->
            @auth
                <!-- User Profile Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button
                        @click="open = !open" 
                        type="button"
                        aria-haspopup="true"
                        :aria-expanded="open.toString()"
                        class="flex items-center gap-2 rounded-full p-1 transition-colors hover:bg-zinc-200 dark:hover:bg-zinc-800"
                    >
                        @if(auth()->user()->avatar)
                            <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full object-cover border border-cyan-500">
                        @else
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-zinc-800 text-xs font-semibold text-zinc-100 dark:bg-zinc-200 dark:text-zinc-900">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                        @endif
                        <span class="hidden sm:inline font-medium text-sm text-slate-700 dark:text-slate-200">
                            {{ auth()->user()->name }}
                        </span>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div 
                        x-show="open" 
                        @click.outside="open = false" 
                        x-transition
                        class="absolute right-0 z-50 mt-2 w-52 rounded-xl border border-zinc-200 bg-white py-2 shadow-xl dark:border-zinc-700 dark:bg-zinc-900"
                        style="display: none;"
                    >
                        <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-800">
                            <p class="text-xs text-slate-600 dark:text-stone-300">Signed in as</p>
                            <p class="truncate text-sm font-semibold text-zinc-800 dark:text-zinc-100">{{ auth()->user()->email }}</p>
                        </div>

                        <a href="{{ route('posts.bookmarks') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60">
                            <svg class="w-4 h-4 text-zinc-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                            </svg>
                            Bookmark Saya
                        </a>

                        @if(auth()->user()->is_admin)
                            <a href="/admin" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-zinc-800/60">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                </svg>
                                Admin Dashboard
                            </a>
                        @endif

                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60">
                            Profile Settings
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2 text-sm text-rose-600 dark:text-rose-400 hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="px-3 py-1.5 text-sm font-medium text-zinc-700 transition-colors hover:text-zinc-950 dark:text-zinc-300 dark:hover:text-white">
                    Log in
                </a>
                <a href="{{ route('register') }}" class="rounded-full bg-zinc-900 px-4 py-1.5 text-sm font-medium text-zinc-100 transition-colors hover:bg-zinc-700 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white">
                    Register
                </a>
            @endauth

        </div>

    </div>
    </nav>
</header>
