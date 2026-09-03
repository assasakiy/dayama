@extends('web.layouts.app')

@php
    $landingDomain = config('platform.sites.landing.domain', env('APP_ROOT_DOMAIN', 'dayama.test'));
    $landingUrl = 'http://' . $landingDomain;
    $blogDomain = config('platform.sites.blog.domain', 'blog.' . $landingDomain);
    $blogUrl = 'http://' . $blogDomain;

    $siteName = \App\Services\SettingService::get('general.site_name', config('app.name'), 'landing');
    $siteDesc = \App\Services\SettingService::get('general.site_description', 'Lembaga Pendidikan & Dakwah Islamiyah yang berkomitmen mencetak generasi Qurani berakhlak mulia.', 'landing');
@endphp

@section('title', $siteName . ' | Beranda')
@section('description', $siteDesc)

@section('content')

{{-- ═══════════════════ HERO — ASYMMETRIC 7/5 SPLIT ═══════════════════ --}}
<section class="hero-home relative overflow-hidden bg-primary">
    @php
        $heroMediaId = $page?->sections['hero']['image_media_id'] ?? null;
        $heroMedia = $heroMediaId ? \Modules\Core\Models\Media::find($heroMediaId) : null;
        $heroImage = $page?->sections['hero']['image'] ?? null;
    @endphp

    @if($heroMedia)
        <img 
            src="{{ parse_url($heroMedia->getUrl('thumb'), PHP_URL_PATH) }}" 
            data-src="{{ parse_url($heroMedia->getUrl(), PHP_URL_PATH) }}" 
            alt="Hero Background" 
            class="absolute inset-0 w-full h-full object-cover z-0 lazyload blur-up opacity-40 mix-blend-overlay pointer-events-none transition-all duration-700"
        >
    @elseif($heroImage)
        <img 
            src="{{ parse_url($heroImage, PHP_URL_PATH) ?? $heroImage }}" 
            alt="Hero Background" 
            class="absolute inset-0 w-full h-full object-cover z-0 opacity-40 mix-blend-overlay pointer-events-none"
        >
    @endif

    <div class="container-page py-16 md:py-24 lg:py-28 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
            {{-- Left 7 cols: copy --}}
            <div class="lg:col-span-7 text-primary-foreground">
                <div class="flex items-center gap-3 mb-6">
                    <span class="inline-flex items-center gap-2 text-xs font-medium uppercase tracking-[0.2em] text-white/70">
                        <span class="w-8 h-px bg-white/40"></span>
                        {{ $page?->sections['hero']['badge'] ?? 'Lembaga Pendidikan & Dakwah Islamiyah' }}
                    </span>
                </div>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black tracking-tight leading-[1.05] max-w-2xl text-balance">
                    {!! $page?->sections['hero']['title'] ?? 'Pondok Pesantren' !!}
                    <span class="block mt-1 text-white/85 font-bold text-3xl md:text-4xl lg:text-5xl">
                        {!! $page?->sections['hero']['highlight'] ?? 'Darul Yatama Wal Masakin' !!}
                    </span>
                </h1>
                <p class="mt-6 text-base md:text-lg text-white/75 max-w-xl leading-relaxed">
                    {{ $page?->sections['hero']['subtitle'] ?? 'Mencetak generasi Qurani yang berakhlak mulia, berwawasan luas, dan siap menjadi pemimpin umat. Lembaga pendidikan Islam modern yang menggabungkan kurikulum nasional dengan pendidikan pesantren.' }}
                </p>
                <div class="mt-8 flex flex-col sm:flex-row gap-3 sm:items-center">
                    <a href="{{ $page?->sections['hero']['button1_url'] ?? $landingUrl . '/pendidikan/program-pendidikan' }}" class="inline-flex items-center gap-2 bg-white text-primary hover:bg-white/90 font-semibold px-6 py-3 rounded-lg shadow-lg shadow-black/10 transition-all text-sm">
                        {{ $page?->sections['hero']['button1_text'] ?? 'Program Pendidikan' }}
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <a href="{{ $page?->sections['hero']['button2_url'] ?? $landingUrl . '/layanan/psb' }}" class="inline-flex items-center gap-2 border border-white/30 text-white hover:bg-white/10 backdrop-blur-sm font-semibold px-6 py-3 rounded-lg transition-all text-sm">
                        {{ $page?->sections['hero']['button2_text'] ?? 'Pendaftaran Santri Baru' }}
                    </a>
                </div>
            </div>

            {{-- Right 5 cols: Islamic geometric SVG ornament --}}
            <div class="lg:col-span-5 hidden lg:flex justify-center items-center relative">
                <div class="relative w-full max-w-sm aspect-square">
                    {{-- 8-pointed star (Khatim Sulayman) --}}
                    <svg viewBox="0 0 200 200" class="absolute inset-0 w-full h-full text-white/15" fill="none" stroke="currentColor" stroke-width="0.8">
                        <rect x="50" y="50" width="100" height="100" transform="rotate(0 100 100)" />
                        <rect x="50" y="50" width="100" height="100" transform="rotate(45 100 100)" />
                        <circle cx="100" cy="100" r="70" />
                        <circle cx="100" cy="100" r="48" />
                        <rect x="50" y="50" width="100" height="100" transform="rotate(22.5 100 100)" opacity="0.5" />
                    </svg>
                    {{-- Center medallion --}}
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-32 h-32 rounded-full bg-white/10 border border-white/20 backdrop-blur-sm flex items-center justify-center shadow-xl">
                            <svg class="w-14 h-14 text-white/90" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        </div>
                    </div>
                    {{-- Orbiting dots --}}
                    <div class="absolute inset-0 animate-spin-slow" style="animation: spin 40s linear infinite;">
                        <span class="absolute top-0 left-1/2 -translate-x-1/2 w-2 h-2 rounded-full bg-white/40"></span>
                        <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-2 h-2 rounded-full bg-white/20"></span>
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-2 h-2 rounded-full bg-white/30"></span>
                        <span class="absolute right-0 top-1/2 -translate-y-1/2 w-2 h-2 rounded-full bg-white/25"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Bottom wave --}}
    <div class="absolute bottom-0 inset-x-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
            <path d="M0 80V40C240 0 480 0 720 40C960 80 1200 80 1440 40V80H0Z" fill="var(--color-background)"/>
        </svg>
    </div>
