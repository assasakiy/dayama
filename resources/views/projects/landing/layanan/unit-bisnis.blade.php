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
                        <span class="ml-1 md:ml-2 font-medium">Layanan</span>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-white/60" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 md:ml-2 font-medium text-white">{{ $title ?? 'Unit Bisnis & Kewirausahaan Dayama' }}</span>
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
                {{ $pageModel?->sections['hero']['subtitle'] ?? 'Selain fokus pada pendidikan spiritual dan akademis, Pondok Pesantren Darul Yatama Wal-Masakin juga membekali santri dengan jiwa wirausaha melalui pengelolaan unit-unit bisnis yang menopang kemandirian ekonomi pesantren.' }}
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

    {{-- Daftar Unit Bisnis --}}
    <div id="katalog" class="mb-20">
        <div class="text-center mb-12">
            <h2 class="section-title">Katalog Unit Usaha</h2>
            <p class="section-subtitle">Keuntungan dari seluruh unit usaha dikelola penuh untuk operasional pesantren dan beasiswa santri Yatim-Piatu.</p>
            <div class="section-accent-bar mx-auto"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {{-- Unit 1: Koperasi --}}
            <div class="feature-card p-0 overflow-hidden group flex flex-col h-full bg-surface">
                <div class="h-48 relative overflow-hidden bg-muted">
                    <img src="https://placehold.co/800x400/f8fafc/64748b?text=Foto+Kopontren" alt="Kopontren" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-4 left-4">
                        <h3 class="text-2xl font-bold text-white">Kopontren Dayama</h3>
                    </div>
                </div>
                <div class="p-6 flex flex-col flex-grow">
                    <p class="text-muted-foreground text-sm mb-4 line-clamp-3">Menyediakan berbagai kebutuhan santri dan masyarakat umum, mulai dari kitab suci, buku pelajaran, seragam, sembako, hingga perlengkapan asrama harian dengan harga terjangkau.</p>
                    
                    <h4 class="text-sm font-bold text-foreground mb-2">Layanan Utama:</h4>
                    <ul class="text-sm text-muted-foreground space-y-1 mb-6 list-disc list-inside flex-grow">
                        <li>Mini Market Santri</li>
                        <li>Penyediaan Seragam & Atribut</li>
                        <li>Toko Kitab & Alat Tulis</li>
                    </ul>
                    
                    <a href="#" class="btn btn-primary w-full justify-center mt-auto">Kunjungi Toko Online</a>
                </div>
            </div>

            {{-- Unit 2: Air Minum Dalam Kemasan --}}
            <div class="feature-card p-0 overflow-hidden group flex flex-col h-full bg-surface">
                <div class="h-48 relative overflow-hidden bg-muted">
                    <img src="https://placehold.co/800x400/f8fafc/64748b?text=Pabrik+Air+Minum" alt="Air Minum" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-4 left-4">
                        <span class="px-2 py-1 text-[10px] font-bold bg-accent text-accent-foreground rounded uppercase tracking-wider mb-2 inline-block">Produk Unggulan</span>
                        <h3 class="text-2xl font-bold text-white">AMDK "Dayama Tirta"</h3>
                    </div>
                </div>
                <div class="p-6 flex flex-col flex-grow">
                    <p class="text-muted-foreground text-sm mb-4 line-clamp-3">Produksi Air Minum Dalam Kemasan (AMDK) yang telah bersertifikat SNI dan Halal MUI. Diproses menggunakan teknologi Reverse Osmosis untuk menjamin kualitas dan kesegaran air.</p>
                    
                    <h4 class="text-sm font-bold text-foreground mb-2">Varian Produk:</h4>
                    <ul class="text-sm text-muted-foreground space-y-1 mb-6 list-disc list-inside flex-grow">
                        <li>Gelas 220ml (Karton)</li>
                        <li>Botol 330ml & 600ml</li>
                        <li>Galon 19 Liter</li>
                    </ul>
                    
                    <a href="#" class="btn btn-primary w-full justify-center mt-auto">Pesan Sekarang (Grosir)</a>
                </div>
            </div>

            {{-- Unit 3: Agribisnis & Peternakan --}}
            <div class="feature-card p-0 overflow-hidden group flex flex-col h-full bg-surface">
                <div class="h-48 relative overflow-hidden bg-muted">
                    <img src="https://placehold.co/800x400/f8fafc/64748b?text=Agribisnis" alt="Agribisnis" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-4 left-4">
                        <h3 class="text-2xl font-bold text-white">Dayama Agro</h3>
                    </div>
                </div>
                <div class="p-6 flex flex-col flex-grow">
                    <p class="text-muted-foreground text-sm mb-4 line-clamp-3">Divisi agribisnis yang mengelola perkebunan sayur hidroponik, peternakan unggas petelur, dan budidaya perikanan air tawar untuk mensuplai dapur umum pesantren.</p>
                    
                    <h4 class="text-sm font-bold text-foreground mb-2">Fokus Budidaya:</h4>
                    <ul class="text-sm text-muted-foreground space-y-1 mb-6 list-disc list-inside flex-grow">
                        <li>Sayuran Organik & Hidroponik</li>
                        <li>Telur Ayam Ras</li>
                        <li>Ikan Nila & Lele Konsumsi</li>
                    </ul>
                    
                    <a href="#" class="btn btn-outline w-full justify-center mt-auto bg-surface">Pelajari Divisi Agro</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Kemitraan (CTA) --}}
    <div id="kemitraan" class="mb-8">
        <div class="card p-8 lg:p-12 flex flex-col md:flex-row items-center gap-10">
            <div class="w-full md:w-2/3">
                <h2 class="text-3xl font-bold text-foreground mb-4">Tertarik Menjalin Kemitraan?</h2>
                <p class="text-muted-foreground mb-6">
                    Kami membuka peluang kerja sama yang saling menguntungkan (Simbiosis Mutualisme) dengan berbagai instansi, perusahaan (CSR), maupun perorangan dalam mengembangkan unit bisnis Dayama, sistem waralaba/distributor produk AMDK, maupun penyaluran dana bergulir wirausaha alumni.
                </p>
                <div class="flex items-center gap-4">
                    <a href="{{ $landingUrl ?? 'http://test-blog.test' }}/layanan/kontak" class="btn btn-primary">Hubungi Tim Bisnis Kami</a>
                    <a href="#" class="btn btn-ghost text-muted-foreground hover:text-foreground">Unduh Proposal Profil Bisnis (PDF)</a>
                </div>
            </div>
            <div class="w-full md:w-1/3 flex justify-center">
                <svg class="w-40 h-40 text-muted-foreground/30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>
    </div>
</div>
@endsection
