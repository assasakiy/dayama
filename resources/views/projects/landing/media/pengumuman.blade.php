@extends('web.layouts.app')

@section('content')
<section class="hero-subpage py-16 md:py-20 pb-24 md:pb-32 relative z-0">
    <div class="container-page relative z-10">
        <nav class="flex text-sm text-white/80 mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('landing.home') ?? '/' }}" class="inline-flex items-center hover:text-white transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        Beranda
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-white/60" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 md:ml-2 font-medium text-white">Media</span>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-white/60" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 md:ml-2 font-medium text-white">{{ $title ?? 'Pengumuman & Edaran' }}</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="max-w-3xl">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black tracking-tight text-white mb-6 leading-tight">
                {!! $pageModel?->sections['hero']['title'] ?? $title ?? '' !!}
                @if(!empty($pageModel?->sections['hero']['highlight']))
                <span class="block text-primary">{!! $pageModel->sections['hero']['highlight'] !!}</span>
                @endif
            </h1>
            <p class="text-lg md:text-xl text-white/90 leading-relaxed max-w-3xl">
                {{ $pageModel?->sections['hero']['subtitle'] ?? 'Papan Informasi Resmi Pondok Pesantren.' }}
            </p>
            </div>
            
            {{-- Search & Filter --}}
            <div class="flex items-center gap-3 w-full md:w-auto shrink-0">
                <div class="relative flex-grow md:w-64">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" class="w-full pl-11 pr-4 py-3 border-0 rounded-xl bg-white text-white shadow-lg focus:outline-none focus:ring-4 focus:ring-primary/30 transition-all text-base" placeholder="Cari nomor surat/judul...">
                </div>
                <button class="px-4 py-3 bg-white text-primary font-bold rounded-xl shadow-lg hover:bg-white/10-muted transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span class="hidden sm:inline">Filter</span>
                </button>
            </div>
        </div>
    </div>
    
    <div class="absolute bottom-0 inset-x-0 pointer-events-none z-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto text-background">
            <path d="M0 80V40C240 0 480 0 720 40C960 80 1200 80 1440 40V80H0Z" fill="currentColor"/>
        </svg>
    </div>
</section>