</section>

{{-- ═══════════════════ STATS — INLINE STRIP ═══════════════════ --}}
<section class="py-10 md:py-12 border-b border-border-subtle">
    <div class="container-page">
        @php
            $statsItems = $page?->sections['stats']['items'] ?? [
                ['number' => '500+', 'label' => 'Santri Aktif'],
                ['number' => '50+', 'label' => 'Tenaga Pengajar'],
                ['number' => '20+', 'label' => 'Tahun Berdiri'],
                ['number' => '1000+', 'label' => 'Alumni'],
            ];
        @endphp
        <dl class="grid grid-cols-2 md:grid-cols-4 gap-y-6 md:gap-0 md:divide-x md:divide-border-subtle">
            @foreach($statsItems as $i => $stat)
            <div class="text-center md:px-6" data-reveal data-reveal-delay="{{ $i * 100 }}">
                <dd class="text-3xl md:text-4xl font-black text-primary tracking-tight tabular-nums" data-counter>{{ $stat['number'] }}</dd>
                <dt class="text-xs md:text-sm text-muted-foreground mt-1 uppercase tracking-wide">{{ $stat['label'] }}</dt>
            </div>
            @endforeach
        </dl>
    </div>
</section>

{{-- ═══════════════════ ABOUT — IMAGE LEFT, PULL QUOTE ═══════════════════ --}}
<section class="py-16 md:py-24 islamic-pattern-bg">
    <div class="container-page">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-center">
            {{-- Image left 5 cols --}}
            <div class="lg:col-span-5 relative order-2 lg:order-1" data-reveal data-reveal-delay="0">
                <div class="aspect-[4/5] rounded-lg bg-surface-muted border border-border-subtle flex items-center justify-center overflow-hidden">
                    @if(isset($page?->sections['about']['image']) && $page->sections['about']['image'])
                        <img src="{{ $page->sections['about']['image'] }}" alt="Tentang Dayama" class="w-full h-full object-cover">
                    @else
                    <div class="text-center p-8">
                        <svg class="w-16 h-16 text-primary/30 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <p class="text-sm text-muted-foreground">Foto Pesantren Dayama</p>
                    </div>
                    @endif
                </div>
                {{-- Floating badge --}}
                <div class="absolute -bottom-4 -right-4 bg-primary text-primary-foreground px-5 py-3 rounded-lg shadow-lg max-w-[180px]">
                    <p class="text-2xl font-black leading-none">20+</p>
                    <p class="text-xs text-white/80 mt-1">Tahun Mengabdi untuk Ummat</p>
                </div>
                {{-- Decorative offset frame --}}
                <div class="absolute -top-3 -left-3 w-20 h-20 border-l-2 border-t-2 border-primary/30 rounded-tl-lg -z-10"></div>
            </div>

            {{-- Text right 7 cols --}}
            <div class="lg:col-span-7 order-1 lg:order-2" data-reveal data-reveal-delay="150">
                <span class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.15em] text-primary">
                    <span class="w-6 h-px bg-primary"></span>
                    {{ $page?->sections['about']['badge'] ?? 'Tentang Kami' }}
                </span>
                <h2 class="mt-4 text-3xl md:text-4xl font-bold tracking-tight leading-tight text-balance">
                    {!! $page?->sections['about']['heading'] ?? 'Mengenal Lebih Dekat<br>Pondok Pesantren Dayama' !!}
                </h2>
                <p class="mt-5 text-muted-foreground leading-relaxed">
                    {{ $page?->sections['about']['description1'] ?? 'Pondok Pesantren Darul Yatama Wal Masakin (Dayama) adalah lembaga pendidikan Islam yang berdedikasi untuk memberikan pendidikan berkualitas kepada anak-anak yatim, dhuafa, dan masyarakat umum. Dengan memadukan kurikulum nasional dan kurikulum pesantren, Dayama berupaya mencetak generasi yang tidak hanya unggul secara akademis, tetapi juga kokoh dalam iman dan akhlak.' }}
                </p>

                {{-- Pull quote --}}
                <blockquote class="mt-6 pl-4 border-l-2 border-primary text-base italic text-foreground/80">
                    "Pendidikan terbaik adalah yang memadukan ilmu pengetahuan dengan ketakwaan, akhlak mulia dengan kemandirian."
                </blockquote>

                <div class="mt-7 flex flex-col sm:flex-row gap-3">
                    <a href="{{ $page?->sections['about']['button1_url'] ?? $landingUrl . '/profil/tentang-yayasan' }}" class="btn btn-primary">
                        {{ $page?->sections['about']['button1_text'] ?? 'Selengkapnya' }}
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <a href="{{ $page?->sections['about']['button2_url'] ?? $landingUrl . '/profil/visi-misi' }}" class="btn btn-outline">
                        {{ $page?->sections['about']['button2_text'] ?? 'Visi & Misi' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════ FEATURES — BENTO + HORIZONTAL ROWS ═══════════════════ --}}
