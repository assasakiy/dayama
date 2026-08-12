@extends('web.layouts.app')

@section('title', 'Sambutan Pimpinan — Pondok Pesantren Darul Yatama Wal Masakin')
@section('description', 'Sambutan dan pesan dari Pimpinan Pondok Pesantren Darul Yatama Wal Masakin.')

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
                        <span class="ml-1 md:ml-2 font-medium text-white">{{ $title ?? 'Sambutan Pimpinan' }}</span>
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
                {{ $pageModel?->sections['hero']['subtitle'] ?? 'Pesan dan harapan dari Pimpinan Pondok Pesantren Dayama.' }}
            </p>
        </div>
    </div>
    
    <div class="absolute bottom-0 inset-x-0 pointer-events-none z-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto text-background">
            <path d="M0 80V40C240 0 480 0 720 40C960 80 1200 80 1440 40V80H0Z" fill="currentColor"/>
        </svg>
    </div>
</section>

{{-- SAMBUTAN --}}
<section class="py-12 md:py-20">
    <div class="container-page max-w-5xl">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 lg:gap-12">
            {{-- Foto & Info Pimpinan --}}
            <div class="lg:col-span-1">
                <div class="sticky top-24">
                    <div class="profile-card">
                        <div class="profile-card-photo bg-surface-muted flex items-center justify-center aspect-[3/4]">
                            <svg class="w-24 h-24 text-primary/20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div class="p-5 text-center">
                            <h3 class="font-bold text-foreground text-lg">Nama Pimpinan Pesantren</h3>
                            <p class="text-sm text-primary font-medium mt-1">Pimpinan Pondok Pesantren</p>
                            <div class="h-px bg-border-subtle my-3"></div>
                            <div class="space-y-2 text-sm text-muted-foreground text-left">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                                    <span>Gelar & Pendidikan (Placeholder)</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <span>Pengalaman (Placeholder)</span>
                                </div>
                            </div>
                            <p class="text-xs text-muted-foreground/60 mt-3">(Data akan diperbarui)</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Isi Sambutan --}}
            <div class="lg:col-span-2">
                {{-- Motto --}}
                <div class="bg-primary-muted border border-primary-border rounded-lg p-6 mb-8">
                    <div class="flex items-start gap-3">
                        <svg class="w-8 h-8 text-primary flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                        <div>
                            <p class="text-foreground font-medium italic leading-relaxed">
                                "Pendidikan yang baik bukan hanya mengisi otak dengan ilmu, tetapi juga mengisi hati dengan iman dan akhlak mulia."
                            </p>
                            <p class="text-xs text-muted-foreground mt-2">— Motto Pimpinan (Placeholder)</p>
                        </div>
                    </div>
                </div>

                <h2 class="section-title mb-2">Assalamu'alaikum Warahmatullahi Wabarakatuh</h2>
                <div class="section-accent-bar mb-8"></div>

                <div class="prose-blog">
                    <p>
                        Alhamdulillah, segala puji bagi Allah SWT yang telah melimpahkan rahmat dan karunia-Nya sehingga Pondok Pesantren Darul Yatama Wal Masakin (Dayama) terus dapat menjalankan misi mulianya dalam mencerdaskan generasi umat Islam.
                    </p>
                    <p>
                        Shalawat serta salam semoga senantiasa tercurahkan kepada Nabi Muhammad SAW, keluarga, sahabat, serta seluruh pengikutnya hingga akhir zaman.
                    </p>
                    <p>
                        Pondok Pesantren Dayama hadir sebagai jawaban atas kebutuhan masyarakat akan lembaga pendidikan Islam yang tidak hanya mengajarkan ilmu agama, tetapi juga membekali santri dengan pengetahuan umum dan keterampilan hidup yang relevan di era modern ini.
                    </p>
                    <p>
                        Kami berkomitmen untuk terus meningkatkan kualitas pendidikan, memperbaiki fasilitas, dan memperluas program-program yang bermanfaat bagi santri dan masyarakat. Kami percaya bahwa setiap anak berhak mendapatkan pendidikan terbaik, terlepas dari latar belakang ekonomi keluarganya.
                    </p>
                    <p>
                        Kepada seluruh pihak yang telah mendukung perjalanan Dayama — para dermawan, orang tua santri, tenaga pengajar, dan seluruh elemen masyarakat — saya mengucapkan terima kasih yang tak terhingga. Semoga amal ibadah kita semua diterima oleh Allah SWT.
                    </p>
                    <p>
                        Mari bersama-sama kita wujudkan generasi yang cerdas, beriman, dan berakhlak mulia demi kejayaan Islam dan kemajuan bangsa.
                    </p>
                    <p class="font-semibold">Wassalamu'alaikum Warahmatullahi Wabarakatuh</p>
                </div>

                <p class="text-xs text-muted-foreground/60 mt-6 italic">(Konten sambutan ini adalah placeholder — silakan perbarui dengan sambutan resmi dari pimpinan pesantren)</p>
            </div>
        </div>
    </div>
</section>

@endsection
