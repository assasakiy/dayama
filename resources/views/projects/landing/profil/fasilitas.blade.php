@extends('web.layouts.app')

@section('title', 'Fasilitas — Pondok Pesantren Darul Yatama Wal Masakin')
@section('description', 'Fasilitas dan sarana prasarana yang tersedia di Pondok Pesantren Darul Yatama Wal Masakin (Dayama) untuk mendukung kegiatan belajar mengajar santri.')

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
                        <span class="ml-1 md:ml-2 font-medium text-white">{{ $title ?? 'Fasilitas Dayama' }}</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <div class="max-w-4xl">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-black tracking-tight text-white mb-6 leading-tight">
                {!! $pageModel?->sections['hero']['title'] ?? 'Fasilitas & Sarana Prasarana' !!}
                @if(!empty($pageModel?->sections['hero']['highlight']))
                <span class="block text-primary">{!! $pageModel->sections['hero']['highlight'] !!}</span>
                @endif
            </h1>
            <p class="text-lg md:text-xl text-white/90 leading-relaxed max-w-3xl">
                {{ $pageModel?->sections['hero']['subtitle'] ?? 'Kami menyediakan fasilitas yang lengkap, modern, dan nyaman untuk mendukung kegiatan belajar mengajar serta ibadah para santri di Pondok Pesantren Dayama.' }}
            </p>
        </div>
    </div>
    
    <div class="absolute bottom-0 inset-x-0 pointer-events-none z-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto text-background">
            <path d="M0 80V40C240 0 480 0 720 40C960 80 1200 80 1440 40V80H0Z" fill="currentColor"/>
        </svg>
    </div>
</section>

{{-- DAFTAR FASILITAS --}}
<section class="py-12 md:py-20">
    <div class="container-page max-w-6xl">
        <div class="text-center mb-12">
            <span class="badge badge-primary mb-4">Infrastruktur</span>
            <h2 class="section-title">Fasilitas Unggulan</h2>
            <p class="section-subtitle mt-3">Lingkungan yang asri dan fasilitas memadai untuk menunjang prestasi.</p>
            <div class="section-accent-bar mx-auto"></div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                $fasilitas = [
                    ['icon' => '🕌', 'title' => 'Masjid Jami\'', 'desc' => 'Pusat ibadah dan kajian kitab kuning dengan kapasitas yang luas.'],
                    ['icon' => '🏫', 'title' => 'Gedung Sekolah', 'desc' => 'Ruang kelas yang nyaman dan representatif untuk proses KBM.'],
                    ['icon' => '🏢', 'title' => 'Asrama Santri', 'desc' => 'Asrama putra dan putri yang terpisah, bersih, dan diawasi penuh.'],
                    ['icon' => '📚', 'title' => 'Perpustakaan', 'desc' => 'Koleksi buku agama, umum, dan literatur pendukung yang lengkap.'],
                    ['icon' => '💻', 'title' => 'Laboratorium Komputer', 'desc' => 'Fasilitas praktik TIK dengan perangkat komputer modern dan internet.'],
                    ['icon' => '🔬', 'title' => 'Laboratorium IPA', 'desc' => 'Ruang praktik sains untuk jenjang pendidikan menengah.'],
                    ['icon' => '⚽', 'title' => 'Lapangan Olahraga', 'desc' => 'Fasilitas olahraga futsal, voli, dan badminton untuk kebugaran santri.'],
                    ['icon' => '🍽️', 'title' => 'Kantin & Koperasi', 'desc' => 'Menyediakan makanan sehat dan kebutuhan harian santri.'],
                    ['icon' => '🏥', 'title' => 'Klinik Kesehatan', 'desc' => 'Pelayanan medis dasar untuk menjaga kesehatan santri di lingkungan pesantren.'],
                ];
            @endphp

            @foreach($fasilitas as $item)
            <div class="feature-card text-center hover:border-primary/30 hover:shadow-md transition-all duration-300">
                <div class="w-16 h-16 mx-auto rounded-full bg-primary/10 flex items-center justify-center text-3xl mb-4 border border-primary/20">
                    {{ $item['icon'] }}
                </div>
                <h3 class="font-semibold text-foreground mb-2 text-lg">{{ $item['title'] }}</h3>
                <p class="text-sm text-muted-foreground leading-relaxed">{{ $item['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- GALERI --}}
<section class="py-12 md:py-20 bg-surface-muted/50 islamic-pattern-bg">
    <div class="container-page">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="badge badge-accent mb-4">Galeri</span>
            <h2 class="section-title">Potret Lingkungan Dayama</h2>
            <div class="section-accent-bar mx-auto"></div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="aspect-[4/3] rounded-xl bg-surface border border-border-subtle overflow-hidden flex items-center justify-center relative group">
                <div class="absolute inset-0 bg-primary/20 opacity-0 group-hover:opacity-100 transition-opacity z-10 flex items-center justify-center">
                    <span class="text-white font-medium drop-shadow-md">Masjid Utama</span>
                </div>
                <svg class="w-10 h-10 text-muted-foreground/30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div class="aspect-[4/3] rounded-xl bg-surface border border-border-subtle overflow-hidden flex items-center justify-center relative group">
                <div class="absolute inset-0 bg-primary/20 opacity-0 group-hover:opacity-100 transition-opacity z-10 flex items-center justify-center">
                    <span class="text-white font-medium drop-shadow-md">Gedung Asrama</span>
                </div>
                <svg class="w-10 h-10 text-muted-foreground/30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div class="aspect-[4/3] rounded-xl bg-surface border border-border-subtle overflow-hidden flex items-center justify-center relative group">
                <div class="absolute inset-0 bg-primary/20 opacity-0 group-hover:opacity-100 transition-opacity z-10 flex items-center justify-center">
                    <span class="text-white font-medium drop-shadow-md">Ruang Kelas</span>
                </div>
                <svg class="w-10 h-10 text-muted-foreground/30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div class="aspect-[4/3] rounded-xl bg-surface border border-border-subtle overflow-hidden flex items-center justify-center relative group">
                <div class="absolute inset-0 bg-primary/20 opacity-0 group-hover:opacity-100 transition-opacity z-10 flex items-center justify-center">
                    <span class="text-white font-medium drop-shadow-md">Perpustakaan</span>
                </div>
                <svg class="w-10 h-10 text-muted-foreground/30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
        </div>
        
        <p class="text-xs text-muted-foreground/60 text-center mt-6">(Placeholder gambar — galeri dapat diatur kemudian via panel admin)</p>
    </div>
</section>

@endsection
