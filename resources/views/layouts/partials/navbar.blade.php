<header class="sticky top-0 z-50 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800/80 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
        
        <!-- Brand Logo -->
        <a href="{{ route('home') }}" class="flex items-center gap-2 group">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-cyan-500 to-blue-600 flex items-center justify-center text-white font-mono font-bold text-lg shadow-md shadow-cyan-500/20 group-hover:scale-105 transition-transform">
                &lt;/&gt;
            </div>
            <span class="font-bold text-xl tracking-tight text-slate-900 dark:text-white flex items-center gap-1">
                Tech<span class="text-cyan-600 dark:text-cyan-400">Journal</span>
            </span>
        </a>

        <!-- Live Search Bar (Center / Desktop powered by Scout & AlpineJS) -->
        <div class="hidden md:flex flex-1 max-w-md mx-6 relative" x-data="{
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
                <input 
                    type="text" 
                    name="search" 
                    x-model="query"
                    @input.debounce.300ms="performSearch()"
                    @focus="if(results.length > 0) open = true"
                    @click.outside="open = false"
                    placeholder="Live Search articles (powered by Scout)..." 
                    class="w-full pl-10 pr-4 py-1.5 text-sm bg-slate-100 dark:bg-slate-800/90 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 rounded-full border border-transparent focus:border-cyan-500 dark:focus:border-cyan-400 focus:bg-white dark:focus:bg-slate-900 focus:outline-none transition-all"
                >
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </form>

            <!-- Live Search Dropdown Results -->
            <div 
                x-show="open" 
                x-transition
                class="absolute left-0 right-0 top-full mt-2 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 py-2 z-50 max-h-80 overflow-y-auto"
                style="display: none;"
            >
                <div x-show="loading" class="px-4 py-3 text-xs text-slate-400 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 animate-spin text-cyan-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Mencari artikel...
                </div>

                <template x-if="!loading && results.length > 0">
                    <div>
                        <div class="px-4 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Hasil Pencarian Live</div>
                        <template x-for="item in results" :key="item.id">
                            <a :href="item.url" class="block px-4 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-colors border-b border-slate-100 dark:border-slate-800/60 last:border-none">
                                <span class="text-[10px] font-semibold text-cyan-600 dark:text-cyan-400 block" x-text="item.category_name"></span>
                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate" x-text="item.title"></h4>
                            </a>
                        </template>
                    </div>
                </template>

                <div x-show="!loading && results.length === 0 && query.trim().length >= 2" class="px-4 py-3 text-xs text-slate-400">
                    Tidak ditemukan artikel untuk "<span x-text="query"></span>".
                </div>
            </div>
        </div>

        <!-- Right Action Items (Theme Switch & Auth) -->
        <div class="flex items-center gap-3">
            
            <!-- Dark / Light Theme Toggle -->
            <button 
                onclick="toggleTheme()" 
                type="button"
                aria-label="Toggle theme"
                class="p-2 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none transition-colors"
            >
                <svg class="w-5 h-5 hidden dark:block text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <svg class="w-5 h-5 block dark:hidden text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                </svg>
            </button>

            <!-- Authentication Status -->
            @auth
                <!-- User Profile Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button 
                        @click="open = !open" 
                        class="flex items-center gap-2 p-1 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                    >
                        @if(auth()->user()->avatar)
                            <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full object-cover border border-cyan-500">
                        @else
                            <div class="w-8 h-8 rounded-full bg-cyan-600 text-white font-semibold flex items-center justify-center text-xs">
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
                        class="absolute right-0 mt-2 w-52 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 py-2 z-50"
                        style="display: none;"
                    >
                        <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-800">
                            <p class="text-xs text-slate-400">Signed in as</p>
                            <p class="text-sm font-semibold truncate text-slate-800 dark:text-slate-100">{{ auth()->user()->email }}</p>
                        </div>

                        <a href="{{ route('posts.bookmarks') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60">
                            <svg class="w-4 h-4 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                            </svg>
                            Bookmark Saya
                        </a>

                        @if(auth()->user()->is_admin)
                            <a href="/admin" class="flex items-center gap-2 px-4 py-2 text-sm text-cyan-600 dark:text-cyan-400 hover:bg-slate-50 dark:hover:bg-slate-800/60 font-medium">
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
                <a href="{{ route('login') }}" class="text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-cyan-600 dark:hover:text-cyan-400 px-3 py-1.5 transition-colors">
                    Log in
                </a>
                <a href="{{ route('register') }}" class="text-sm font-semibold text-white bg-cyan-600 hover:bg-cyan-500 px-4 py-1.5 rounded-full shadow-sm shadow-cyan-600/30 transition-all">
                    Register
                </a>
            @endauth

        </div>

    </div>
</header>
