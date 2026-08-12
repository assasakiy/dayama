@extends('web.layouts.app')

@section('content')
<section class="hero-subpage py-16 md:py-20 pb-24 md:pb-32 relative z-0" x-data="gallery()">
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
                        <span class="ml-1 md:ml-2 font-medium text-white">Media</span>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-white/60" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 md:ml-2 font-medium text-white">{{ $title ?? 'Galeri Foto' }}</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="max-w-3xl">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black tracking-tight text-white mb-6 leading-tight">
                {!! $pageModel?->sections['hero']['title'] ?? $title ?? '' !!}
                @if(!empty($pageModel?->sections['hero']['highlight']))
                <span class="block text-primary">{!! $pageModel->sections['hero']['highlight'] !!}</span>
                @endif
            </h1>
            <p class="text-lg md:text-xl text-white/90 leading-relaxed max-w-3xl">
                {{ $pageModel?->sections['hero']['subtitle'] ?? 'Rekam jejak visual berbagai kegiatan, fasilitas, dan momen-momen berharga.' }}
            </p>
            </div>
            
            <div class="w-full md:w-80 relative shrink-0">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" class="w-full pl-11 pr-4 py-3 border-0 rounded-xl bg-white text-white shadow-lg focus:outline-none focus:ring-4 focus:ring-primary/30 transition-all text-base" placeholder="Cari album...">
            </div>
        </div>
    </div>
    
    <div class="absolute bottom-0 inset-x-0 pointer-events-none z-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto text-background">
            <path d="M0 80V40C240 0 480 0 720 40C960 80 1200 80 1440 40V80H0Z" fill="currentColor"/>
        </svg>
    </div>
</section>

