<footer class="mt-20 border-t border-slate-200 dark:border-slate-800/80 bg-white/50 dark:bg-slate-900/50 backdrop-blur">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex flex-col md:flex-row items-center justify-between gap-6 text-sm text-slate-500 dark:text-slate-400">
        
        <div class="flex items-center gap-3">
            <div class="w-7 h-7 rounded-lg bg-cyan-600 text-white font-mono font-bold text-xs flex items-center justify-center">
                &lt;/&gt;
            </div>
            <p>&copy; {{ date('Y') }} TechJournal. Created with Laravel 11 & Tailwind CSS.</p>
        </div>

        <div class="flex items-center gap-6 font-medium">
            <a href="{{ route('home') }}" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors">Home</a>
            <a href="/admin" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors">Admin Panel</a>
            <a href="https://github.com" target="_blank" rel="noopener" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors">GitHub</a>
        </div>

    </div>
</footer>
