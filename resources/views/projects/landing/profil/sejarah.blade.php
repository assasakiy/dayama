@extends('web.layouts.app')

@section('title', 'Sejarah — Pondok Pesantren Darul Yatama Wal Masakin')
@section('description', 'Perjalanan sejarah Pondok Pesantren Darul Yatama Wal Masakin dari awal berdiri hingga saat ini.')

@php
    $landingDomain = config('projects.projects.landing.domain', env('DOMAIN_MAIN', 'test-blog.test'));
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
                        <span class="ml-1 md:ml-2 font-medium text-white">{{ $title ?? 'Sejarah Dayama' }}</span>
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
                {{ $pageModel?->sections['hero']['subtitle'] ?? 'Perjalanan panjang membangun lembaga pendidikan Islam yang berdedikasi untuk umat.' }}
            </p>
        </div>
    </div>
    
    <div class="absolute bottom-0 inset-x-0 pointer-events-none z-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto text-background">
            <path d="M0 80V40C240 0 480 0 720 40C960 80 1200 80 1440 40V80H0Z" fill="currentColor"/>
        </svg>
    </div>
</section>

{{-- PENGANTAR --}}
<section class="py-12 md:py-16">
    <div class="container-page max-w-4xl">
        <div class="text-center">
            <span class="badge badge-primary mb-4">Perjalanan Kami</span>
            <h2 class="section-title">Dari Pengajian Kecil Menuju Pesantren Modern</h2>
            <div class="section-accent-bar mx-auto"></div>
            <p class="mt-6 text-muted-foreground leading-relaxed max-w-2xl mx-auto">
                Pondok Pesantren Darul Yatama Wal Masakin memiliki sejarah panjang yang bermula dari kepedulian mendalam terhadap nasib anak-anak yatim dan keluarga kurang mampu. Berikut adalah tonggak-tonggak penting dalam perjalanan kami.
            </p>
        </div>
    </div>
</section>

{{-- TIMELINE --}}
<section class="py-8 md:py-16">
    <div class="container-page max-w-3xl">
        <div class="timeline">
            {{-- Item 1 --}}
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-year mb-2">Awal Mula</div>
                <h3 class="font-semibold text-foreground text-lg mb-2">Berdirinya Pengajian Komunitas</h3>
                <p class="text-sm text-muted-foreground leading-relaxed">
                    Bermula dari pengajian kecil yang dirintis oleh para tokoh agama dan masyarakat setempat. Kegiatan ini awalnya dilakukan di rumah-rumah warga dan mushola sederhana dengan jumlah santri yang masih sedikit.
                </p>
                <div class="mt-3 aspect-[16/9] max-w-sm rounded-md bg-surface-muted border border-border-subtle flex items-center justify-center">
                    <p class="text-xs text-muted-foreground/60">Foto Dokumentasi Awal (Placeholder)</p>
                </div>
            </div>

            {{-- Item 2 --}}
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-year mb-2">Peresmian Yayasan</div>
                <h3 class="font-semibold text-foreground text-lg mb-2">Pendirian Yayasan Secara Resmi</h3>
                <p class="text-sm text-muted-foreground leading-relaxed">
                    Yayasan Darul Yatama Wal Masakin diresmikan sebagai badan hukum. Dengan status hukum ini, yayasan mulai memperluas programnya dan mendirikan gedung pesantren pertama dengan fasilitas asrama sederhana.
                </p>
            </div>

            {{-- Item 3 --}}
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-year mb-2">Pembangunan Infrastruktur</div>
                <h3 class="font-semibold text-foreground text-lg mb-2">Pengembangan Fasilitas Pesantren</h3>
                <p class="text-sm text-muted-foreground leading-relaxed">
                    Berkat dukungan para dermawan, Dayama berhasil membangun gedung madrasah, asrama putra dan putri, masjid, perpustakaan, dan fasilitas pendukung lainnya. Jumlah santri mulai bertambah secara signifikan.
                </p>
            </div>

            {{-- Item 4 --}}
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-year mb-2">Akreditasi & Pengakuan</div>
                <h3 class="font-semibold text-foreground text-lg mb-2">Mendapatkan Akreditasi Resmi</h3>
                <p class="text-sm text-muted-foreground leading-relaxed">
                    Madrasah di bawah naungan Dayama berhasil memperoleh akreditasi dari Badan Akreditasi Nasional, mengukuhkan kualitas pendidikan yang ditawarkan. Pesantren juga mendapat berbagai pengakuan dari pemerintah dan lembaga terkait.
                </p>
            </div>

            {{-- Item 5 --}}
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-year mb-2">Era Modern</div>
                <h3 class="font-semibold text-foreground text-lg mb-2">Transformasi Digital & Ekspansi Program</h3>
                <p class="text-sm text-muted-foreground leading-relaxed">
                    Dayama bertransformasi menjadi pesantren modern dengan integrasi teknologi informasi. Program pendidikan diperluas meliputi tahfidz, bahasa asing, keterampilan digital, dan kewirausahaan. Kini Dayama menampung ratusan santri dari berbagai daerah di Indonesia.
                </p>
            </div>

            {{-- Item 6 --}}
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-year mb-2">Saat Ini & Masa Depan</div>
                <h3 class="font-semibold text-foreground text-lg mb-2">Terus Bertumbuh & Berdampak</h3>
                <p class="text-sm text-muted-foreground leading-relaxed">
                    Dengan lebih dari 500 santri aktif dan ribuan alumni yang tersebar di seluruh Indonesia, Dayama terus berkomitmen untuk memberikan pendidikan terbaik bagi generasi Islam. Rencana pengembangan mencakup penambahan jenjang pendidikan, pusat pelatihan, dan program beasiswa yang lebih luas.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- TOKOH PENDIRI --}}
