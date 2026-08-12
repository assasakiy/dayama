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
                        <span class="ml-1 md:ml-2 font-medium">Pendidikan</span>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-white/60" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 md:ml-2 font-medium text-white">{{ $title ?? 'Prestasi Santri & Kelembagaan' }}</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <div class="max-w-4xl">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-black tracking-tight text-white mb-6 leading-tight">
                {!! $pageModel?->sections['hero']['title'] ?? $title ?? '' !!}
                @if(!empty($pageModel?->sections['hero']['highlight']))
                <span class="block text-primary">{!! $pageModel->sections['hero']['highlight'] !!}</span>
                @endif
            </h1>
            <p class="text-lg md:text-xl text-white/90 leading-relaxed max-w-3xl">
                {{ $pageModel?->sections['hero']['subtitle'] ?? 'Dedikasi, kerja keras, dan doa mengantarkan santri Dayama meraih berbagai pencapaian gemilang di tingkat Regional hingga Nasional.' }}
            </p>
        </div>
    </div>
    
    <div class="absolute bottom-0 inset-x-0 pointer-events-none z-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto text-background">
            <path d="M0 80V40C240 0 480 0 720 40C960 80 1200 80 1440 40V80H0Z" fill="currentColor"/>
        </svg>
    </div>
</section>