<section class="py-16 md:py-24 bg-surface-muted/40">
    <div class="container-page">
        {{-- Left-aligned header --}}
        <div class="max-w-2xl mb-12" data-reveal>
            <span class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.15em] text-primary">
                <span class="w-6 h-px bg-primary"></span>
                {{ $page?->sections['features']['badge'] ?? 'Keunggulan' }}
            </span>
            <h2 class="mt-4 text-3xl md:text-4xl font-bold tracking-tight">{{ $page?->sections['features']['heading'] ?? 'Mengapa Memilih Dayama?' }}</h2>
            <p class="mt-3 text-muted-foreground">{{ $page?->sections['features']['subtitle'] ?? 'Program pendidikan terpadu yang mempersiapkan santri untuk kebahagiaan dunia dan akhirat.' }}</p>
        </div>

        @php
            $featureItems = $page?->sections['features']['items'] ?? [
                ['icon' => 'book-open', 'title' => 'Kurikulum Terpadu', 'description' => 'Menggabungkan kurikulum nasional (Kemendikbud) dengan kurikulum pesantren dan Kemenag secara harmonis.'],
                ['icon' => 'star', 'title' => 'Program Tahfidz Al-Quran', 'description' => 'Program hafalan Al-Quran dengan metode yang teruji, dibimbing oleh para hafidz dan hafidzah berpengalaman.'],
                ['icon' => 'globe', 'title' => 'Bahasa Arab & Inggris', 'description' => 'Penguasaan bahasa internasional melalui program intensif percakapan harian dan laboratorium bahasa.'],
                ['icon' => 'home', 'title' => 'Fasilitas Lengkap', 'description' => 'Asrama nyaman, masjid, laboratorium, perpustakaan, lapangan olahraga, dan ruang multimedia modern.'],
                ['icon' => 'heart', 'title' => 'Pembinaan Karakter', 'description' => 'Pendidikan akhlak melalui suri tauladan, kedisiplinan, dan kegiatan keagamaan rutin yang membentuk kepribadian santri.'],
                ['icon' => 'gift', 'title' => 'Beasiswa Yatim & Dhuafa', 'description' => 'Membuka akses pendidikan berkualitas bagi anak-anak yatim dan keluarga kurang mampu melalui program beasiswa penuh.'],
            ];
        @endphp

        {{-- Bento grid: first item large, rest 2-col compact --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-5">
            {{-- Highlight card (col-span-2, row-span-2) --}}
            <div class="lg:col-span-2 lg:row-span-2 bg-background border border-border-subtle rounded-lg p-8 md:p-10 flex flex-col justify-between min-h-[280px] relative overflow-hidden" data-reveal>
                <div class="absolute top-0 right-0 w-40 h-40 bg-primary/5 rounded-bl-[100%]"></div>
                <div class="relative">
                    <div class="w-12 h-12 rounded-lg bg-primary-muted text-primary flex items-center justify-center mb-5">
                        <x-icon :icon="$featureItems[0]['icon'] ?? 'check-circle'" class="w-6 h-6" />
                    </div>
                    <h3 class="text-xl md:text-2xl font-bold mb-3">{{ $featureItems[0]['title'] }}</h3>
                    <p class="text-muted-foreground leading-relaxed max-w-md">{{ $featureItems[0]['description'] }}</p>
                </div>
                <div class="relative mt-6">
                    <span class="text-xs uppercase tracking-wider text-muted-foreground/60">Program Unggulan</span>
                </div>
            </div>

            {{-- Compact horizontal cards --}}
            @foreach(array_slice($featureItems, 1) as $fIndex => $feature)
            <div class="bg-background border border-border-subtle rounded-lg p-5 flex gap-4 items-start hover:border-secondary hover:shadow-elevated transition-all" data-reveal data-reveal-delay="{{ ($fIndex + 1) * 100 }}">
                <div class="w-10 h-10 shrink-0 rounded-md bg-primary-muted text-primary flex items-center justify-center">
                    <x-icon :icon="$feature['icon'] ?? 'check-circle'" class="w-5 h-5" />
                </div>
                <div>
                    <h3 class="font-semibold text-foreground mb-1">{{ $feature['title'] }}</h3>
                    <p class="text-sm text-muted-foreground leading-relaxed">{{ $feature['description'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════ PROGRAMS — NUMBERED HORIZONTAL ROWS ═══════════════════ --}}
<section class="py-16 md:py-24">
    <div class="container-page">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 mb-12">
            <div class="lg:col-span-8" data-reveal>
                <span class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.15em] text-primary">
                    <span class="w-6 h-px bg-primary"></span>
                    {{ $page?->sections['programs']['badge'] ?? 'Program Kami' }}
                </span>
                <h2 class="mt-4 text-3xl md:text-4xl font-bold tracking-tight">{{ $page?->sections['programs']['heading'] ?? 'Jenjang Pendidikan' }}</h2>
                <p class="mt-3 text-muted-foreground max-w-xl">{{ $page?->sections['programs']['subtitle'] ?? 'Beragam jenjang pendidikan yang tersedia untuk membentuk generasi yang berilmu dan berakhlak.' }}</p>
            </div>
            <div class="lg:col-span-4 flex lg:items-end lg:justify-end" data-reveal data-reveal-delay="100">
                <a href="{{ $landingUrl }}/pendidikan/program-pendidikan" class="text-sm font-medium text-primary hover:underline underline-offset-4 inline-flex items-center gap-1">
                    Lihat semua program
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>

        @php
            $programItems = $page?->sections['programs']['items'] ?? [
                ['icon' => 'book', 'title' => 'Pondok Pesantren', 'description' => 'Pendidikan kepesantrenan dengan asrama terpadu, kajian kitab kuning, dan pembinaan akhlak 24 jam.', 'url' => $landingUrl . '/pendidikan/pondok-pesantren', 'image' => null],
                ['icon' => 'graduation-cap', 'title' => 'Madrasah', 'description' => 'Pendidikan formal dari tingkat Ibtidaiyah hingga Aliyah dengan kurikulum Kemenag yang terstandarisasi.', 'url' => $landingUrl . '/pendidikan/madrasah', 'image' => null],
                ['icon' => 'book-open', 'title' => 'TPQ (Taman Pendidikan Quran)', 'description' => 'Pembelajaran Al-Quran untuk anak usia dini dengan metode yang menyenangkan dan mudah dipahami.', 'url' => $landingUrl . '/pendidikan/tpq', 'image' => null],
            ];
        @endphp

        <div class="divide-y divide-border-subtle border-y border-border-subtle">
            @foreach($programItems as $index => $program)
            <a href="{{ $program['url'] ?? '#' }}" class="group grid grid-cols-12 gap-4 md:gap-6 py-6 md:py-8 items-center hover:bg-surface-muted/40 transition-colors -mx-4 px-4 rounded-lg" data-reveal data-reveal-delay="{{ $index * 120 }}">
                {{-- Thumbnail (jika ada image) --}}
                @if(isset($program['image']) && $program['image'])
                <div class="col-span-3 md:col-span-2">
                    <div class="aspect-[4/3] rounded-md overflow-hidden bg-surface-muted">
                        <img src="{{ $program['image'] }}" alt="{{ $program['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>
                </div>
                <div class="col-span-1 md:col-span-1">
                    <span class="text-xl md:text-2xl font-black text-primary/30 group-hover:text-primary transition-colors tabular-nums">{{ str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="col-span-8 md:col-span-6">
                    <h3 class="text-lg md:text-xl font-bold text-foreground group-hover:text-primary transition-colors">{{ $program['title'] }}</h3>
                    <p class="text-sm text-muted-foreground mt-1 max-w-2xl">{{ $program['description'] }}</p>
                </div>
                @else
                <div class="col-span-2 md:col-span-1">
                    <span class="text-2xl md:text-3xl font-black text-primary/30 group-hover:text-primary transition-colors tabular-nums">{{ str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="col-span-10 md:col-span-7">
                    <h3 class="text-lg md:text-xl font-bold text-foreground group-hover:text-primary transition-colors">{{ $program['title'] }}</h3>
                    <p class="text-sm text-muted-foreground mt-1 max-w-2xl">{{ $program['description'] }}</p>
                </div>
                @endif
                <div class="col-span-12 md:col-span-4 flex md:justify-end">
                    <span class="inline-flex items-center gap-2 text-sm font-medium text-muted-foreground group-hover:text-primary transition-colors">
                        Pelajari
                        <span class="w-8 h-8 rounded-full border border-border-strong group-hover:border-primary group-hover:bg-primary group-hover:text-primary-foreground flex items-center justify-center transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </span>
                    </span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════ ISLAMIC DIVIDER ═══════════════════ --}}
<div class="container-page">
    <div class="islamic-divider">
        <svg class="islamic-divider-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
    </div>
</div>

{{-- ═══════════════════ BERITA — FEATURED + SIDEBAR ═══════════════════ --}}
<section class="py-16 md:py-24">
    <div class="container-page">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between mb-10">
            <div data-reveal>
                <span class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.15em] text-primary">
                    <span class="w-6 h-px bg-primary"></span>
                    Berita & Informasi
                </span>
                <h2 class="mt-4 text-3xl md:text-4xl font-bold tracking-tight">Berita Terbaru</h2>
            </div>
            <a href="{{ $blogUrl }}" class="mt-4 sm:mt-0 text-sm font-medium text-primary hover:underline underline-offset-4 inline-flex items-center gap-1" data-reveal data-reveal-delay="100">
                Lihat Semua Berita
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>

        @php
            $latestPosts = \Modules\CMS\Models\Post::where('status', 'published')
                ->latest('published_at')
                ->take(3)
                ->get();
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Featured large (2 cols) --}}
            @isset($latestPosts[0])
                @php 
                    /** @var \Modules\CMS\Models\Post $featured */
                    $featured = $latestPosts[0]; 
                @endphp
                <a href="{{ $blogUrl }}/{{ $featured->slug }}" class="lg:col-span-2 group block bg-background border border-border-subtle rounded-lg overflow-hidden hover:shadow-elevated hover:border-secondary transition-all" data-reveal>
                    @if($featured->getFirstMediaUrl('cover'))
                        <div class="aspect-[16/9] overflow-hidden bg-surface-muted">
                            <img src="{{ $featured->getFirstMediaUrl('cover') }}" alt="{{ $featured->title }}" class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-500">
                        </div>
                    @else
                        <div class="aspect-[16/9] bg-surface-muted flex items-center justify-center">
                            <svg class="w-12 h-12 text-primary/20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                        </div>
                    @endif
                    <div class="p-6 md:p-8">
                        @if($featured->category)
                            <span class="badge badge-primary mb-3">{{ $featured->category->name }}</span>
                        @endif
                        <h3 class="text-xl md:text-2xl font-bold text-foreground group-hover:text-primary transition-colors mb-3">{{ $featured->title }}</h3>
                        <p class="text-muted-foreground line-clamp-2">{{ $featured->excerpt ?? Str::limit(strip_tags($featured->body), 160) }}</p>
                        <div class="mt-4 text-xs text-muted-foreground">{{ $featured->published_at?->translatedFormat('d F Y') ?? $featured->created_at->translatedFormat('d F Y') }}</div>
                    </div>
                </a>
            @else
                <div class="lg:col-span-2 bg-background border border-border-subtle rounded-lg p-8 flex items-center justify-center min-h-[300px] text-center">
                    <div>
                        <svg class="w-12 h-12 text-primary/20 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                        <p class="text-sm text-muted-foreground">Berita terbaru akan muncul di sini</p>
                    </div>
                </div>
            @endisset

            {{-- Compact sidebar (1 col) --}}
            <div class="flex flex-col gap-4">
                @for($i = 1; $i < 3; $i++)
                    @isset($latestPosts[$i])
                        @php 
                            /** @var \Modules\CMS\Models\Post $post */
                            $post = $latestPosts[$i]; 
                        @endphp
                        <a href="{{ $blogUrl }}/{{ $post->slug }}" class="group flex gap-4 bg-background border border-border-subtle rounded-lg p-4 hover:shadow-elevated hover:border-secondary transition-all" data-reveal data-reveal-delay="{{ $i * 120 }}">
                            @if($post->getFirstMediaUrl('cover'))
                                <div class="w-20 h-20 shrink-0 rounded-md overflow-hidden bg-surface-muted">
                                    <img src="{{ $post->getFirstMediaUrl('cover') }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="w-20 h-20 shrink-0 rounded-md bg-surface-muted flex items-center justify-center">
                                    <svg class="w-6 h-6 text-primary/20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                </div>
                            @endif
                            <div class="min-w-0">
                                @if($post->category)
                                    <span class="text-[10px] font-semibold uppercase tracking-wide text-primary">{{ $post->category->name }}</span>
                                @endif
                                <h3 class="font-semibold text-sm text-foreground group-hover:text-primary transition-colors line-clamp-2 mt-1">{{ $post->title }}</h3>
                                <span class="text-xs text-muted-foreground mt-1 block">{{ $post->published_at?->translatedFormat('d M') ?? $post->created_at->translatedFormat('d M') }}</span>
                            </div>
                        </a>
                    @else
                        <div class="flex gap-4 bg-background border border-border-subtle rounded-lg p-4">
                            <div class="w-20 h-20 shrink-0 rounded-md bg-surface-muted flex items-center justify-center">
                                <svg class="w-6 h-6 text-primary/20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold uppercase tracking-wide text-muted-foreground">Informasi</span>
                                <h3 class="font-semibold text-sm text-muted-foreground mt-1">Berita akan muncul di sini</h3>
                            </div>
                        </div>
                    @endisset
                @endfor
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════ TESTIMONIAL — SLIDER 3 CARD + ARROW ═══════════════════ --}}
<section class="py-16 md:py-20 overflow-hidden" x-data="testimonialSlider" @mouseenter="stopAuto()" @mouseleave="startAuto()">
    <div class="container-page mb-10" data-reveal>
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-primary text-center">Testimoni</p>
        <h2 class="mt-2 text-2xl md:text-3xl font-bold tracking-tight text-center">Apa Kata Mereka</h2>
    </div>
    <div class="container-page relative">
        <button @click="prev" class="absolute left-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-background border border-border-subtle shadow-sm hover:border-primary hover:text-primary flex items-center justify-center transition-colors" aria-label="Sebelumnya">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <div class="overflow-hidden mx-12" x-ref="track">
            <div class="flex gap-6 transition-transform duration-500 ease-out" :style="`transform: translateX(-${offset}px)`">
                @php
                    $testimonials = [
                        ['name' => 'Bpk. Ahmad Fauzi', 'role' => 'Wali Santri', 'text' => 'Alhamdulillah, sejak mondok di sini, anak kami menunjukkan perkembangan akhlak yang luar biasa.'],
                        ['name' => 'Siti Aminah', 'role' => 'Alumni', 'text' => 'Bekal ilmu agama dan keterampilan sangat bermanfaat. Lingkungan pesantren membentuk karakter saya lebih tangguh.'],
                        ['name' => 'H. Zainuddin', 'role' => 'Tokoh Masyarakat', 'text' => 'Lembaga pendidikan yang sangat berkontribusi bagi masyarakat. Lulusannya pintar mengaji dan pandai berbaur.'],
                        ['name' => 'Dra. Wahyuni', 'role' => 'Orang Tua', 'text' => 'Fasilitas terus meningkat. Guru ramah, suportif, perhatian penuh pada tiap santri.'],
                        ['name' => 'M. Rizky', 'role' => 'Santri', 'text' => 'Masa nyantri kenangan terindah. Kemandirian dan adab jadi pondasi hidup hingga sekarang.'],
                        ['name' => 'Ibu Ningsih', 'role' => 'Orang Tua', 'text' => 'Awalnya berat melepas anak ke asrama, tapi melihat perubahannya rajin shalat dan mandiri, rasa syukur tak terhingga.'],
                    ];
                @endphp
                @foreach($testimonials as $testi)
                <div class="shrink-0 w-full md:w-[calc((100%-24px)/2)] lg:w-[calc((100%-48px)/3)] xl:w-[calc((100%-72px)/4)]">
                    <div class="bg-background border border-border-subtle rounded-lg p-6 h-full">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center shrink-0 font-bold text-sm">{{ substr($testi['name'], 0, 1) }}</div>
                            <div>
                                <h4 class="font-semibold text-foreground text-sm">{{ $testi['name'] }}</h4>
                                <p class="text-xs text-muted-foreground">{{ $testi['role'] }}</p>
                            </div>
                        </div>
                        <p class="text-muted-foreground text-sm leading-relaxed">"{{ $testi['text'] }}"</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <button @click="next" class="absolute right-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-background border border-border-subtle shadow-sm hover:border-primary hover:text-primary flex items-center justify-center transition-colors" aria-label="Berikutnya">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>
