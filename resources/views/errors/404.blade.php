@extends('layouts.blog')

@section('title', '404 Halaman Tidak Ditemukan — TechJournal')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 flex flex-col items-center justify-center text-center">

    <div class="w-24 h-24 rounded-3xl bg-cyan-500/10 text-cyan-500 flex items-center justify-center font-mono font-bold text-4xl mb-6 border border-cyan-500/20">
        404
    </div>

    <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
        Halaman Tidak Ditemukan
    </h1>

    <p class="mt-3 text-base text-slate-600 dark:text-slate-400 max-w-md">
        Maaf, artikel atau halaman yang Anda cari tidak ditemukan atau telah dipindahkan.
    </p>

    <div class="mt-8 flex items-center gap-4">
        <a href="{{ route('home') }}" class="px-6 py-2.5 bg-cyan-600 hover:bg-cyan-500 text-white font-semibold text-xs rounded-full shadow-md shadow-cyan-600/30 transition-all">
            Kembali ke Beranda
        </a>
    </div>

</div>
@endsection
