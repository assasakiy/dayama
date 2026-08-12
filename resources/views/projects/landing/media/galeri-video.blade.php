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
                        <span class="ml-1 md:ml-2 font-medium">Media & Informasi</span>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-white/60" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 md:ml-2 font-medium text-white">{{ $title ?? 'Galeri Video Dayama' }}</span>
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
                {{ $pageModel?->sections['hero']['subtitle'] ?? 'Tonton dokumentasi kegiatan, tausiyah Tuan Guru, profil pesantren, dan liputan khusus.' }}
            </p>
        </div>
    </div>
    
    <div class="absolute bottom-0 inset-x-0 pointer-events-none z-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto text-background">
            <path d="M0 80V40C240 0 480 0 720 40C960 80 1200 80 1440 40V80H0Z" fill="currentColor"/>
        </svg>
    </div>
</section>

<div class="container-page py-12 md:py-16" x-data="{ videoModalOpen: false, activeVideoUrl: '' }">
    {{-- Featured Video --}}
    <div class="mb-16">
        <div class="relative w-full aspect-video rounded-3xl overflow-hidden shadow-2xl group cursor-pointer border border-border-subtle bg-black" @click="activeVideoUrl = 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1'; videoModalOpen = true;">
            {{-- Placeholder Thumbnail --}}
            <img src="https://placehold.co/1280x720/1e293b/ffffff?text=Profil+Pondok+Pesantren+Dayama" alt="Profil Pesantren" class="w-full h-full object-cover opacity-70 group-hover:opacity-50 transition-opacity duration-500">
            
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent flex flex-col justify-end p-6 md:p-10">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 md:w-20 md:h-20 bg-primary/90 rounded-full flex items-center justify-center text-white shadow-lg group-hover:scale-110 group-hover:bg-primary transition-all duration-300">
                        <svg class="w-8 h-8 md:w-10 md:h-10 ml-2" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                    <div>
                        <span class="px-3 py-1 text-xs font-bold bg-red-600 text-white rounded-full uppercase tracking-wider mb-2 inline-block">Terbaru</span>
                        <h2 class="text-2xl md:text-4xl font-bold text-white leading-tight">Video Profil Pesantren Darul Yatama Wal-Masakin 2026</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kategori & List --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 border-b border-border-subtle pb-4">
        <h3 class="text-2xl font-bold text-foreground">Video Pilihan</h3>
        
        <div class="hidden md:flex gap-2">
            <button class="px-4 py-1.5 rounded-full bg-primary text-primary-foreground text-sm font-bold shadow-sm">Semua</button>
            <button class="px-4 py-1.5 rounded-full bg-surface text-foreground hover:bg-surface-muted border border-border-subtle text-sm font-bold shadow-sm transition-colors">Tausiyah</button>
            <button class="px-4 py-1.5 rounded-full bg-surface text-foreground hover:bg-surface-muted border border-border-subtle text-sm font-bold shadow-sm transition-colors">Kegiatan</button>
            <button class="px-4 py-1.5 rounded-full bg-surface text-foreground hover:bg-surface-muted border border-border-subtle text-sm font-bold shadow-sm transition-colors">Podcast</button>
        </div>
        
        {{-- Mobile select --}}
        <select class="md:hidden px-4 py-2 border border-border-subtle rounded-lg bg-surface text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary">
            <option>Semua Video</option>
            <option>Tausiyah</option>
            <option>Kegiatan</option>
            <option>Podcast Santri</option>
        </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        {{-- Video Item 1 --}}
        <div class="group cursor-pointer flex flex-col h-full" @click="activeVideoUrl = 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1'; videoModalOpen = true;">
            <div class="relative aspect-video rounded-xl overflow-hidden mb-4 border border-border-subtle bg-muted">
                <img src="https://placehold.co/800x450/334155/ffffff?text=Tausiyah+TGH+Mutawalli" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-colors"></div>
                <div class="absolute bottom-2 right-2 bg-black/80 text-white text-xs font-bold px-2 py-1 rounded">
                    45:20
                </div>
                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity transform scale-50 group-hover:scale-100 duration-300">
                    <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center text-white shadow-lg">
                        <svg class="w-6 h-6 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                </div>
            </div>
            <div>
                <h4 class="text-lg font-bold text-foreground mb-1 group-hover:text-primary transition-colors line-clamp-2 leading-tight">Pengajian Rutin Mingguan: Kitab Al-Hikam - TGH. Mutawalli</h4>
                <p class="text-sm text-muted-foreground flex items-center gap-2">
                    <span>12.5rb x ditonton</span>
                    <span class="w-1 h-1 rounded-full bg-muted-foreground"></span>
                    <span>2 hari yang lalu</span>
                </p>
            </div>
        </div>

        {{-- Video Item 2 --}}
        <div class="group cursor-pointer flex flex-col h-full" @click="activeVideoUrl = 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1'; videoModalOpen = true;">
            <div class="relative aspect-video rounded-xl overflow-hidden mb-4 border border-border-subtle bg-muted">
                <img src="https://placehold.co/800x450/475569/ffffff?text=Haflah+Akhirussanah" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-colors"></div>
                <div class="absolute bottom-2 right-2 bg-black/80 text-white text-xs font-bold px-2 py-1 rounded">
                    1:20:05
                </div>
                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity transform scale-50 group-hover:scale-100 duration-300">
                    <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center text-white shadow-lg">
                        <svg class="w-6 h-6 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                </div>
            </div>
            <div>
                <h4 class="text-lg font-bold text-foreground mb-1 group-hover:text-primary transition-colors line-clamp-2 leading-tight">Momen Haru Haflah Akhirussanah & Wisuda Santri Angkatan ke-25</h4>
                <p class="text-sm text-muted-foreground flex items-center gap-2">
                    <span>8.2rb x ditonton</span>
                    <span class="w-1 h-1 rounded-full bg-muted-foreground"></span>
                    <span>1 bulan yang lalu</span>
                </p>
            </div>
        </div>

        {{-- Video Item 3 --}}
        <div class="group cursor-pointer flex flex-col h-full" @click="activeVideoUrl = 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1'; videoModalOpen = true;">
            <div class="relative aspect-video rounded-xl overflow-hidden mb-4 border border-border-subtle bg-muted">
                <img src="https://placehold.co/800x450/64748b/ffffff?text=Podcast+Santri" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-colors"></div>
                <div class="absolute bottom-2 right-2 bg-black/80 text-white text-xs font-bold px-2 py-1 rounded">
                    25:14
                </div>
                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity transform scale-50 group-hover:scale-100 duration-300">
                    <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center text-white shadow-lg">
                        <svg class="w-6 h-6 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                </div>
            </div>
            <div>
                <h4 class="text-lg font-bold text-foreground mb-1 group-hover:text-primary transition-colors line-clamp-2 leading-tight">Obrolan Santri: Suka Duka Tinggal di Asrama Dayama #PodSantri</h4>
                <p class="text-sm text-muted-foreground flex items-center gap-2">
                    <span>4.5rb x ditonton</span>
                    <span class="w-1 h-1 rounded-full bg-muted-foreground"></span>
                    <span>2 bulan yang lalu</span>
                </p>
            </div>
        </div>
        
        {{-- Video Item 4 --}}
        <div class="group cursor-pointer flex flex-col h-full" @click="activeVideoUrl = 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1'; videoModalOpen = true;">
            <div class="relative aspect-video rounded-xl overflow-hidden mb-4 border border-border-subtle bg-muted">
                <img src="https://placehold.co/800x450/1e293b/ffffff?text=Liga+Santri" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-colors"></div>
                <div class="absolute bottom-2 right-2 bg-black/80 text-white text-xs font-bold px-2 py-1 rounded">
                    12:45
                </div>
                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity transform scale-50 group-hover:scale-100 duration-300">
                    <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center text-white shadow-lg">
                        <svg class="w-6 h-6 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                </div>
            </div>
            <div>
                <h4 class="text-lg font-bold text-foreground mb-1 group-hover:text-primary transition-colors line-clamp-2 leading-tight">Highlights Final Liga Santri Nusantara Region NTB - Dayama FC</h4>
                <p class="text-sm text-muted-foreground flex items-center gap-2">
                    <span>15rb x ditonton</span>
                    <span class="w-1 h-1 rounded-full bg-muted-foreground"></span>
                    <span>3 bulan yang lalu</span>
                </p>
            </div>
        </div>
    </div>
    
    {{-- Load More --}}
    <div class="mt-12 text-center">
        <button class="btn btn-outline border-border-subtle shadow-sm bg-surface">Muat Lebih Banyak Video</button>
    </div>

    {{-- YouTube Channel CTA --}}
    <div class="mt-20 bg-red-600 rounded-3xl p-8 lg:p-12 text-white flex flex-col md:flex-row items-center justify-between gap-8 relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('/img/pattern/islamic-pattern.svg')] opacity-10 mix-blend-overlay"></div>
        
        <div class="relative z-10 md:w-2/3">
            <h2 class="text-3xl font-bold mb-4">Subscribe YouTube Dayama Official</h2>
            <p class="text-red-100 mb-0">Jangan lewatkan update video terbaru seputar kajian, kegiatan santri, dan live streaming acara pondok pesantren. Nyalakan lonceng notifikasinya!</p>
        </div>
        
        <div class="relative z-10 shrink-0">
            <a href="https://youtube.com" target="_blank" class="btn bg-white text-red-600 hover:bg-red-50 border-transparent shadow-xl px-8 py-3 text-lg font-bold flex items-center gap-3 rounded-full">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                Subscribe Sekarang
            </a>
        </div>
    </div>

    {{-- Video Modal --}}
    <div 
        x-show="videoModalOpen" 
        x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/95 backdrop-blur-sm p-4"
        @keydown.escape.window="videoModalOpen = false; activeVideoUrl = ''"
    >
        <div class="relative w-full max-w-5xl aspect-video rounded-xl overflow-hidden shadow-2xl" @click.away="videoModalOpen = false; activeVideoUrl = ''">
            <button @click="videoModalOpen = false; activeVideoUrl = ''" class="absolute top-4 right-4 z-10 w-10 h-10 bg-black/50 hover:bg-red-600 text-white rounded-full flex items-center justify-center transition-colors">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <iframe 
                x-show="videoModalOpen"
                :src="activeVideoUrl" 
                class="w-full h-full" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen>
            </iframe>
        </div>
    </div>
</div>
@endsection