</section>

{{-- ═══════════════════ MITRA — MARQUEE KANAN → KIRI ═══════════════════ --}}
<section class="py-14 md:py-20 overflow-hidden border-t border-border-subtle">
    <div class="container-page mb-8" data-reveal>
        <p class="text-center text-xs font-semibold uppercase tracking-[0.2em] text-muted-foreground">
            Dipercaya & bermitra dengan
        </p>
    </div>
    <div class="marquee">
        <div class="marquee-track">
            {{-- Set 1 --}}
            @php
                $partners = $page?->sections['partners']['items'] ?? [
                    ['name' => 'Kemenag RI', 'logo' => null],
                    ['name' => 'Kemendikbud RI', 'logo' => null],
                    ['name' => 'Pemkot Aceh', 'logo' => null],
                    ['name' => 'BSMI', 'logo' => null],
                    ['name' => 'Dompet Dhuafa', 'logo' => null],
                    ['name' => 'ACT', 'logo' => null],
                    ['name' => 'Baznas', 'logo' => null],
                    ['name' => 'Wakaf Salman', 'logo' => null],
                ];
            @endphp
            @foreach($partners as $partner)
                <div class="flex items-center justify-center shrink-0 h-16 md:h-20 px-6 md:px-8 grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition-all duration-300">
                    @if($partner['logo'])
                        <img src="{{ $partner['logo'] }}" alt="{{ $partner['name'] }}" class="h-full w-auto object-contain">
                    @else
                        <span class="text-lg md:text-xl font-bold text-muted-foreground/50 whitespace-nowrap">{{ $partner['name'] }}</span>
                    @endif
                </div>
            @endforeach
            {{-- Duplicate set untuk loop seamless --}}
            @foreach($partners as $partner)
                <div class="flex items-center justify-center shrink-0 h-16 md:h-20 px-6 md:px-8 grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition-all duration-300" aria-hidden="true">
                    @if($partner['logo'])
                        <img src="{{ $partner['logo'] }}" alt="{{ $partner['name'] }}" class="h-full w-auto object-contain">
                    @else
                        <span class="text-lg md:text-xl font-bold text-muted-foreground/50 whitespace-nowrap">{{ $partner['name'] }}</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════ CTA — SLIM SURFACE BAND ═══════════════════ --}}
