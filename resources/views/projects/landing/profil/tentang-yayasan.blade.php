@extends('web.layouts.app')

@section('title', 'Tentang Dayama — Pondok Pesantren Darul Yatama Wal Masakin')
@section('description', 'Mengenal lebih dekat Pondok Pesantren Darul Yatama Wal Masakin (Dayama), lembaga pendidikan Islam yang berdedikasi untuk pendidikan anak yatim dan dhuafa.')

@php
    $landingDomain = config('platform.sites.landing.domain', env('APP_ROOT_DOMAIN', 'dayama.test'));
    $landingUrl = 'http://' . $landingDomain;
@endphp

@section('content')

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
                        <span class="ml-1 md:ml-2 font-medium text-white">{{ $title ?? 'Tentang Dayama' }}</span>
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
                {{ $pageModel?->sections['hero']['subtitle'] ?? 'Mengenal lebih dekat lembaga pendidikan Islam yang berkomitmen untuk mencerdaskan umat.' }}
            </p>
        </div>
    </div>
    
    <div class="absolute bottom-0 inset-x-0 pointer-events-none z-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto text-background">
            <path d="M0 80V40C240 0 480 0 720 40C960 80 1200 80 1440 40V80H0Z" fill="currentColor"/>
        </svg>
    </div>
</section>

{{-- SEJARAH SINGKAT --}}
<section class="py-12 md:py-20">
    <div class="container-page">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-center">
            <div>
                <span class="badge badge-primary mb-4">Sejarah Kami</span>
                <h2 class="section-title">Sejarah Singkat Berdirinya Dayama</h2>
                <div class="section-accent-bar"></div>
                <p class="mt-6 text-muted-foreground leading-relaxed">
                    Pondok Pesantren Darul Yatama Wal Masakin (Dayama) didirikan atas dasar kepedulian terhadap nasib anak-anak yatim dan keluarga dhuafa yang membutuhkan akses pendidikan berkualitas. Berawal dari sebuah pengajian kecil, Dayama terus bertumbuh menjadi lembaga pendidikan Islam yang komprehensif.
                </p>
                <p class="mt-4 text-muted-foreground leading-relaxed">
                    Dengan dukungan para dermawan, tokoh masyarakat, dan kerja keras para pendiri, pesantren ini berhasil membangun infrastruktur pendidikan yang memadai dan program pengajaran yang berkualitas tinggi.
                </p>
                <a href="{{ $landingUrl }}/profil/sejarah" class="btn btn-outline mt-6">
                    Baca Sejarah Lengkap
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
            <div class="aspect-[4/3] rounded-lg bg-surface-muted border border-border-subtle flex items-center justify-center">
                <div class="text-center p-8">
                    <svg class="w-16 h-16 text-primary/30 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                    <p class="text-sm text-muted-foreground">Foto Sejarah Pesantren</p>
                    <p class="text-xs text-muted-foreground/60 mt-1">(Placeholder)</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- TENTANG YAYASAN --}}
<section class="py-12 md:py-20 bg-surface-muted/50 islamic-pattern-bg">
    <div class="container-page">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="badge badge-primary mb-4">Yayasan</span>
            <h2 class="section-title">Tentang Yayasan</h2>
            <p class="section-subtitle mt-3">Yayasan Darul Yatama Wal Masakin merupakan lembaga nirlaba yang menaungi seluruh kegiatan pendidikan dan sosial.</p>
            <div class="section-accent-bar mx-auto"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto">
            <div class="feature-card">
                <h3 class="font-semibold text-foreground mb-2">Nama Yayasan</h3>
                <p class="text-sm text-muted-foreground">Yayasan Darul Yatama Wal Masakin</p>
            </div>
            <div class="feature-card">
                <h3 class="font-semibold text-foreground mb-2">Status Hukum</h3>
                <p class="text-sm text-muted-foreground">Badan Hukum resmi yang terdaftar di Kementerian Hukum dan HAM RI</p>
            </div>
            <div class="feature-card">
                <h3 class="font-semibold text-foreground mb-2">Bidang Kegiatan</h3>
                <p class="text-sm text-muted-foreground">Pendidikan, Dakwah, Sosial & Kemanusiaan</p>
            </div>
            <div class="feature-card">
                <h3 class="font-semibold text-foreground mb-2">Lokasi</h3>
                <p class="text-sm text-muted-foreground">Indonesia (Alamat lengkap akan diperbarui)</p>
            </div>
        </div>
    </div>
</section>

{{-- NILAI-NILAI --}}
<section class="py-12 md:py-20">
    <div class="container-page">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="badge badge-accent mb-4">Prinsip Kami</span>
            <h2 class="section-title">Nilai-Nilai Dayama</h2>
            <div class="section-accent-bar mx-auto"></div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="feature-card text-center">
                <div class="feature-card-icon mx-auto">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h3 class="font-semibold text-foreground mb-1">Keikhlasan</h3>
                <p class="text-sm text-muted-foreground">Mengabdi dengan tulus tanpa pamrih demi keridhaan Allah SWT.</p>
            </div>
            <div class="feature-card text-center">
                <div class="feature-card-icon mx-auto">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3 class="font-semibold text-foreground mb-1">Amanah</h3>
                <p class="text-sm text-muted-foreground">Menjaga kepercayaan yang diberikan dengan penuh tanggung jawab.</p>
            </div>
            <div class="feature-card text-center">
                <div class="feature-card-icon mx-auto">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="font-semibold text-foreground mb-1">Ukhuwah</h3>
                <p class="text-sm text-muted-foreground">Menjalin persaudaraan dan gotong-royong dalam kebaikan.</p>
            </div>
            <div class="feature-card text-center">
                <div class="feature-card-icon mx-auto">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h3 class="font-semibold text-foreground mb-1">Istiqomah</h3>
                <p class="text-sm text-muted-foreground">Konsisten dalam kebaikan dan terus berusaha meningkatkan kualitas.</p>
            </div>
        </div>
    </div>
</section>

{{-- STATISTIK --}}
<section class="py-12 md:py-16 bg-surface-muted/50">
    <div class="container-page">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
            <div class="stat-card">
                <div class="stat-card-number">500+</div>
                <div class="stat-card-label">Santri Aktif</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-number">50+</div>
                <div class="stat-card-label">Tenaga Pengajar</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-number">3</div>
                <div class="stat-card-label">Jenjang Pendidikan</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-number">1000+</div>
                <div class="stat-card-label">Alumni Tersebar</div>
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-12 md:py-20">
    <div class="container-page">
        <div class="cta-block islamic-pattern-bg">
            <h2 class="text-2xl md:text-3xl font-bold mb-3">Bergabunglah Bersama Kami</h2>
            <p class="text-muted-foreground max-w-xl mx-auto mb-8 text-sm md:text-base">Daftarkan putra-putri Anda untuk mendapatkan pendidikan terbaik, atau jadilah bagian dari perjalanan kami melalui donasi.</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ $landingUrl }}/layanan/psb" class="inline-flex items-center gap-2 bg-white text-primary font-semibold px-7 py-3 rounded-lg shadow-lg hover:bg-white/95 transition-all text-sm">Daftarkan Santri Baru</a>
                <a href="{{ $landingUrl }}/layanan/donasi" class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-sm text-foreground font-semibold px-7 py-3 rounded-lg border border-white/25 hover:bg-white/25 transition-all text-sm">Donasi Sekarang</a>
            </div>
        </div>
    </div>
</section>

@endsection
