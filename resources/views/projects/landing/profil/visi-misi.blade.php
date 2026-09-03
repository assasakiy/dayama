@extends('web.layouts.app')

@section('title', 'Visi & Misi — Pondok Pesantren Darul Yatama Wal Masakin')
@section('description', 'Visi, misi, dan tujuan pendidikan Pondok Pesantren Darul Yatama Wal Masakin.')

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
                        <span class="ml-1 md:ml-2 font-medium text-white">{{ $title ?? 'Visi & Misi' }}</span>
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
                {{ $pageModel?->sections['hero']['subtitle'] ?? 'Landasan dan arah perjalanan pendidikan Pondok Pesantren Dayama.' }}
            </p>
        </div>
    </div>
    
    <div class="absolute bottom-0 inset-x-0 pointer-events-none z-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto text-background">
            <path d="M0 80V40C240 0 480 0 720 40C960 80 1200 80 1440 40V80H0Z" fill="currentColor"/>
        </svg>
    </div>
</section>

{{-- VISI --}}
<section class="py-12 md:py-20">
    <div class="container-page max-w-4xl">
        <div class="text-center mb-12">
            <span class="badge badge-accent mb-4">Visi</span>
            <h2 class="section-title">Visi Pesantren</h2>
            <div class="section-accent-bar mx-auto"></div>
        </div>

        <div class="bg-surface border border-border-subtle rounded-lg p-8 md:p-12 text-center islamic-pattern-bg">
            <svg class="w-10 h-10 text-primary/40 mx-auto mb-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            <blockquote class="text-xl md:text-2xl font-semibold text-foreground leading-relaxed italic">
                "Menjadi lembaga pendidikan Islam terdepan yang mencetak generasi Qurani, berakhlak mulia, berwawasan luas, dan berdaya saing global."
            </blockquote>
            <p class="text-xs text-muted-foreground/60 mt-6">(Placeholder — perbarui dengan visi resmi pesantren)</p>
        </div>
    </div>
</section>

{{-- MISI --}}
<section class="py-12 md:py-20 bg-surface-muted/50">
    <div class="container-page max-w-4xl">
        <div class="text-center mb-12">
            <span class="badge badge-primary mb-4">Misi</span>
            <h2 class="section-title">Misi Pesantren</h2>
            <div class="section-accent-bar mx-auto"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @php
                $misi = [
                    ['icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'title' => 'Menyelenggarakan pendidikan Islam yang berkualitas dan terpadu antara ilmu agama dan ilmu umum.'],
                    ['icon' => 'M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342', 'title' => 'Membina santri agar hafal Al-Quran dan mampu memahami serta mengamalkan ajaran Islam dalam kehidupan sehari-hari.'],
                    ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'title' => 'Membentuk karakter santri yang berakhlakul karimah, disiplin, mandiri, dan bertanggung jawab.'],
                    ['icon' => 'M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129', 'title' => 'Mengembangkan kemampuan bahasa Arab dan bahasa Inggris sebagai alat komunikasi global.'],
                    ['icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'title' => 'Memberikan akses pendidikan bagi anak-anak yatim dan dhuafa melalui program beasiswa.'],
                    ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'title' => 'Mempersiapkan lulusan yang berdaya saing dan siap berkontribusi bagi masyarakat dan bangsa.'],
                ];
            @endphp

            @foreach($misi as $index => $item)
            <div class="feature-card flex gap-4 items-start">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-primary text-primary-foreground flex items-center justify-center text-sm font-bold">{{ $index + 1 }}</div>
                <div>
                    <p class="text-sm text-foreground leading-relaxed">{{ $item['title'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
        <p class="text-xs text-muted-foreground/60 text-center mt-6">(Placeholder — perbarui dengan misi resmi pesantren)</p>
    </div>
</section>

{{-- NILAI INTI --}}
<section class="py-12 md:py-20">
    <div class="container-page">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="badge badge-primary mb-4">Prinsip</span>
            <h2 class="section-title">Nilai Inti Pendidikan</h2>
            <div class="section-accent-bar mx-auto"></div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 max-w-4xl mx-auto">
            @php
                $nilaiInti = [
                    ['label' => 'Iman', 'desc' => 'Fondasi keimanan yang kokoh'],
                    ['label' => 'Ilmu', 'desc' => 'Penguasaan ilmu yang luas'],
                    ['label' => 'Amal', 'desc' => 'Pengamalan ilmu dalam kehidupan'],
                    ['label' => 'Akhlak', 'desc' => 'Budi pekerti yang mulia'],
                    ['label' => 'Dakwah', 'desc' => 'Menyebarkan kebaikan'],
                ];
            @endphp
            @foreach($nilaiInti as $nilai)
            <div class="text-center p-4 bg-surface border border-border-subtle rounded-lg">
                <div class="w-12 h-12 rounded-full bg-primary-muted text-primary flex items-center justify-center mx-auto mb-3 text-lg font-bold">
                    {{ substr($nilai['label'], 0, 1) }}
                </div>
                <h3 class="font-semibold text-foreground text-sm">{{ $nilai['label'] }}</h3>
                <p class="text-xs text-muted-foreground mt-1">{{ $nilai['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- TUJUAN PENDIDIKAN --}}
<section class="py-12 md:py-20 bg-surface-muted/50 islamic-pattern-bg">
    <div class="container-page max-w-4xl">
        <div class="text-center mb-12">
            <span class="badge badge-primary mb-4">Tujuan</span>
            <h2 class="section-title">Tujuan Pendidikan</h2>
            <div class="section-accent-bar mx-auto"></div>
        </div>
        <div class="space-y-4">
            @php
                $tujuan = [
                    'Menghasilkan lulusan yang hafal Al-Quran minimal 5 juz dan mampu membaca kitab kuning.',
                    'Membentuk santri yang menguasai bahasa Arab dan Inggris secara aktif.',
                    'Mencetak generasi yang memiliki jiwa kepemimpinan dan kewirausahaan.',
                    'Mempersiapkan santri untuk melanjutkan pendidikan di perguruan tinggi terkemuka.',
                    'Mewujudkan masyarakat yang berkeadilan melalui pendidikan inklusif bagi yatim dan dhuafa.',
                ];
            @endphp
            @foreach($tujuan as $item)
            <div class="flex gap-3 items-start">
                <svg class="w-5 h-5 text-primary flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-foreground text-sm leading-relaxed">{{ $item }}</p>
            </div>
            @endforeach
        </div>
        <p class="text-xs text-muted-foreground/60 text-center mt-6">(Placeholder — perbarui dengan tujuan resmi pesantren)</p>
    </div>
</section>

@endsection
