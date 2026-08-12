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
                        <span class="ml-1 md:ml-2 font-medium text-white">Pendidikan</span>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-white/60" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 md:ml-2 font-medium text-white">{{ $title ?? 'Program Pendidikan Unggulan' }}</span>
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
                {{ $pageModel?->sections['hero']['subtitle'] ?? 'Kami menghadirkan program pendidikan yang komprehensif, mengintegrasikan kurikulum pesantren salaf dengan keterampilan abad 21 untuk mencetak generasi Islami yang siap menghadapi tantangan global.' }}
            </p>
        </div>
    </div>
    
    <div class="absolute bottom-0 inset-x-0 pointer-events-none z-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto text-background">
            <path d="M0 80V40C240 0 480 0 720 40C960 80 1200 80 1440 40V80H0Z" fill="currentColor"/>
        </svg>
    </div>
</section>

{{-- Main Content - Grid Program --}}
<div class="container-page py-12 md:py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            {{-- Program 1: Tahfidz Al-Qur'an --}}
            <div class="feature-card p-0 overflow-hidden group flex flex-col h-full">
                <div class="h-48 bg-muted relative overflow-hidden">
                    {{-- Placeholder Image --}}
                    <div class="absolute inset-0 bg-primary/20 flex items-center justify-center text-primary group-hover:scale-105 transition-transform duration-500">
                        <svg class="w-16 h-16 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div class="absolute top-4 left-4">
                        <span class="px-2.5 py-1 text-xs font-bold bg-white text-primary rounded shadow-sm uppercase tracking-wider">Tahfidz</span>
                    </div>
                </div>
                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="text-2xl font-bold text-foreground mb-3 group-hover:text-primary transition-colors">Tahfidz Al-Qur'an</h3>
                    <p class="text-muted-foreground text-sm mb-6 flex-grow">Program hafalan Al-Qur'an 30 Juz dengan metode mutqin (kuat) disertai pemahaman tajwid dan tahsin yang bersanad.</p>
                    
                    <div class="space-y-2 mb-6">
                        <div class="flex items-center gap-2 text-sm text-foreground">
                            <svg class="w-4 h-4 text-secondary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span class="font-medium">Target:</span> Hufadz 30 Juz (3-6 Tahun)
                        </div>
                        <div class="flex items-center gap-2 text-sm text-foreground">
                            <svg class="w-4 h-4 text-secondary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span class="font-medium">Lembaga:</span> Pondok Pesantren
                        </div>
                    </div>
                    
                    <a href="{{ $landingUrl ?? 'http://test-blog.test' }}/pendidikan/lembaga/pondok-pesantren" class="btn btn-outline w-full justify-center group-hover:bg-primary group-hover:text-primary-foreground group-hover:border-primary transition-all">Pelajari Selengkapnya</a>
                </div>
            </div>

            {{-- Program 2: Kajian Kitab Kuning --}}
            <div class="feature-card p-0 overflow-hidden group flex flex-col h-full">
                <div class="h-48 bg-muted relative overflow-hidden">
                    <div class="absolute inset-0 bg-secondary/20 flex items-center justify-center text-secondary group-hover:scale-105 transition-transform duration-500">
                        <svg class="w-16 h-16 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div class="absolute top-4 left-4">
                        <span class="px-2.5 py-1 text-xs font-bold bg-white text-secondary rounded shadow-sm uppercase tracking-wider">Salafiyah</span>
                    </div>
                </div>
                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="text-2xl font-bold text-foreground mb-3 group-hover:text-primary transition-colors">Kajian Kitab Kuning</h3>
                    <p class="text-muted-foreground text-sm mb-6 flex-grow">Pendalaman literatur klasik Islam dengan metode bandongan dan sorogan untuk penguasaan fiqih, tauhid, tasawuf, dan ushuluddin.</p>
                    
                    <div class="space-y-2 mb-6">
                        <div class="flex items-center gap-2 text-sm text-foreground">
                            <svg class="w-4 h-4 text-secondary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span class="font-medium">Target:</span> Mampu membaca & memahami Turats
                        </div>
                        <div class="flex items-center gap-2 text-sm text-foreground">
                            <svg class="w-4 h-4 text-secondary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span class="font-medium">Lembaga:</span> Madrasah Diniyah
                        </div>
                    </div>
                    
                    <a href="{{ $landingUrl ?? 'http://test-blog.test' }}/pendidikan/lembaga/madrasah-diniyah" class="btn btn-outline w-full justify-center group-hover:bg-primary group-hover:text-primary-foreground group-hover:border-primary transition-all">Pelajari Selengkapnya</a>
                </div>
            </div>

            {{-- Program 3: Bahasa Arab & Inggris --}}
            <div class="feature-card p-0 overflow-hidden group flex flex-col h-full">
                <div class="h-48 bg-muted relative overflow-hidden">
                    <div class="absolute inset-0 bg-accent/20 flex items-center justify-center text-accent-foreground group-hover:scale-105 transition-transform duration-500">
                        <svg class="w-16 h-16 opacity-50 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                    </div>
                    <div class="absolute top-4 left-4">
                        <span class="px-2.5 py-1 text-xs font-bold bg-white text-accent rounded shadow-sm uppercase tracking-wider">Bilingual</span>
                    </div>
                </div>
                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="text-2xl font-bold text-foreground mb-3 group-hover:text-primary transition-colors">Bahasa Arab & Inggris</h3>
                    <p class="text-muted-foreground text-sm mb-6 flex-grow">Program intensif penguasaan bahasa asing aktif (muhadatsah/conversation) sebagai bahasa pengantar di area pondok.</p>
                    
                    <div class="space-y-2 mb-6">
                        <div class="flex items-center gap-2 text-sm text-foreground">
                            <svg class="w-4 h-4 text-secondary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span class="font-medium">Target:</span> Komunikasi lisan & tulisan aktif
                        </div>
                        <div class="flex items-center gap-2 text-sm text-foreground">
                            <svg class="w-4 h-4 text-secondary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span class="font-medium">Lembaga:</span> Semua Tingkatan
                        </div>
                    </div>
                    
                    <a href="{{ $landingUrl ?? 'http://test-blog.test' }}/pendidikan/lembaga/pondok-pesantren" class="btn btn-outline w-full justify-center group-hover:bg-primary group-hover:text-primary-foreground group-hover:border-primary transition-all">Pelajari Selengkapnya</a>
                </div>
            </div>

            {{-- Program 4: Life Skill & Teknologi Informasi --}}
            <div class="feature-card p-0 overflow-hidden group flex flex-col h-full">
                <div class="h-48 bg-muted relative overflow-hidden">
                    <div class="absolute inset-0 bg-primary/20 flex items-center justify-center text-primary group-hover:scale-105 transition-transform duration-500">
                        <svg class="w-16 h-16 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="absolute top-4 left-4">
                        <span class="px-2.5 py-1 text-xs font-bold bg-white text-primary rounded shadow-sm uppercase tracking-wider">Vokasi</span>
                    </div>
                </div>
                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="text-2xl font-bold text-foreground mb-3 group-hover:text-primary transition-colors">Teknologi & Life Skill</h3>
                    <p class="text-muted-foreground text-sm mb-6 flex-grow">Pembekalan keterampilan vokasional, kewirausahaan, multimedia, dan desain grafis untuk kemandirian santri.</p>
                    
                    <div class="space-y-2 mb-6">
                        <div class="flex items-center gap-2 text-sm text-foreground">
                            <svg class="w-4 h-4 text-secondary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span class="font-medium">Target:</span> Siap kerja & wirausaha
                        </div>
                        <div class="flex items-center gap-2 text-sm text-foreground">
                            <svg class="w-4 h-4 text-secondary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span class="font-medium">Lembaga:</span> SMK & Pondok Pesantren
                        </div>
                    </div>
                    
                    <a href="{{ $landingUrl ?? 'http://test-blog.test' }}/pendidikan/lembaga/smk" class="btn btn-outline w-full justify-center group-hover:bg-primary group-hover:text-primary-foreground group-hover:border-primary transition-all">Pelajari Selengkapnya</a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
