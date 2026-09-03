@php 
    $siteName = \App\Services\SettingService::get('general.site_name', config('app.name'), $context);
    $tagline = \App\Services\SettingService::get('general.tagline', 'Lembaga Pendidikan & Dakwah Islamiyah', $context);
    $logoUrl = \App\Services\SettingService::get('general.logo_url', null, $context);
    
    // Get landing domain URL for absolute links
    $landingDomain = config('platform.sites.landing.domain', env('APP_ROOT_DOMAIN', 'dayama.test'));
    $landingUrl = 'http://' . $landingDomain;
    
    // Get blog domain URL
    $blogDomain = config('platform.sites.blog.domain', 'blog.' . $landingDomain);
    $blogUrl = 'http://' . $blogDomain;
@endphp
<!-- Header 1 -->
<div class="h-16 bg-background border-b border-border-subtle relative z-20 flex items-center transition-colors">
    <div class="container-page flex items-center justify-between w-full h-full">
        {{-- Logo --}}
        <a href="{{ $landingUrl }}" class="flex items-center gap-3 text-foreground tracking-tight hover:opacity-80 transition-opacity">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-8 w-auto">
            @else
                <span class="w-8 h-8 rounded bg-primary flex items-center justify-center text-primary-foreground text-sm font-bold shadow-sm">{{ substr($siteName, 0, 1) }}</span>
            @endif
            <div class="flex flex-col">
                <span class="font-bold leading-none text-lg">{{ $siteName }}</span>
                <span class="text-[10px] text-muted-foreground uppercase tracking-widest mt-0.5">{{ $tagline }}</span>
            </div>
        </a>

        {{-- Nav --}}
        <nav class="hidden lg:flex items-center gap-1 relative" role="navigation" aria-label="{{ __('Main navigation') }}"
            x-data="{
                left: 0, width: 0, opacity: 0, activeEl: null,
                init() {
                    this.$nextTick(() => {
                        this.activeEl = this.$el.querySelector('.active-nav-item');
                        this.resetUnderline();
                    });
                },
                setUnderline(el) {
                    this.left = el.offsetLeft;
                    this.width = el.offsetWidth;
                    this.opacity = 1;
                },
                resetUnderline() {
                    if (this.activeEl) {
                        this.left = this.activeEl.offsetLeft;
                        this.width = this.activeEl.offsetWidth;
                        this.opacity = 1;
                    } else {
                        this.opacity = 0;
                    }
                }
            }"
            @mouseleave="resetUnderline()"
        >
            {{-- Indicator Line --}}
            <div class="absolute -bottom-1 h-[3px] bg-primary transition-all duration-300 ease-out rounded-full pointer-events-none z-10"
                 :style="`left: ${left}px; width: ${width}px; opacity: ${opacity};`"
                 x-cloak></div>

            <a href="{{ $landingUrl }}" 
               @mouseenter="setUnderline($event.currentTarget)"
               class="btn btn-ghost text-sm hover:bg-transparent hover:text-primary {{ request()->is('/') ? 'active-nav-item text-primary font-medium' : '' }}">Beranda</a>
            
            {{-- Profil --}}
            <div class="relative group {{ request()->is('profil*') ? 'active-nav-item' : '' }}" x-data="{ open: false }" @mouseenter="open = true; setUnderline($event.currentTarget)" @mouseleave="open = false">
                <button type="button" class="btn btn-ghost text-sm gap-1 flex items-center hover:bg-transparent hover:text-primary {{ request()->is('profil*') ? 'text-primary font-medium' : '' }}">
                    Profil <svg class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-cloak x-transition.opacity class="absolute top-full left-1/2 -translate-x-1/2 mt-1 w-[48rem] bg-background border border-border-subtle shadow-xl py-6 rounded-xl z-50 overflow-hidden flex">
                    {{-- Kolom Kiri: Yayasan --}}
                    <div class="w-1/2 border-r border-border-subtle px-8">
                        <h4 class="text-sm font-bold text-primary mb-3 px-2 flex items-center gap-2 whitespace-nowrap">
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            Yayasan & Sejarah
                        </h4>
                        <div class="flex flex-col gap-1">
                            <a href="{{ $landingUrl }}/profil/tentang-yayasan" class="block px-3 py-2 text-sm hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('profil/tentang-yayasan') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Tentang Yayasan</a>
                            <a href="{{ $landingUrl }}/profil/sejarah" class="block px-3 py-2 text-sm hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('profil/sejarah') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Sejarah</a>
                            <a href="{{ $landingUrl }}/profil/visi-misi" class="block px-3 py-2 text-sm hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('profil/visi-misi') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Visi & Misi</a>
                            <a href="{{ $landingUrl }}/profil/sambutan-pimpinan" class="block px-3 py-2 text-sm hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('profil/sambutan-pimpinan') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Sambutan Pimpinan</a>
                        </div>
                    </div>
                    
                    {{-- Kolom Kanan: Organisasi --}}
                    <div class="w-1/2 px-8">
                        <h4 class="text-sm font-bold text-primary mb-3 px-2 flex items-center gap-2 whitespace-nowrap">
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Organisasi & Jaringan
                        </h4>
                        <div class="flex flex-col gap-1">
                            <a href="{{ $landingUrl }}/profil/struktur-organisasi" class="block px-3 py-2 text-sm hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('profil/struktur-organisasi') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Struktur Organisasi</a>
                            <a href="{{ $landingUrl }}/profil/legalitas-akreditasi" class="block px-3 py-2 text-sm hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('profil/legalitas-akreditasi') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Legalitas & Akreditasi</a>
                            <a href="{{ $landingUrl }}/profil/mitra-kerjasama" class="block px-3 py-2 text-sm hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('profil/mitra-kerjasama') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Mitra Kerja Sama</a>
                            <a href="{{ $landingUrl }}/profil/fasilitas" class="block px-3 py-2 text-sm hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('profil/fasilitas') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Fasilitas</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pendidikan --}}
            <div class="relative group {{ request()->is('pendidikan*') ? 'active-nav-item' : '' }}" x-data="{ open: false }" @mouseenter="open = true; setUnderline($event.currentTarget)" @mouseleave="open = false">
                <button type="button" class="btn btn-ghost text-sm gap-1 flex items-center hover:bg-transparent hover:text-primary {{ request()->is('pendidikan*') ? 'text-primary font-medium' : '' }}">
                    Pendidikan <svg class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-cloak x-transition.opacity class="absolute top-full left-1/2 -translate-x-1/2 mt-1 w-[48rem] bg-background border border-border-subtle shadow-xl py-6 rounded-xl z-50 overflow-hidden flex">
                    {{-- Kolom Kiri: Lembaga Pendidikan --}}
                    <div class="w-1/2 border-r border-border-subtle px-8">
                        <h4 class="text-sm font-bold text-primary mb-3 px-2 flex items-center gap-2 whitespace-nowrap">
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" /></svg>
                            Lembaga Pendidikan
                        </h4>
                        <div class="flex flex-col gap-1">
                            @if(isset($menuInstitutions) && $menuInstitutions->count() > 0)
                                @foreach($menuInstitutions as $inst)
                                    <a href="{{ $landingUrl }}/pendidikan/lembaga/{{ $inst->slug }}" class="block px-3 py-2 text-sm hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('pendidikan/lembaga/'.$inst->slug) ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">{{ $inst->short_name ?? $inst->name }}</a>
                                @endforeach
                            @else
                                <span class="block px-3 py-2 text-sm text-muted-foreground italic">Belum ada data lembaga</span>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Kolom Kanan: Program & Info --}}
                    <div class="w-1/2 px-8">
                        <h4 class="text-sm font-bold text-primary mb-3 px-2 flex items-center gap-2 whitespace-nowrap">
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            Informasi Lainnya
                        </h4>
                        <div class="flex flex-col gap-1">
                            <a href="{{ $landingUrl }}/pendidikan/program-pendidikan" class="block px-3 py-2 text-sm hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('pendidikan/program-pendidikan') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Program Pendidikan</a>
                            <a href="{{ $landingUrl }}/pendidikan/staf" class="block px-3 py-2 text-sm hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('pendidikan/staf') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Guru & Tenaga Kependidikan</a>
                            <a href="{{ $landingUrl }}/pendidikan/prestasi" class="block px-3 py-2 text-sm hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('pendidikan/prestasi') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Prestasi</a>
                            <a href="{{ $landingUrl }}/pendidikan/alumni" class="block px-3 py-2 text-sm hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('pendidikan/alumni') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Alumni</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Layanan --}}
            <div class="relative group {{ request()->is('layanan*') ? 'active-nav-item' : '' }}" x-data="{ open: false }" @mouseenter="open = true; setUnderline($event.currentTarget)" @mouseleave="open = false">
                <button type="button" class="btn btn-ghost text-sm gap-1 flex items-center hover:bg-transparent hover:text-primary {{ request()->is('layanan*') ? 'text-primary font-medium' : '' }}">
                    Layanan <svg class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-cloak x-transition.opacity class="absolute top-full left-1/2 -translate-x-1/2 mt-1 w-[48rem] bg-background border border-border-subtle shadow-xl py-6 rounded-xl z-50 overflow-hidden flex">
                    {{-- Kolom Kiri --}}
                    <div class="w-1/2 border-r border-border-subtle px-8">
                        <h4 class="text-sm font-bold text-primary mb-3 px-2 flex items-center gap-2 whitespace-nowrap">
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            Layanan Pesantren
                        </h4>
                        <div class="flex flex-col gap-1">
                            <a href="https://psb.dayama.web.id" target="_blank" rel="noopener noreferrer" class="block px-3 py-2 text-sm font-medium text-primary hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 flex items-center justify-between group-link">
                                PSB (Santri Baru)
                                <svg class="w-3 h-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                            <a href="https://docs.dayama.web.id" target="_blank" rel="noopener noreferrer" class="block px-3 py-2 text-sm text-foreground hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 flex items-center justify-between group-link">
                                Pustaka Dayama
                                <svg class="w-3 h-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                            <a href="{{ $landingUrl }}/layanan/unit-bisnis" class="block px-3 py-2 text-sm hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('layanan/unit-bisnis') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Unit Bisnis</a>
                        </div>
                    </div>
                    
                    {{-- Kolom Kanan --}}
                    <div class="w-1/2 px-8">
                        <h4 class="text-sm font-bold text-primary mb-3 px-2 flex items-center gap-2 whitespace-nowrap">
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            Interaksi & Bantuan
                        </h4>
                        <div class="flex flex-col gap-1">
                            <a href="{{ $landingUrl }}/layanan/donasi" class="block px-3 py-2 text-sm hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('layanan/donasi') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Donasi</a>
                            <a href="{{ $landingUrl }}/layanan/kontak" class="block px-3 py-2 text-sm hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('layanan/kontak') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Kontak</a>
                            <a href="{{ $landingUrl }}/layanan/faq" class="block px-3 py-2 text-sm hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('layanan/faq') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">FAQ</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Media --}}
            <div class="relative group {{ request()->is('media*') ? 'active-nav-item' : '' }}" x-data="{ open: false }" @mouseenter="open = true; setUnderline($event.currentTarget)" @mouseleave="open = false">
                <button type="button" class="btn btn-ghost text-sm gap-1 flex items-center hover:bg-transparent hover:text-primary {{ request()->is('media*') ? 'text-primary font-medium' : '' }}">
                    Media <svg class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-cloak x-transition.opacity class="absolute top-full left-1/2 -translate-x-1/2 mt-1 w-[48rem] bg-background border border-border-subtle shadow-xl py-6 rounded-xl z-50 overflow-hidden flex">
                    {{-- Kolom Kiri --}}
                    <div class="w-1/2 border-r border-border-subtle px-8">
                        <h4 class="text-sm font-bold text-primary mb-3 px-2 flex items-center gap-2 whitespace-nowrap">
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15"/></svg>
                            Informasi & Berita
                        </h4>
                        <div class="flex flex-col gap-1">
                            <a href="{{ $blogUrl }}" class="block px-3 py-2 text-sm font-medium text-primary hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300">Berita (Blog)</a>
                            <a href="{{ $landingUrl }}/media/pengumuman" class="block px-3 py-2 text-sm hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('media/pengumuman') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Pengumuman</a>
                            <a href="{{ $landingUrl }}/media/agenda" class="block px-3 py-2 text-sm hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('media/agenda') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Kalender Kegiatan</a>
                        </div>
                    </div>
                    
                    {{-- Kolom Kanan --}}
                    <div class="w-1/2 px-8">
                        <h4 class="text-sm font-bold text-primary mb-3 px-2 flex items-center gap-2 whitespace-nowrap">
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Dokumentasi
                        </h4>
                        <div class="flex flex-col gap-1">
                            <a href="{{ $landingUrl }}/media/galeri-foto" class="block px-3 py-2 text-sm hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('media/galeri-foto') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Galeri Foto</a>
                            <a href="{{ $landingUrl }}/media/galeri-video" class="block px-3 py-2 text-sm hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('media/galeri-video') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Galeri Video</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        {{-- Actions --}}
        <div class="flex items-center gap-1.5 sm:gap-3">
            <button type="button" class="btn btn-ghost p-2 rounded-full" aria-label="{{ __('Search') }}" onclick="document.getElementById('search-modal').showModal()">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </button>
            <button type="button" class="btn btn-ghost p-2 rounded-full" id="theme-toggle" aria-label="{{ __('Toggle dark mode') }}" x-data @click="$store.theme.toggle()">
                <svg class="w-4 h-4 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                <svg class="w-4 h-4 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </button>

            @auth
                <!-- Notifications Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open" @click.away="open = false" class="btn btn-ghost p-2 rounded-full relative" aria-label="{{ __('Notifications') }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="absolute top-1 right-1.5 w-2 h-2 bg-primary rounded-full ring-2 ring-background"></span>
                        @endif
                    </button>

                    <div x-show="open" x-cloak x-transition.opacity class="absolute right-0 mt-2 w-80 bg-surface border border-border-subtle rounded-xl shadow-lg z-50 overflow-hidden text-left">
                        <div class="px-4 py-3 border-b border-border-subtle flex justify-between items-center bg-surface-muted/30">
                            <h3 class="text-sm font-semibold">{{ __('Notifications') }}</h3>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                            <form action="{{ route('notifications.read.all') }}" method="POST">
                                @csrf
                                <button type="submit" class="text-[10px] text-primary hover:underline font-medium">{{ __('Mark all as read') }}</button>
                            </form>
                            @endif
                        </div>
                        <div class="max-h-80 overflow-y-auto">
                            @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
                            <a href="{{ $notification->data['url'] ?? '#' }}" class="block px-4 py-3 hover:bg-surface-muted transition-colors border-b border-border-subtle last:border-0">
                                <p class="text-xs text-foreground">{{ $notification->data['message'] ?? 'New notification' }}</p>
                                <span class="text-[10px] text-muted-foreground mt-1 block">{{ $notification->created_at->diffForHumans() }}</span>
                            </a>
                            @empty
                            <div class="px-4 py-6 text-center text-muted-foreground text-sm">
                                {{ __('No new notifications') }}
                            </div>
                            @endforelse
                        </div>
                        <div class="p-2 border-t border-border-subtle text-center bg-surface-muted/30">
                            <a href="{{ route('dashboard.index') }}" class="text-[11px] text-foreground font-medium hover:text-primary transition-colors">{{ __('View all in Dashboard') }}</a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('dashboard.index') }}" class="hidden lg:inline-flex btn btn-primary text-sm h-9 px-4 rounded-full shadow-sm">{{ __('Dashboard') }}</a>
            @else
                <div class="hidden lg:flex items-center gap-2 border-l border-border-subtle pl-4 ml-1">
                    <a href="{{ route('login') }}" class="btn btn-ghost text-sm h-9 px-4 rounded-full">{{ __('Sign In') }}</a>
                    <a href="{{ url('/register') }}" class="btn btn-primary text-sm h-9 px-4 rounded-full shadow-sm">{{ __('Sign Up') }}</a>
                </div>
            @endauth

            {{-- Mobile menu toggle --}}
            <button 
                id="mobile-menu-toggle-btn"
                type="button" 
                class="lg:hidden btn btn-ghost p-2 rounded-full relative w-10 h-10 flex items-center justify-center" 
                aria-label="{{ __('Toggle menu') }}" 
                x-data="{ open: false }" 
                @click="window.toggleMobileMenu()"
                @mobile-menu-changed.window="open = $event.detail"
            >
                <div class="relative w-5 h-4">
                    <span class="absolute left-0 w-full h-[2px] bg-current transform transition-all duration-300 ease-in-out" :class="open ? 'top-1/2 -translate-y-1/2 rotate-45' : 'top-0'"></span>
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-[2px] bg-current transform transition-all duration-300 ease-in-out" :class="open ? 'opacity-0 translate-x-3' : 'opacity-100'"></span>
                    <span class="absolute left-0 w-full h-[2px] bg-current transform transition-all duration-300 ease-in-out" :class="open ? 'top-1/2 -translate-y-1/2 -rotate-45' : 'bottom-0'"></span>
                </div>
            </button>
        </div>
    </div>
</div>