<section class="py-12 md:py-16 bg-surface-muted/10 border-t border-border-subtle">
    <div class="container-page">
        <div class="bg-surface border border-border-subtle rounded-lg p-8 md:p-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6 relative overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300" data-reveal>
            <div class="absolute -top-12 -left-12 w-48 h-48 bg-primary/5 rounded-full blur-2xl"></div>
            
            <div class="relative z-10 flex items-start gap-4 md:gap-6">
                <div class="w-12 h-12 shrink-0 rounded-md bg-primary/10 text-primary flex items-center justify-center">
                    <x-icon :icon="$globalCta->icon ?? 'heart'" class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-foreground tracking-tight">{{ $globalCta->title ?? 'Mari Bersama Membangun Generasi Qurani' }}</h2>
                    <p class="mt-2 text-sm text-muted-foreground max-w-2xl leading-relaxed">
                        {{ $globalCta->description ?? 'Setiap donasi Anda adalah investasi akhirat yang tak ternilai. Bantu anak-anak yatim dan dhuafa mendapatkan pendidikan terbaik.' }}
                    </p>
                </div>
            </div>
            <div class="relative z-10 flex flex-col sm:flex-row gap-3 shrink-0">
                <a href="{{ $globalCta->resolved_url ?? $landingUrl . '/layanan/donasi' }}" class="btn btn-primary">
                    {{ $globalCta->button_text ?? 'Donasi Sekarang' }}
                    <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
                <a href="{{ $landingUrl }}/layanan/kontak" class="btn btn-outline">Hubungi Kami</a>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════ FLOATING WHATSAPP ═══════════════════ --}}
<a href="https://wa.me/6281234567890" target="_blank" rel="noopener noreferrer" class="fixed bottom-6 left-6 z-50 w-14 h-14 rounded-full bg-[#25D366] text-white shadow-lg hover:shadow-xl hover:scale-105 transition-all flex items-center justify-center" aria-label="Hubungi kami via WhatsApp">
    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
</a>

@endsection