<div class="container-page max-w-5xl py-12 md:py-16">
    {{-- Pinned Announcement --}}
    <div class="mb-10">
        <h3 class="text-sm font-bold text-muted-foreground uppercase tracking-wider mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
            Pengumuman Penting
        </h3>
        
        <div class="bg-red-50/50 dark:bg-red-950/20 border border-red-200 dark:border-red-900/50 rounded-xl p-5 md:p-6 shadow-sm flex flex-col md:flex-row gap-5 items-start">
            <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/50 flex items-center justify-center shrink-0 text-red-600 dark:text-red-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
            </div>
            <div class="flex-grow">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="px-2 py-0.5 text-[10px] font-bold bg-red-600 text-white rounded uppercase tracking-wider">Tinggi</span>
                    <span class="px-2 py-0.5 text-[10px] font-bold bg-surface border border-border-subtle text-foreground rounded uppercase tracking-wider">PSB</span>
                    <span class="text-xs text-muted-foreground font-medium flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        15 Mar 2026
                    </span>
                </div>
                <h2 class="text-lg font-bold text-foreground mb-2 leading-snug">
                    Pengumuman Kelulusan Seleksi Santri Baru (PSB) Gelombang 1 Tahun 2026/2027
                </h2>
                <p class="text-sm text-muted-foreground mb-4">
                    Berdasarkan hasil tes tertulis dan wawancara, berikut dilampirkan Surat Keputusan (SK) Kelulusan PSB Gelombang 1. Bagi wali santri yang putranya dinyatakan lulus, harap segera melakukan daftar ulang paling lambat tanggal 25 Maret 2026.
                </p>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="#" class="btn bg-red-600 hover:bg-red-700 text-white px-4 py-2 text-sm shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Unduh SK Kelulusan (PDF)
                    </a>
                    <div class="text-xs text-muted-foreground font-medium">Berlaku s/d: 30 Mar 2026</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar Pengumuman --}}
    <div>
        <h3 class="text-sm font-bold text-muted-foreground uppercase tracking-wider mb-4">Daftar Edaran Resmi</h3>
        
        <div class="card p-0 overflow-hidden">
            <div class="divide-y divide-border-subtle bg-surface">
                {{-- Item 1 --}}
                <div class="p-5 hover:bg-surface-muted/50 transition-colors flex flex-col md:flex-row gap-5 items-start">
                    <div class="w-12 h-12 rounded-lg border border-border-subtle bg-background flex flex-col items-center justify-center shrink-0">
                        <span class="text-xs font-bold text-muted-foreground uppercase leading-none mb-1">Feb</span>
                        <span class="text-lg font-black text-foreground leading-none">28</span>
                    </div>
                    <div class="flex-grow">
                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                            <span class="px-2 py-0.5 text-[10px] font-bold bg-primary/10 text-primary rounded uppercase tracking-wider">Sedang</span>
                            <span class="px-2 py-0.5 text-[10px] font-bold bg-surface border border-border-subtle text-foreground rounded uppercase tracking-wider">Akademik</span>
                            <span class="text-xs text-muted-foreground">No: 045/SK/YDYM/II/2026</span>
                        </div>
                        <h4 class="text-base font-bold text-foreground mb-1 leading-snug">Edaran Libur Menyambut Bulan Suci Ramadhan 1447 H</h4>
                        <p class="text-sm text-muted-foreground line-clamp-2">Pemberitahuan kepada seluruh wali santri mengenai jadwal libur awal Ramadhan dan teknis penjemputan santri dari asrama.</p>
                    </div>
                    <div class="shrink-0 pt-2 md:pt-0">
                        <a href="#" class="btn btn-outline border-border-subtle text-foreground hover:bg-surface-muted px-3 py-1.5 text-xs shadow-sm flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Unduh (1.2 MB)
                        </a>
                    </div>
                </div>

                {{-- Item 2 --}}
                <div class="p-5 hover:bg-surface-muted/50 transition-colors flex flex-col md:flex-row gap-5 items-start">
                    <div class="w-12 h-12 rounded-lg border border-border-subtle bg-background flex flex-col items-center justify-center shrink-0">
                        <span class="text-xs font-bold text-muted-foreground uppercase leading-none mb-1">Jan</span>
                        <span class="text-lg font-black text-foreground leading-none">15</span>
                    </div>
                    <div class="flex-grow">
                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                            <span class="px-2 py-0.5 text-[10px] font-bold bg-muted flex items-center text-foreground rounded uppercase tracking-wider">Rendah</span>
                            <span class="px-2 py-0.5 text-[10px] font-bold bg-surface border border-border-subtle text-foreground rounded uppercase tracking-wider">Administrasi</span>
                            <span class="text-xs text-muted-foreground">No: 012/SE/YDYM/I/2026</span>
                        </div>
                        <h4 class="text-base font-bold text-foreground mb-1 leading-snug">Pembaruan Nomor Rekening Virtual Account Pembayaran SPP</h4>
                        <p class="text-sm text-muted-foreground line-clamp-2">Menginformasikan adanya migrasi sistem pembayaran SPP ke Virtual Account (VA) BSI untuk mempermudah pengecekan otomatis.</p>
                    </div>
                    <div class="shrink-0 pt-2 md:pt-0">
                        <a href="#" class="btn btn-outline border-border-subtle text-foreground hover:bg-surface-muted px-3 py-1.5 text-xs shadow-sm flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Unduh (850 KB)
                        </a>
                    </div>
                </div>

                {{-- Item 3 --}}
                <div class="p-5 hover:bg-surface-muted/50 transition-colors flex flex-col md:flex-row gap-5 items-start">
                    <div class="w-12 h-12 rounded-lg border border-border-subtle bg-background flex flex-col items-center justify-center shrink-0 opacity-60">
                        <span class="text-xs font-bold text-muted-foreground uppercase leading-none mb-1">Des</span>
                        <span class="text-lg font-black text-foreground leading-none">05</span>
                    </div>
                    <div class="flex-grow opacity-70">
                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                            <span class="px-2 py-0.5 text-[10px] font-bold bg-surface-muted text-muted-foreground rounded uppercase tracking-wider border border-border-subtle">Kadaluarsa</span>
                            <span class="px-2 py-0.5 text-[10px] font-bold bg-surface border border-border-subtle text-foreground rounded uppercase tracking-wider">Umum</span>
                        </div>
                        <h4 class="text-base font-bold text-foreground mb-1 leading-snug">Jadwal Pengambilan Raport Semester Ganjil TA 2025/2026</h4>
                        <p class="text-sm text-muted-foreground line-clamp-2">Jadwal pembagian raport untuk tingkat MTs dan MA beserta undangan pertemuan wali santri komite madrasah.</p>
                    </div>
                    <div class="shrink-0 pt-2 md:pt-0">
                        <a href="#" class="btn btn-ghost px-3 py-1.5 text-xs text-muted-foreground flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Arsip
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Pagination --}}
        <div class="mt-8 flex justify-between items-center">
            <div class="text-sm text-muted-foreground">Menampilkan 1-10 dari 45 edaran</div>
            <nav class="inline-flex rounded-md shadow-sm -space-x-px">
                <a href="#" class="px-3 py-1.5 rounded-l-md border border-border-subtle bg-surface text-muted-foreground hover:bg-surface-muted transition-colors">
                    <span class="sr-only">Previous</span>
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                </a>
                <a href="#" class="z-10 bg-primary border-primary text-primary-foreground px-3 py-1.5 border text-sm font-medium">1</a>
                <a href="#" class="border-border-subtle bg-surface text-foreground hover:bg-surface-muted px-3 py-1.5 border text-sm font-medium transition-colors">2</a>
                <a href="#" class="border-border-subtle bg-surface text-foreground hover:bg-surface-muted px-3 py-1.5 border text-sm font-medium transition-colors">3</a>
                <a href="#" class="px-3 py-1.5 rounded-r-md border border-border-subtle bg-surface text-muted-foreground hover:bg-surface-muted transition-colors">
                    <span class="sr-only">Next</span>
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4-4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                </a>
            </nav>
        </div>
    </div>
</div>
@endsection
