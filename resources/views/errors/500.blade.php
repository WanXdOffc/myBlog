@extends('layouts.blog')

@section('title', '500 Server Error — TechJournal')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 flex flex-col items-center justify-center text-center">

    <div class="w-24 h-24 rounded-3xl bg-rose-500/10 text-rose-500 flex items-center justify-center font-mono font-bold text-4xl mb-6 border border-rose-500/20">
        500
    </div>

    <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
        Terjadi Kesalahan Server
    </h1>

    <p class="mt-3 text-base text-slate-600 dark:text-slate-400 max-w-md">
        Maaf, sistem sedang mengalami kendala internal. Silakan coba muat ulang halaman beberapa saat lagi.
    </p>

    <div class="mt-8 flex items-center gap-4">
        <a href="{{ route('home') }}" class="px-6 py-2.5 bg-cyan-600 hover:bg-cyan-500 text-white font-semibold text-xs rounded-full shadow-md shadow-cyan-600/30 transition-all">
            Kembali ke Beranda
        </a>
    </div>

</div>
@endsection