<div class="container-page py-12 md:py-16" x-data="gallery()">
    {{-- Filter & Search --}}
    <div class="flex flex-col md:flex-row justify-between items-center gap-6 mb-10">
        <div class="flex flex-wrap justify-center md:justify-start gap-2 w-full md:w-auto">
            <button @click="setCategory('Semua')" :class="activeCategory === 'Semua' ? 'bg-primary text-primary-foreground' : 'bg-surface text-foreground hover:bg-surface-muted border-border-subtle'" class="px-5 py-2 rounded-full text-sm font-bold transition-colors border shadow-sm">
                Semua Foto
            </button>
            <button @click="setCategory('Fasilitas')" :class="activeCategory === 'Fasilitas' ? 'bg-primary text-primary-foreground' : 'bg-surface text-foreground hover:bg-surface-muted border-border-subtle'" class="px-5 py-2 rounded-full text-sm font-bold transition-colors border shadow-sm">
                Fasilitas
            </button>
            <button @click="setCategory('Kegiatan')" :class="activeCategory === 'Kegiatan' ? 'bg-primary text-primary-foreground' : 'bg-surface text-foreground hover:bg-surface-muted border-border-subtle'" class="px-5 py-2 rounded-full text-sm font-bold transition-colors border shadow-sm">
                Kegiatan Santri
            </button>
            <button @click="setCategory('Ekstrakurikuler')" :class="activeCategory === 'Ekstrakurikuler' ? 'bg-primary text-primary-foreground' : 'bg-surface text-foreground hover:bg-surface-muted border-border-subtle'" class="px-5 py-2 rounded-full text-sm font-bold transition-colors border shadow-sm">
                Ekstrakurikuler
            </button>
        </div>
    </div>

    {{-- Masonry Grid --}}
    <div class="columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-4 space-y-4">
        {{-- Loop Images (Simulated) --}}
        <template x-for="img in filteredImages" :key="img.id">
            <div 
                class="break-inside-avoid rounded-xl overflow-hidden relative group cursor-pointer border border-border-subtle bg-surface shadow-sm"
                @click="openLightbox(img)"
            >
                <img :src="img.src" :alt="img.title" class="w-full h-auto object-cover transform transition-transform duration-700 group-hover:scale-105" loading="lazy">
                
                {{-- Overlay (Hover Effect) --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-5">
                    <span class="text-[10px] font-bold bg-primary text-primary-foreground px-2 py-0.5 rounded uppercase tracking-wider w-max mb-2" x-text="img.category"></span>
                    <h3 class="text-white font-bold text-lg leading-tight mb-1 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300" x-text="img.title"></h3>
                    <p class="text-white/80 text-xs transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 delay-75" x-text="img.date"></p>
                </div>
                
                {{-- Expand Icon --}}
                <div class="absolute top-4 right-4 w-8 h-8 rounded-full bg-black/50 text-white backdrop-blur-sm flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 transform scale-50 group-hover:scale-100">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                </div>
            </div>
        </template>
    </div>
    
    {{-- Load More --}}
    <div class="mt-12 text-center" x-show="filteredImages.length > 0">
        <button class="btn btn-outline border-border-subtle shadow-sm bg-surface">Muat Lebih Banyak Foto</button>
    </div>

    {{-- Empty State --}}
    <div x-show="filteredImages.length === 0" x-cloak class="text-center py-20 bg-surface rounded-2xl border border-border-subtle">
        <svg class="w-16 h-16 text-muted-foreground/30 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        <h3 class="text-xl font-bold text-foreground mb-2">Foto Tidak Ditemukan</h3>
        <p class="text-muted-foreground">Tidak ada foto dalam kategori ini.</p>
    </div>

    {{-- Lightbox Modal --}}
    <div 
        x-show="lightboxOpen" 
        x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/95 backdrop-blur-sm p-4 md:p-10"
        @keydown.escape.window="closeLightbox()"
        @keydown.arrow-left.window="prevImage()"
        @keydown.arrow-right.window="nextImage()"
    >
        <div 
            x-show="lightboxOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full h-full flex flex-col"
            @click.away="closeLightbox()"
        >
            {{-- Toolbar --}}
            <div class="flex justify-between items-center mb-4 shrink-0">
                <div class="text-white">
                    <h4 class="font-bold text-lg" x-text="activeImage?.title"></h4>
                    <p class="text-white/70 text-sm" x-text="activeImage?.date"></p>
                </div>
                <div class="flex gap-4">
                    <a :href="activeImage?.src" download class="btn btn-ghost btn-icon text-white hover:bg-white/20">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </a>
                    <button @click="closeLightbox()" class="btn btn-ghost btn-icon text-white hover:bg-white/20 hover:text-red-400">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            
            {{-- Image Container --}}
            <div class="flex-grow relative flex items-center justify-center overflow-hidden">
                {{-- Nav Prev --}}
                <button @click.stop="prevImage()" class="absolute left-0 md:left-4 z-10 p-2 md:p-4 rounded-full bg-black/50 text-white hover:bg-black hover:scale-110 transition-all backdrop-blur-md">
                    <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                
                <img :src="activeImage?.src" class="max-w-full max-h-full object-contain rounded-lg shadow-2xl" alt="">
                
                {{-- Nav Next --}}
                <button @click.stop="nextImage()" class="absolute right-0 md:right-4 z-10 p-2 md:p-4 rounded-full bg-black/50 text-white hover:bg-black hover:scale-110 transition-all backdrop-blur-md">
                    <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('gallery', () => ({
            activeCategory: 'Semua',
            lightboxOpen: false,
            activeImage: null,
            images: [
                { id: 1, category: 'Fasilitas', title: 'Masjid Jami\' Dayama', date: 'Maret 2026', src: 'https://placehold.co/800x1200/10b981/ffffff?text=Masjid+Besar' },
                { id: 2, category: 'Kegiatan', title: 'Kajian Kitab Kuning Rutin', date: 'Februari 2026', src: 'https://placehold.co/1200x800/f59e0b/ffffff?text=Kajian+Kitab' },
                { id: 3, category: 'Ekstrakurikuler', title: 'Latihan Memanah', date: 'Januari 2026', src: 'https://placehold.co/800x800/3b82f6/ffffff?text=Memanah' },
                { id: 4, category: 'Fasilitas', title: 'Gedung Asrama Putra Baru', date: 'Desember 2025', src: 'https://placehold.co/1200x600/64748b/ffffff?text=Asrama+Putra' },
                { id: 5, category: 'Kegiatan', title: 'Wisuda Tahfidz 30 Juz', date: 'November 2025', src: 'https://placehold.co/800x1000/14b8a6/ffffff?text=Wisuda+Tahfidz' },
                { id: 6, category: 'Ekstrakurikuler', title: 'Tim Hadroh El-Dayama', date: 'Oktober 2025', src: 'https://placehold.co/1000x800/8b5cf6/ffffff?text=Hadroh' },
                { id: 7, category: 'Fasilitas', title: 'Laboratorium Komputer', date: 'September 2025', src: 'https://placehold.co/800x1200/ec4899/ffffff?text=Lab+Komputer' },
                { id: 8, category: 'Kegiatan', title: 'Masa Ta\'aruf Santri Baru', date: 'Juli 2025', src: 'https://placehold.co/1200x800/f43f5e/ffffff?text=Masa+Taaruf' },
            ],
            
            get filteredImages() {
                if (this.activeCategory === 'Semua') {
                    return this.images;
                }
                return this.images.filter(img => img.category === this.activeCategory);
            },
            
            setCategory(category) {
                this.activeCategory = category;
            },
            
            openLightbox(img) {
                this.activeImage = img;
                this.lightboxOpen = true;
                document.body.style.overflow = 'hidden';
            },
            
            closeLightbox() {
                this.lightboxOpen = false;
                setTimeout(() => { this.activeImage = null; }, 300);
                document.body.style.overflow = '';
            },
            
            prevImage() {
                if (!this.activeImage) return;
                const index = this.filteredImages.findIndex(i => i.id === this.activeImage.id);
                if (index > 0) {
                    this.activeImage = this.filteredImages[index - 1];
                } else {
                    this.activeImage = this.filteredImages[this.filteredImages.length - 1];
                }
            },
            
            nextImage() {
                if (!this.activeImage) return;
                const index = this.filteredImages.findIndex(i => i.id === this.activeImage.id);
                if (index < this.filteredImages.length - 1) {
                    this.activeImage = this.filteredImages[index + 1];
                } else {
                    this.activeImage = this.filteredImages[0];
                }
            }
        }))
    })
</script>
@endsection
