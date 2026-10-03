<footer class="mt-20 border-t border-zinc-200 bg-zinc-100 dark:border-zinc-800 dark:bg-zinc-900">
    <div class="mx-auto flex max-w-4xl flex-col items-center justify-between gap-6 px-5 py-10 text-sm text-zinc-600 dark:text-zinc-300 md:flex-row sm:px-8">
        
        <p>&copy; {{ date('Y') }} TechJournal. Ditulis dan diterbitkan dengan cermat.</p>

        <nav aria-label="Navigasi footer" class="flex items-center gap-6 font-medium">
            <a href="{{ route('home') }}" class="underline-offset-4 hover:text-zinc-950 hover:underline dark:hover:text-white">Beranda</a>
            <a href="/admin" class="underline-offset-4 hover:text-zinc-950 hover:underline dark:hover:text-white">Admin</a>
            <a href="https://github.com" target="_blank" rel="noopener noreferrer" class="underline-offset-4 hover:text-zinc-950 hover:underline dark:hover:text-white">GitHub</a>
        </nav>

    </div>
</footer>