<section class="py-12 md:py-20 bg-surface-muted/50 islamic-pattern-bg">
    <div class="container-page">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="badge badge-accent mb-4">Tokoh</span>
            <h2 class="section-title">Para Pendiri & Perintis</h2>
            <p class="section-subtitle mt-3">Mereka yang telah meletakkan pondasi pertama dengan keikhlasan dan kerja keras.</p>
            <div class="section-accent-bar mx-auto"></div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 max-w-4xl mx-auto">
            @for($i = 1; $i <= 3; $i++)
            <div class="profile-card">
                <div class="profile-card-photo bg-surface-muted flex items-center justify-center">
                    <svg class="w-16 h-16 text-primary/20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div class="p-4 text-center">
                    <h3 class="font-semibold text-foreground">Nama Pendiri {{ $i }}</h3>
                    <p class="text-sm text-muted-foreground mt-1">Pendiri / Perintis Yayasan</p>
                    <p class="text-xs text-muted-foreground/60 mt-1">(Placeholder — data akan diperbarui)</p>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-12 md:py-20">
    <div class="container-page">
        <div class="cta-block islamic-pattern-bg">
            <h2 class="text-2xl md:text-3xl font-bold mb-3">Jadilah Bagian dari Sejarah Kami</h2>
            <p class="text-muted-foreground max-w-xl mx-auto mb-8 text-sm md:text-base">Dukung perjalanan Dayama dalam mencerdaskan anak bangsa melalui pendidikan berkualitas.</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ $landingUrl }}/layanan/psb" class="inline-flex items-center gap-2 bg-white text-primary font-semibold px-7 py-3 rounded-lg shadow-lg hover:bg-white/95 transition-all text-sm">Daftarkan Santri Baru</a>
                <a href="{{ $landingUrl }}/layanan/donasi" class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-sm text-foreground font-semibold px-7 py-3 rounded-lg border border-white/25 hover:bg-white/25 transition-all text-sm">Donasi Sekarang</a>
            </div>
        </div>
    </div>
</section>

@endsection
