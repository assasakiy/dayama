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
                        <span class="ml-1 md:ml-2 font-medium text-white">{{ $title ?? 'Guru & Tenaga Kependidikan' }}</span>
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
                {{ $pageModel?->sections['hero']['subtitle'] ?? 'Mengenal lebih dekat para asatidz, pengajar, dan staf yang berdedikasi mendidik generasi emas berakhlaqul karimah.' }}
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
    {{-- Statistics --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
        <div class="stat-card">
            <div class="stat-card-number">120+</div>
            <div class="stat-card-label">Total Pengajar</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-number">85%</div>
            <div class="stat-card-label">Bersertifikasi</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-number">15</div>
            <div class="stat-card-label">Lulusan Timur Tengah</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-number">40+</div>
            <div class="stat-card-label">Staf Administrasi</div>
        </div>
    </div>

    {{-- Filter & Search Section --}}
    <div class="card p-4 flex flex-col md:flex-row gap-4 justify-between items-center mb-8">
        <div class="w-full md:w-1/3 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" class="w-full pl-10 pr-4 py-2 border border-border-strong rounded-lg bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="Cari nama guru...">
        </div>
        <div class="w-full md:w-auto flex overflow-x-auto pb-1 md:pb-0 gap-2 hide-scrollbar">
            <button class="px-4 py-2 rounded-lg bg-primary text-primary-foreground text-sm font-medium whitespace-nowrap">Semua</button>
            <button class="px-4 py-2 rounded-lg bg-surface-muted text-muted-foreground hover:text-foreground hover:bg-surface-muted/80 text-sm font-medium whitespace-nowrap transition-colors">Pondok Pesantren</button>
            <button class="px-4 py-2 rounded-lg bg-surface-muted text-muted-foreground hover:text-foreground hover:bg-surface-muted/80 text-sm font-medium whitespace-nowrap transition-colors">Madrasah Aliyah</button>
            <button class="px-4 py-2 rounded-lg bg-surface-muted text-muted-foreground hover:text-foreground hover:bg-surface-muted/80 text-sm font-medium whitespace-nowrap transition-colors">Madrasah Tsanawiyah</button>
            <button class="px-4 py-2 rounded-lg bg-surface-muted text-muted-foreground hover:text-foreground hover:bg-surface-muted/80 text-sm font-medium whitespace-nowrap transition-colors">SMK</button>
        </div>
    </div>

    {{-- Guru Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        
        {{-- Guru Card 1 --}}
        <div class="profile-card group">
            <div class="aspect-[4/5] bg-muted relative overflow-hidden">
                <img src="https://ui-avatars.com/api/?name=Kyai+Ahmad&background=random&size=400" alt="Kyai Ahmad" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-4 w-full">
                    <span class="inline-block px-2 py-0.5 bg-primary/90 text-primary-foreground text-[10px] font-bold uppercase rounded-sm mb-2 backdrop-blur-sm shadow-sm">Pimpinan Pondok</span>
                    <h3 class="text-lg font-bold text-white mb-0.5">KH. Ahmad Syafii, Lc., M.A.</h3>
                    <p class="text-white/80 text-xs font-medium">Alumni Universitas Al-Azhar, Kairo</p>
                </div>
            </div>
            <div class="p-4 bg-surface">
                <div class="flex items-start gap-2 mb-3">
                    <svg class="w-4 h-4 text-secondary mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <div>
                        <div class="text-[11px] text-muted-foreground uppercase tracking-wider font-semibold">Bidang Studi</div>
                        <div class="text-sm font-medium text-foreground">Tafsir & Hadits</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Guru Card 2 --}}
        <div class="profile-card group">
            <div class="aspect-[4/5] bg-muted relative overflow-hidden">
                <img src="https://ui-avatars.com/api/?name=Ustadz+Budi&background=random&size=400" alt="Ustadz Budi" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-4 w-full">
                    <span class="inline-block px-2 py-0.5 bg-accent/90 text-accent-foreground text-[10px] font-bold uppercase rounded-sm mb-2 backdrop-blur-sm shadow-sm">Kepala Madrasah</span>
                    <h3 class="text-lg font-bold text-white mb-0.5">Ust. Budi Santoso, S.Pd.I</h3>
                    <p class="text-white/80 text-xs font-medium">UIN Sunan Kalijaga</p>
                </div>
            </div>
            <div class="p-4 bg-surface">
                <div class="flex items-start gap-2 mb-3">
                    <svg class="w-4 h-4 text-secondary mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <div>
                        <div class="text-[11px] text-muted-foreground uppercase tracking-wider font-semibold">Bidang Studi</div>
                        <div class="text-sm font-medium text-foreground">Bahasa Arab & Fiqih</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Guru Card 3 --}}
        <div class="profile-card group">
            <div class="aspect-[4/5] bg-muted relative overflow-hidden">
                <img src="https://ui-avatars.com/api/?name=Ustadzah+Siti&background=random&size=400" alt="Ustadzah Siti" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-4 w-full">
                    <span class="inline-block px-2 py-0.5 bg-secondary/90 text-secondary-foreground text-[10px] font-bold uppercase rounded-sm mb-2 backdrop-blur-sm shadow-sm">Pengasuh Putri</span>
                    <h3 class="text-lg font-bold text-white mb-0.5">Ustdz. Siti Aminah, S.Ag.</h3>
                    <p class="text-white/80 text-xs font-medium">IIQ Jakarta</p>
                </div>
            </div>
            <div class="p-4 bg-surface">
                <div class="flex items-start gap-2 mb-3">
                    <svg class="w-4 h-4 text-secondary mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <div>
                        <div class="text-[11px] text-muted-foreground uppercase tracking-wider font-semibold">Bidang Studi</div>
                        <div class="text-sm font-medium text-foreground">Tahfidz & Tajwid</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Guru Card 4 --}}
        <div class="profile-card group">
            <div class="aspect-[4/5] bg-muted relative overflow-hidden">
                <img src="https://ui-avatars.com/api/?name=Pak+Andi&background=random&size=400" alt="Pak Andi" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-4 w-full">
                    <span class="inline-block px-2 py-0.5 bg-blue-500/90 text-white text-[10px] font-bold uppercase rounded-sm mb-2 backdrop-blur-sm shadow-sm">Guru Produktif</span>
                    <h3 class="text-lg font-bold text-white mb-0.5">Andi Setiawan, S.Kom.</h3>
                    <p class="text-white/80 text-xs font-medium">Universitas Brawijaya</p>
                </div>
            </div>
            <div class="p-4 bg-surface">
                <div class="flex items-start gap-2 mb-3">
                    <svg class="w-4 h-4 text-secondary mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <div>
                        <div class="text-[11px] text-muted-foreground uppercase tracking-wider font-semibold">Bidang Studi</div>
                        <div class="text-sm font-medium text-foreground">Teknologi Informasi / RPL</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Pagination --}}
    <div class="mt-12 flex justify-center">
        <nav class="inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
            <a href="#" class="px-3 py-2 rounded-l-md border border-border-subtle bg-surface text-muted-foreground hover:bg-surface-muted transition-colors">
                <span class="sr-only">Previous</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
            </a>
            <a href="#" aria-current="page" class="z-10 bg-primary border-primary text-primary-foreground hover:bg-primary/90 px-4 py-2 border text-sm font-medium">1</a>
            <a href="#" class="border-border-subtle bg-surface text-foreground hover:bg-surface-muted px-4 py-2 border text-sm font-medium transition-colors">2</a>
            <a href="#" class="border-border-subtle bg-surface text-foreground hover:bg-surface-muted px-4 py-2 border text-sm font-medium transition-colors">3</a>
            <span class="border-border-subtle bg-surface text-foreground px-4 py-2 border text-sm font-medium">...</span>
            <a href="#" class="border-border-subtle bg-surface text-foreground hover:bg-surface-muted px-4 py-2 border text-sm font-medium transition-colors">8</a>
            <a href="#" class="px-3 py-2 rounded-r-md border border-border-subtle bg-surface text-muted-foreground hover:bg-surface-muted transition-colors">
                <span class="sr-only">Next</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
            </a>
        </nav>
    </div>
</div>
@endsection
