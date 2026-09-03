@extends('web.layouts.app')

@section('title', 'Legalitas & Akreditasi — Pondok Pesantren Darul Yatama Wal Masakin')
@section('description', 'Legalitas, akreditasi, dan sertifikasi resmi Pondok Pesantren Darul Yatama Wal Masakin.')

@php
    $landingDomain = config('platform.sites.landing.domain', env('APP_ROOT_DOMAIN', 'dayama.test'));
    $landingUrl = 'http://' . $landingDomain;
@endphp

@section('content')

{{-- HERO --}}
{{-- HERO --}}
<section class="hero-subpage py-16 md:py-20 pb-24 md:pb-32 relative z-0">
    <div class="container-page relative z-10">
        <nav class="flex text-sm text-white/80 mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('landing.home') ?? '/' }}" class="inline-flex items-center hover:text-white transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        Beranda
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-white/60" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 md:ml-2 font-medium">Profil</span>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-white/60" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 md:ml-2 font-medium text-white">{{ $title ?? 'Legalitas & Akreditasi' }}</span>
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
                {{ $pageModel?->sections['hero']['subtitle'] ?? 'Dokumen resmi dan pengakuan kualitas pendidikan Pondok Pesantren Dayama.' }}
            </p>
        </div>
    </div>
    
    <div class="absolute bottom-0 inset-x-0 pointer-events-none z-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto text-background">
            <path d="M0 80V40C240 0 480 0 720 40C960 80 1200 80 1440 40V80H0Z" fill="currentColor"/>
        </svg>
    </div>
</section>

{{-- DOKUMEN LEGAL --}}
<section class="py-12 md:py-20">
    <div class="container-page">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="badge badge-primary mb-4">Legalitas</span>
            <h2 class="section-title">Dokumen Legalitas</h2>
            <p class="section-subtitle mt-3">Seluruh dokumen hukum dan perizinan resmi yang mendasari operasional Pondok Pesantren Dayama.</p>
            <div class="section-accent-bar mx-auto"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
            @php
                $legalitas = [
                    ['title' => 'Akta Pendirian Yayasan', 'nomor' => 'No. XX Tahun XXXX', 'status' => 'Aktif', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['title' => 'SK Kemenkumham', 'nomor' => 'AHU-XXXXXXXXXX', 'status' => 'Aktif', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                    ['title' => 'Izin Operasional Pesantren', 'nomor' => 'No. XXX/XXXX/XXXX', 'status' => 'Aktif', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                    ['title' => 'NPSN (Nomor Pokok Sekolah)', 'nomor' => 'XXXXXXXX', 'status' => 'Terdaftar', 'icon' => 'M7 20l4-16m2 16l4-16M6 9h14M4 15h14'],
                    ['title' => 'NSPP (Nomor Statistik Pondok)', 'nomor' => 'XXXXXXXXXXXX', 'status' => 'Terdaftar', 'icon' => 'M7 20l4-16m2 16l4-16M6 9h14M4 15h14'],
                    ['title' => 'NPWP Yayasan', 'nomor' => 'XX.XXX.XXX.X-XXX.XXX', 'status' => 'Aktif', 'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z'],
                ];
            @endphp

            @foreach($legalitas as $doc)
            <div class="feature-card">
                <div class="flex items-start gap-4">
                    <div class="feature-card-icon flex-shrink-0 mb-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $doc['icon'] }}"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-foreground text-sm">{{ $doc['title'] }}</h3>
                        <p class="text-xs text-muted-foreground mt-1 font-mono">{{ $doc['nomor'] }}</p>
                        <span class="badge badge-success mt-2">{{ $doc['status'] }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <p class="text-xs text-muted-foreground/60 text-center mt-6">(Placeholder — nomor dokumen akan diperbarui dengan data resmi)</p>
    </div>
</section>

{{-- AKREDITASI --}}
<section class="py-12 md:py-20 bg-surface-muted/50 islamic-pattern-bg">
    <div class="container-page max-w-4xl">
        <div class="text-center mb-12">
            <span class="badge badge-accent mb-4">Akreditasi</span>
            <h2 class="section-title">Status Akreditasi</h2>
            <div class="section-accent-bar mx-auto"></div>
        </div>

        <div class="bg-background border border-border-subtle rounded-lg p-8 text-center">
            <div class="w-24 h-24 rounded-full bg-primary-muted text-primary flex items-center justify-center mx-auto mb-6 text-4xl font-extrabold">
                A
            </div>
            <h3 class="text-2xl font-bold text-foreground">Terakreditasi "A" (Placeholder)</h3>
            <p class="text-muted-foreground mt-2">Berdasarkan penilaian Badan Akreditasi Nasional</p>
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-lg mx-auto">
                <div class="text-center">
                    <div class="text-lg font-bold text-primary">XX</div>
                    <div class="text-xs text-muted-foreground">Skor Akreditasi</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-bold text-primary">XXXX</div>
                    <div class="text-xs text-muted-foreground">Tahun Perolehan</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-bold text-primary">XXXX</div>
                    <div class="text-xs text-muted-foreground">Berlaku Sampai</div>
                </div>
            </div>
            <p class="text-xs text-muted-foreground/60 mt-6">(Data akreditasi akan diperbarui setelah verifikasi)</p>
        </div>
    </div>
</section>

@endsection