<div class="container-page py-12 md:py-16">
    {{-- Filter Tabs --}}
    <div class="flex justify-center mb-12">
        <div class="bg-surface rounded-full shadow-sm border border-border-subtle p-1.5 inline-flex overflow-x-auto hide-scrollbar max-w-full">
            <button class="px-6 py-2 rounded-full bg-primary text-primary-foreground text-sm font-medium whitespace-nowrap shadow-sm">Semua</button>
            <button class="px-6 py-2 rounded-full text-foreground hover:bg-surface-muted text-sm font-medium whitespace-nowrap transition-colors">Nasional</button>
            <button class="px-6 py-2 rounded-full text-foreground hover:bg-surface-muted text-sm font-medium whitespace-nowrap transition-colors">Provinsi</button>
            <button class="px-6 py-2 rounded-full text-foreground hover:bg-surface-muted text-sm font-medium whitespace-nowrap transition-colors">Kabupaten</button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        
        {{-- Left Column: Highlight Grid (Masonry effect simulated) --}}
        <div class="lg:col-span-8">
            <h2 class="text-2xl font-bold text-foreground mb-6 flex items-center gap-3">
                <svg class="w-6 h-6 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                Sorotan Prestasi Terbaru
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Card 1 (Large) --}}
                <div class="md:col-span-2 feature-card p-0 overflow-hidden group flex flex-col md:flex-row h-full">
                    <div class="md:w-1/2 aspect-video md:aspect-auto relative overflow-hidden bg-muted">
                        <div class="absolute inset-0 bg-secondary/20 flex items-center justify-center text-secondary">
                            <svg class="w-16 h-16 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 text-xs font-bold bg-accent text-accent-foreground rounded shadow-sm uppercase tracking-wider">Nasional</span>
                        </div>
                    </div>
                    <div class="md:w-1/2 p-6 md:p-8 flex flex-col justify-center">
                        <div class="text-sm font-semibold text-primary mb-2">2026</div>
                        <h3 class="text-2xl font-bold text-foreground mb-3">Juara 1 Lomba MQK (Musabaqah Qira'atil Kutub) Tingkat Nasional</h3>
                        <p class="text-muted-foreground text-sm mb-4">Tim MQK Putra Dayama berhasil meraih juara pertama pada perhelatan MQK Nasional di Jakarta, menyisihkan 34 provinsi lainnya.</p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm">
                                MQ
                            </div>
                            <div>
                                <div class="text-sm font-bold text-foreground">Ahmad Dzaki & Tim</div>
                                <div class="text-xs text-muted-foreground">Kategori Fiqih Ulya</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 2 --}}
                <div class="feature-card p-0 overflow-hidden group flex flex-col h-full">
                    <div class="aspect-video relative overflow-hidden bg-muted">
                        <div class="absolute inset-0 bg-primary/20 flex items-center justify-center text-primary">
                            <svg class="w-12 h-12 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 text-xs font-bold bg-secondary text-secondary-foreground rounded shadow-sm uppercase tracking-wider">Provinsi</span>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <div class="text-sm font-semibold text-primary mb-2">2025</div>
                        <h3 class="text-lg font-bold text-foreground mb-3 leading-snug">Juara Umum POSPEDA NTB</h3>
                        <p class="text-muted-foreground text-sm mb-4 flex-grow">Kontingen Dayama memborong 12 Emas pada Pekan Olahraga dan Seni Pondok Pesantren Daerah (POSPEDA).</p>
                        <div class="text-sm font-bold text-foreground">Kontingen Dayama</div>
                    </div>
                </div>

                {{-- Card 3 --}}
                <div class="feature-card p-0 overflow-hidden group flex flex-col h-full">
                    <div class="aspect-video relative overflow-hidden bg-muted">
                        <div class="absolute inset-0 bg-blue-500/20 flex items-center justify-center text-blue-500">
                            <svg class="w-12 h-12 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 text-xs font-bold bg-blue-500 text-white rounded shadow-sm uppercase tracking-wider">Kabupaten</span>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <div class="text-sm font-semibold text-primary mb-2">2025</div>
                        <h3 class="text-lg font-bold text-foreground mb-3 leading-snug">Medali Emas Olimpiade Sains (OSN) Matematika</h3>
                        <p class="text-muted-foreground text-sm mb-4 flex-grow">Siswa MA Dayama meraih medali emas pada OSN Tingkat Kabupaten Lombok Timur.</p>
                        <div class="text-sm font-bold text-foreground">Bima Al-Fatih</div>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 text-center">
                <button class="btn btn-outline">Muat Lebih Banyak</button>
            </div>
        </div>

        {{-- Right Column: Timeline --}}
        <div class="lg:col-span-4">
            <div class="card p-6 sticky top-24">
                <h2 class="text-xl font-bold text-foreground mb-6 pb-4 border-b border-border-subtle">
                    Rekam Jejak Prestasi
                </h2>
                
                <div class="relative border-l-2 border-primary/20 ml-3 space-y-8">
                    {{-- Timeline Item 1 --}}
                    <div class="relative pl-6">
                        <div class="absolute w-3 h-3 bg-primary rounded-full -left-[7px] top-1.5 ring-4 ring-primary/20"></div>
                        <div class="text-sm font-bold text-primary mb-1">Tahun 2026</div>
                        <ul class="space-y-3">
                            <li class="text-sm">
                                <div class="font-bold text-foreground">Juara 1 MQK Nasional</div>
                                <div class="text-muted-foreground text-xs">Kementerian Agama RI</div>
                            </li>
                            <li class="text-sm">
                                <div class="font-bold text-foreground">Juara 2 Tahfidz 30 Juz Nasional</div>
                                <div class="text-muted-foreground text-xs">LPTQ Nasional</div>
                            </li>
                        </ul>
                    </div>
                    
                    {{-- Timeline Item 2 --}}
                    <div class="relative pl-6">
                        <div class="absolute w-3 h-3 bg-muted-foreground rounded-full -left-[7px] top-1.5 border-2 border-surface"></div>
                        <div class="text-sm font-bold text-foreground mb-1">Tahun 2025</div>
                        <ul class="space-y-3">
                            <li class="text-sm">
                                <div class="font-bold text-foreground">Juara Umum POSPEDA NTB</div>
                                <div class="text-muted-foreground text-xs">Pemprov NTB</div>
                            </li>
                            <li class="text-sm">
                                <div class="font-bold text-foreground">Medali Emas OSN Matematika</div>
                                <div class="text-muted-foreground text-xs">Kabupaten Lombok Timur</div>
                            </li>
                            <li class="text-sm">
                                <div class="font-bold text-foreground">Juara 1 Pidato Bahasa Arab</div>
                                <div class="text-muted-foreground text-xs">Universitas Mataram</div>
                            </li>
                        </ul>
                    </div>
                    
                    {{-- Timeline Item 3 --}}
                    <div class="relative pl-6">
                        <div class="absolute w-3 h-3 bg-muted-foreground rounded-full -left-[7px] top-1.5 border-2 border-surface"></div>
                        <div class="text-sm font-bold text-foreground mb-1">Tahun 2024</div>
                        <ul class="space-y-3">
                            <li class="text-sm">
                                <div class="font-bold text-foreground">Pondok Pesantren Inovatif Terpilih</div>
                                <div class="text-muted-foreground text-xs">Gubernur NTB</div>
                            </li>
                            <li class="text-sm">
                                <div class="font-bold text-foreground">Juara 1 Lomba Kaligrafi</div>
                                <div class="text-muted-foreground text-xs">Festival Seni Islami</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection
