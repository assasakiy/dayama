<div class="grid grid-cols-1 md:grid-cols-4 gap-8">
    {{-- Brand --}}
    <div class="md:col-span-2">
        <a href="{{ $landingUrl }}" class="flex items-center gap-3 text-foreground tracking-tight hover:opacity-80 transition-opacity mb-4">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-10 w-auto">
            @else
                <span class="w-10 h-10 rounded-md bg-primary flex items-center justify-center text-primary-foreground text-base font-bold shadow-sm">{{ substr($siteName, 0, 1) }}</span>
            @endif
            <div class="flex flex-col">
                <span class="font-bold leading-none text-xl">{{ $siteName }}</span>
                <span class="text-xs text-muted-foreground uppercase tracking-widest mt-1">{{ $tagline }}</span>
            </div>
        </a>
        <p class="text-muted-foreground text-sm leading-relaxed max-w-sm">
            {{ $footerDesc }}
        </p>
        @php
            $socialLinks = [
                ['key' => 'social.facebook', 'title' => 'Facebook', 'icon' => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>'],
                ['key' => 'social.instagram', 'title' => 'Instagram', 'icon' => '<rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/>'],
                ['key' => 'social.youtube', 'title' => 'YouTube', 'icon' => '<path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/>'],
                ['key' => 'social.twitter', 'title' => 'Twitter / X', 'icon' => '<path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/>'],
            ];
            $hasAnySocial = false;
            foreach ($socialLinks as $s) {
                if (\App\Services\SettingService::get($s['key'], null, 'global')) { $hasAnySocial = true; break; }
            }
        @endphp
        @if($hasAnySocial)
        <div class="mt-6">
            <span class="text-sm font-semibold text-foreground">Ikuti Kami</span>
            <div class="flex items-center gap-4 mt-3 text-muted-foreground">
                {{-- RSS --}}
                <a href="{{ $blogUrl }}/rss.xml" class="hover:text-primary transition-colors" title="RSS Feed">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 11a9 9 0 0 1 9 9"/><path d="M4 4a16 16 0 0 1 16 16"/><circle cx="5" cy="19" r="1"/></svg>
                </a>
                @foreach($socialLinks as $social)
                    @php $url = \App\Services\SettingService::get($social['key'], null, 'global'); @endphp
                    @if($url)
                    <a href="{{ $url }}" class="hover:text-primary transition-colors" title="{{ $social['title'] }}" target="_blank" rel="noopener noreferrer">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $social['icon'] !!}</svg>
                    </a>
                    @endif
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Explore --}}
    <div>
        <h4 class="text-sm font-semibold text-foreground mb-3">Jelajahi</h4>
        <ul class="space-y-2 text-sm text-muted-foreground">
            <li><a href="{{ $landingUrl }}/profil/tentang-yayasan" class="hover:text-foreground transition-colors">Profil Pesantren</a></li>
            <li><a href="{{ $landingUrl }}/pendidikan/program-pendidikan" class="hover:text-foreground transition-colors">Program Pendidikan</a></li>
            <li><a href="{{ $landingUrl }}/layanan/psb" class="hover:text-foreground transition-colors">Pendaftaran Santri Baru</a></li>
            <li><a href="{{ $landingUrl }}/layanan/donasi" class="hover:text-foreground transition-colors">Donasi</a></li>
            <li><a href="{{ $landingUrl }}/media/galeri-foto" class="hover:text-foreground transition-colors">Galeri Foto & Video</a></li>
        </ul>
    </div>

    {{-- Informasi --}}
    <div>
        <h4 class="text-sm font-semibold text-foreground mb-3">Pusat Informasi</h4>
        <ul class="space-y-2 text-sm text-muted-foreground">
            <li><a href="{{ $blogUrl }}" class="hover:text-foreground transition-colors">Berita & Artikel</a></li>
            <li><a href="{{ $landingUrl }}/media/pengumuman" class="hover:text-foreground transition-colors">Pengumuman</a></li>
            <li><a href="{{ $landingUrl }}/media/agenda" class="hover:text-foreground transition-colors">Kalender Kegiatan</a></li>
            <li><a href="{{ $landingUrl }}/layanan/kontak" class="hover:text-foreground transition-colors">Hubungi Kami</a></li>
            <li><a href="{{ $landingUrl }}/layanan/faq" class="hover:text-foreground transition-colors">Tanya Jawab (FAQ)</a></li>
        </ul>
    </div>
</div>
