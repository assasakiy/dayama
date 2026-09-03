@extends('web.layouts.app')

@section('title', 'Mitra Kerja Sama — Pondok Pesantren Darul Yatama Wal Masakin')
@section('description', 'Daftar mitra kerja sama Pondok Pesantren Darul Yatama Wal Masakin dalam bidang pendidikan, sosial, dan dakwah.')

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
                        <span class="ml-1 md:ml-2 font-medium text-white">{{ $title ?? 'Mitra Kerja Sama' }}</span>
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
                {{ $pageModel?->sections['hero']['subtitle'] ?? 'Lembaga dan organisasi yang telah menjalin kemitraan dengan Pesantren Dayama.' }}
            </p>
        </div>
    </div>
    
    <div class="absolute bottom-0 inset-x-0 pointer-events-none z-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto text-background">
            <path d="M0 80V40C240 0 480 0 720 40C960 80 1200 80 1440 40V80H0Z" fill="currentColor"/>
        </svg>
    </div>
</section>

{{-- LOGO MITRA --}}
<section class="py-12 md:py-20">
    <div class="container-page">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="badge badge-primary mb-4">Kemitraan</span>
            <h2 class="section-title">Dipercaya oleh Berbagai Lembaga</h2>
            <p class="section-subtitle mt-3">Kami berkolaborasi dengan berbagai pihak untuk memberikan pendidikan dan layanan terbaik.</p>
            <div class="section-accent-bar mx-auto"></div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6 max-w-4xl mx-auto">
            @php
                $mitra = [
                    ['nama' => 'Kementerian Agama RI', 'bidang' => 'Kurikulum & Akreditasi'],
                    ['nama' => 'Kementerian Pendidikan', 'bidang' => 'Pendidikan Nasional'],
                    ['nama' => 'BAZNAS', 'bidang' => 'Zakat & Beasiswa'],
                    ['nama' => 'Dompet Dhuafa', 'bidang' => 'Sosial & Kemanusiaan'],
                    ['nama' => 'Rumah Zakat', 'bidang' => 'Program Pendidikan'],
                    ['nama' => 'Bank Syariah', 'bidang' => 'Keuangan Syariah'],
                    ['nama' => 'Universitas Mitra', 'bidang' => 'Beasiswa & Lanjut Studi'],
                    ['nama' => 'Pemerintah Daerah', 'bidang' => 'Pembangunan & Sosial'],
                ];
            @endphp

            @foreach($mitra as $item)
            <div class="feature-card text-center py-8">
                <div class="w-16 h-16 rounded-full bg-surface-muted border border-border-subtle flex items-center justify-center mx-auto mb-4">
                    <span class="text-primary font-bold text-lg">{{ substr($item['nama'], 0, 2) }}</span>
                </div>
                <h3 class="font-semibold text-foreground text-sm">{{ $item['nama'] }}</h3>
                <p class="text-xs text-muted-foreground mt-1">{{ $item['bidang'] }}</p>
            </div>
            @endforeach
        </div>
        <p class="text-xs text-muted-foreground/60 text-center mt-6">(Placeholder — logo dan data mitra akan diperbarui)</p>
    </div>
</section>

{{-- TESTIMONI MITRA --}}
<section class="py-12 md:py-20 bg-surface-muted/50 islamic-pattern-bg">
    <div class="container-page max-w-4xl">
        <div class="text-center mb-12">
            <span class="badge badge-accent mb-4">Testimoni</span>
            <h2 class="section-title">Apa Kata Mitra Kami</h2>
            <div class="section-accent-bar mx-auto"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @for($i = 1; $i <= 2; $i++)
            <div class="bg-background border border-border-subtle rounded-lg p-6">
                <svg class="w-6 h-6 text-primary/30 mb-4" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                <p class="text-sm text-foreground leading-relaxed italic mb-4">
                    "Pondok Pesantren Dayama menunjukkan komitmen yang luar biasa dalam membina generasi muda. Kami bangga menjadi bagian dari perjalanan mereka."
                </p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-surface-muted flex items-center justify-center">
                        <span class="text-primary font-bold text-sm">M{{ $i }}</span>
                    </div>
                    <div>
                        <p class="font-semibold text-foreground text-sm">Nama Perwakilan Mitra {{ $i }}</p>
                        <p class="text-xs text-muted-foreground">Jabatan — Lembaga Mitra {{ $i }}</p>
                    </div>
                </div>
            </div>
            @endfor
        </div>
        <p class="text-xs text-muted-foreground/60 text-center mt-6">(Placeholder — testimoni akan diperbarui)</p>
    </div>
</section>

{{-- CTA --}}
<section class="py-12 md:py-20">
    <div class="container-page">
        <div class="cta-block islamic-pattern-bg">
            <h2 class="text-2xl md:text-3xl font-bold mb-3">Tertarik Bermitra dengan Dayama?</h2>
            <p class="text-muted-foreground max-w-xl mx-auto mb-8 text-sm md:text-base">Kami terbuka untuk berbagai bentuk kerja sama yang saling menguntungkan dan bermanfaat bagi masyarakat.</p>
            <a href="{{ $landingUrl }}/layanan/kontak" class="inline-flex items-center gap-2 bg-white text-primary font-semibold px-7 py-3 rounded-lg shadow-lg hover:bg-white/95 transition-all text-sm">Hubungi Kami</a>
        </div>
    </div>
</section>

@endsection
