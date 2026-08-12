{{-- Mobile menu (Moved outside <header> to avoid transform containing block issues) --}}
<div id="mobile-menu" class="hidden lg:hidden fixed top-16 left-0 w-full max-h-[calc(100vh-4rem)] bg-background shadow-2xl z-50 flex flex-col border-t border-border-subtle overflow-hidden">
    {{-- Mobile Menu Body (Scrollable) --}}
    <div class="flex-1 overflow-y-auto overscroll-contain px-4 py-3 min-h-0">
        <nav class="flex flex-col gap-1">
        <a href="{{ $landingUrl ?? 'http://test-blog.test' }}" class="btn btn-ghost justify-start">Beranda</a>
        
        {{-- Mobile Accordions --}}
        <div x-data="{ openProfil: false }" class="flex flex-col">
            <button type="button" @click="openProfil = !openProfil" class="btn btn-ghost justify-between w-full text-left {{ request()->is('profil*') ? 'underline underline-offset-8 decoration-2 decoration-primary text-primary font-medium' : 'btn btn-ghost justify-between w-full text-left' }}">
                Profil <svg class="w-4 h-4 transition-transform duration-200" :class="openProfil ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="openProfil" x-collapse class="pl-4 pb-2">
                {{-- Group 1 --}}
                <div class="mt-2 mb-3">
                    <div class="px-3 py-1 text-[13px] font-bold text-primary flex items-center gap-1.5">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Yayasan & Sejarah
                    </div>
                    <div class="pl-4 ml-4 flex flex-col border-l-2 border-border-subtle/50 mt-1">
                        <a href="{{ $landingUrl ?? 'http://test-blog.test' }}/profil/tentang-yayasan" class="block px-3 py-1.5 text-sm  hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('profil/tentang-yayasan') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Tentang Yayasan</a>
                        <a href="{{ $landingUrl ?? 'http://test-blog.test' }}/profil/sejarah" class="block px-3 py-1.5 text-sm  hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('profil/sejarah') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Sejarah</a>
                        <a href="{{ $landingUrl ?? 'http://test-blog.test' }}/profil/visi-misi" class="block px-3 py-1.5 text-sm  hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('profil/visi-misi') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Visi & Misi</a>
                        <a href="{{ $landingUrl ?? 'http://test-blog.test' }}/profil/sambutan-pimpinan" class="block px-3 py-1.5 text-sm  hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('profil/sambutan-pimpinan') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Sambutan Pimpinan</a>
                    </div>
                </div>
                
                {{-- Group 2 --}}
                <div class="mb-2">
                    <div class="px-3 py-1 text-[13px] font-bold text-primary flex items-center gap-1.5">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Organisasi & Jaringan
                    </div>
                    <div class="pl-4 ml-4 flex flex-col border-l-2 border-border-subtle/50 mt-1">
                        <a href="{{ $landingUrl ?? 'http://test-blog.test' }}/profil/struktur-organisasi" class="block px-3 py-1.5 text-sm  hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('profil/struktur-organisasi') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Struktur Organisasi</a>
                        <a href="{{ $landingUrl ?? 'http://test-blog.test' }}/profil/legalitas-akreditasi" class="block px-3 py-1.5 text-sm  hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('profil/legalitas-akreditasi') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Legalitas & Akreditasi</a>
                        <a href="{{ $landingUrl ?? 'http://test-blog.test' }}/profil/mitra-kerjasama" class="block px-3 py-1.5 text-sm  hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('profil/mitra-kerjasama') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Mitra Kerja Sama</a>
                        <a href="{{ $landingUrl ?? 'http://test-blog.test' }}/profil/fasilitas" class="block px-3 py-1.5 text-sm  hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('profil/fasilitas') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Fasilitas</a>
                    </div>
                </div>
            </div>
        </div>

        <div x-data="{ openPendidikan: false }" class="flex flex-col">
            <button type="button" @click="openPendidikan = !openPendidikan" class="btn btn-ghost justify-between w-full text-left {{ request()->is('pendidikan*') ? 'underline underline-offset-8 decoration-2 decoration-primary text-primary font-medium' : 'btn btn-ghost justify-between w-full text-left' }}">
                Pendidikan <svg class="w-4 h-4 transition-transform duration-200" :class="openPendidikan ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="openPendidikan" x-collapse class="pl-4 pb-2">
                {{-- Group 1 --}}
                <div class="mt-2 mb-3">
                    <div class="px-3 py-1 text-[13px] font-bold text-primary flex items-center gap-1.5">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" /></svg>
                        Lembaga Pendidikan
                    </div>
                    <div class="pl-4 ml-4 flex flex-col border-l-2 border-border-subtle/50 mt-1">
                        @if(isset($menuInstitutions) && $menuInstitutions->count() > 0)
                            @foreach($menuInstitutions as $inst)
                                <a href="{{ $landingUrl ?? 'http://test-blog.test' }}/pendidikan/lembaga/{{ $inst->slug }}" class="block px-3 py-1.5 text-sm  hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('pendidikan/lembaga/'.$inst->slug) ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">{{ $inst->short_name ?? $inst->name }}</a>
                            @endforeach
                        @else
                            <span class="block px-3 py-1.5 text-sm text-muted-foreground italic">Belum ada data lembaga</span>
                        @endif
                    </div>
                </div>
                
                {{-- Group 2 --}}
                <div class="mb-2">
                    <div class="px-3 py-1 text-[13px] font-bold text-primary flex items-center gap-1.5">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        Informasi Lainnya
                    </div>
                    <div class="pl-4 ml-4 flex flex-col border-l-2 border-border-subtle/50 mt-1">
                        <a href="{{ $landingUrl ?? 'http://test-blog.test' }}/pendidikan/program-pendidikan" class="block px-3 py-1.5 text-sm  hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('pendidikan/program-pendidikan') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Program Pendidikan</a>
                        <a href="{{ $landingUrl ?? 'http://test-blog.test' }}/pendidikan/staf" class="block px-3 py-1.5 text-sm  hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('pendidikan/staf') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Guru & Tenaga Kependidikan</a>
                        <a href="{{ $landingUrl ?? 'http://test-blog.test' }}/pendidikan/prestasi" class="block px-3 py-1.5 text-sm  hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('pendidikan/prestasi') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Prestasi</a>
                        <a href="{{ $landingUrl ?? 'http://test-blog.test' }}/pendidikan/alumni" class="block px-3 py-1.5 text-sm  hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('pendidikan/alumni') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Alumni</a>
                    </div>
                </div>
            </div>
        </div>

        <div x-data="{ openLayanan: false }" class="flex flex-col">
            <button type="button" @click="openLayanan = !openLayanan" class="btn btn-ghost justify-between w-full text-left {{ request()->is('layanan*') ? 'underline underline-offset-8 decoration-2 decoration-primary text-primary font-medium' : 'btn btn-ghost justify-between w-full text-left' }}">
                Layanan <svg class="w-4 h-4 transition-transform duration-200" :class="openLayanan ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="openLayanan" x-collapse class="pl-4 pb-2">
                <div class="mt-2 mb-3">
                    <div class="px-3 py-1 text-[13px] font-bold text-primary flex items-center gap-1.5">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        Layanan Pesantren
                    </div>
                    <div class="pl-4 ml-4 flex flex-col border-l-2 border-border-subtle/50 mt-1">
                        <a href="https://psb.dayama.web.id" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between px-3 py-1.5 text-sm text-primary font-medium hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300">
                            PSB (Santri Baru)
                            <svg class="w-3 h-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                        <a href="https://docs.dayama.web.id" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between px-3 py-1.5 text-sm text-foreground hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300">
                            Pustaka Dayama
                            <svg class="w-3 h-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                        <a href="{{ $landingUrl ?? 'http://test-blog.test' }}/layanan/unit-bisnis" class="block px-3 py-1.5 text-sm  hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('layanan/unit-bisnis') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Unit Bisnis</a>
                    </div>
                </div>
                
                <div class="mb-2">
                    <div class="px-3 py-1 text-[13px] font-bold text-primary flex items-center gap-1.5">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        Interaksi & Bantuan
                    </div>
                    <div class="pl-4 ml-4 flex flex-col border-l-2 border-border-subtle/50 mt-1">
                        <a href="{{ $landingUrl ?? 'http://test-blog.test' }}/layanan/donasi" class="block px-3 py-1.5 text-sm  hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('layanan/donasi') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Donasi</a>
                        <a href="{{ $landingUrl ?? 'http://test-blog.test' }}/layanan/kontak" class="block px-3 py-1.5 text-sm  hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('layanan/kontak') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Kontak</a>
                        <a href="{{ $landingUrl ?? 'http://test-blog.test' }}/layanan/faq" class="block px-3 py-1.5 text-sm  hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('layanan/faq') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">FAQ</a>
                    </div>
                </div>
            </div>
        </div>

        <div x-data="{ openMedia: false }" class="flex flex-col">
            <button type="button" @click="openMedia = !openMedia" class="btn btn-ghost justify-between w-full text-left {{ request()->is('media*') ? 'underline underline-offset-8 decoration-2 decoration-primary text-primary font-medium' : 'btn btn-ghost justify-between w-full text-left' }}">
                Media <svg class="w-4 h-4 transition-transform duration-200" :class="openMedia ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="openMedia" x-collapse class="pl-4 pb-2">
                <div class="mt-2 mb-3">
                    <div class="px-3 py-1 text-[13px] font-bold text-primary flex items-center gap-1.5">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15"/></svg>
                        Informasi & Berita
                    </div>
                    <div class="pl-4 ml-4 flex flex-col border-l-2 border-border-subtle/50 mt-1">
                        <a href="{{ $blogUrl ?? 'http://blog.test-blog.test' }}" class="block px-3 py-1.5 text-sm text-primary font-medium hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300">Berita (Blog)</a>
                        <a href="{{ $landingUrl ?? 'http://test-blog.test' }}/media/pengumuman" class="block px-3 py-1.5 text-sm  hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('media/pengumuman') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Pengumuman</a>
                        <a href="{{ $landingUrl ?? 'http://test-blog.test' }}/media/agenda" class="block px-3 py-1.5 text-sm  hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('media/agenda') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Kalender Kegiatan</a>
                    </div>
                </div>
                
                <div class="mb-2">
                    <div class="px-3 py-1 text-[13px] font-bold text-primary flex items-center gap-1.5">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Dokumentasi
                    </div>
                    <div class="pl-4 ml-4 flex flex-col border-l-2 border-border-subtle/50 mt-1">
                        <a href="{{ $landingUrl ?? 'http://test-blog.test' }}/media/galeri-foto" class="block px-3 py-1.5 text-sm  hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('media/galeri-foto') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Galeri Foto</a>
                        <a href="{{ $landingUrl ?? 'http://test-blog.test' }}/media/galeri-video" class="block px-3 py-1.5 text-sm  hover:bg-primary/5 hover:text-primary hover:translate-x-1 rounded-md transition-all duration-300 {{ request()->is('media/galeri-video') ? 'underline underline-offset-4 decoration-2 decoration-primary text-primary font-medium' : 'text-foreground' }}">Galeri Video</a>
                    </div>
                </div>
            </div>
        </div>
        
        </nav>
    </div>
    
    {{-- Mobile Menu Footer (Fixed at bottom) --}}
    <div class="shrink-0 p-4 border-t border-border-subtle bg-surface shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
        <div class="flex flex-col gap-2">
            @auth
                <a href="{{ route('dashboard.index') }}" class="btn btn-primary w-full justify-center shadow-sm">{{ __('Dashboard') }}</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-ghost w-full justify-center">{{ __('Sign In') }}</a>
                <a href="{{ url('/register') }}" class="btn btn-primary w-full justify-center shadow-sm">{{ __('Sign Up') }}</a>
            @endauth
        </div>
    </div>
</div>

