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
                        <span class="ml-1 md:ml-2 font-medium text-white">{{ $title ?? 'Jejaring Alumni Dayama' }}</span>
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
                {{ $pageModel?->sections['hero']['subtitle'] ?? 'Menyambung silaturahmi, merajut karya. Ribuan alumni Dayama telah tersebar di berbagai sektor, mengabdi untuk agama, bangsa, dan negara.' }}
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

    {{-- Sebaran & Statistik Alumni --}}
    <div class="mb-16">
        <div class="card p-8 lg:p-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 divide-y md:divide-y-0 md:divide-x divide-border-subtle">
                <div class="text-center px-4">
                    <div class="text-4xl font-black text-primary mb-2">5.000+</div>
                    <div class="text-sm font-medium text-muted-foreground uppercase tracking-wider">Total Alumni</div>
                </div>
                <div class="text-center px-4 pt-8 md:pt-0">
                    <div class="text-4xl font-black text-accent mb-2">24</div>
                    <div class="text-sm font-medium text-muted-foreground uppercase tracking-wider">Provinsi di Indonesia</div>
                </div>
                <div class="text-center px-4 pt-8 md:pt-0">
                    <div class="text-4xl font-black text-secondary mb-2">8</div>
                    <div class="text-sm font-medium text-muted-foreground uppercase tracking-wider">Negara (Studi & Kerja)</div>
                </div>
                <div class="text-center px-4 pt-8 md:pt-0">
                    <div class="text-4xl font-black text-primary mb-2">3.200+</div>
                    <div class="text-sm font-medium text-muted-foreground uppercase tracking-wider">Anggota Aktif</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kisah Sukses (Story Cards) --}}
    <div class="mb-20">
        <div class="text-center mb-12">
            <h2 class="section-title">Kisah Inspiratif</h2>
            <p class="section-subtitle">Jejak langkah alumni yang membawa manfaat dan menebar kebaikan di masyarakat.</p>
            <div class="section-accent-bar mx-auto"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- Story 1 --}}
            <div class="feature-card p-0 overflow-hidden group flex flex-col h-full bg-surface">
                <div class="aspect-[4/3] bg-muted relative overflow-hidden">
                    <img src="https://ui-avatars.com/api/?name=Dr+Hasan&background=random&size=400" alt="Dr. Hasan" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-4 left-4">
                        <span class="px-2.5 py-1 text-xs font-bold bg-primary text-primary-foreground rounded shadow-sm uppercase tracking-wider mb-2 inline-block">Akademisi</span>
                        <h3 class="text-xl font-bold text-white">Dr. Hasan Al-Banna, Lc., M.A.</h3>
                    </div>
                </div>
                <div class="p-6 flex flex-col flex-grow">
                    <p class="text-muted-foreground text-sm mb-4 italic flex-grow">"Bekal ilmu agama dan kemandirian dari pesantren menjadi pondasi terkuat saya hingga berhasil menyelesaikan studi doktoral di Timur Tengah."</p>
                    <div class="pt-4 border-t border-border-subtle">
                        <div class="text-sm font-bold text-foreground">Dosen UIN Syarif Hidayatullah</div>
                        <div class="text-xs text-muted-foreground">Alumni Angkatan 2010</div>
                    </div>
                </div>
            </div>

            {{-- Story 2 --}}
            <div class="feature-card p-0 overflow-hidden group flex flex-col h-full bg-surface">
                <div class="aspect-[4/3] bg-muted relative overflow-hidden">
                    <img src="https://ui-avatars.com/api/?name=Fatimah&background=random&size=400" alt="Fatimah" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-4 left-4">
                        <span class="px-2.5 py-1 text-xs font-bold bg-secondary text-secondary-foreground rounded shadow-sm uppercase tracking-wider mb-2 inline-block">Pengusaha</span>
                        <h3 class="text-xl font-bold text-white">Fatimah Az-Zahra, S.E.</h3>
                    </div>
                </div>
                <div class="p-6 flex flex-col flex-grow">
                    <p class="text-muted-foreground text-sm mb-4 italic flex-grow">"Program entrepreneurship pesantren sangat aplikatif. Kini saya bisa memberdayakan 50+ santriwati lewat usaha konveksi muslimah yang saya rintis."</p>
                    <div class="pt-4 border-t border-border-subtle">
                        <div class="text-sm font-bold text-foreground">Founder Zahra Hijab</div>
                        <div class="text-xs text-muted-foreground">Alumni Angkatan 2015</div>
                    </div>
                </div>
            </div>

            {{-- Story 3 --}}
            <div class="feature-card p-0 overflow-hidden group flex flex-col h-full bg-surface">
                <div class="aspect-[4/3] bg-muted relative overflow-hidden">
                    <img src="https://ui-avatars.com/api/?name=Letkol+Ahmad&background=random&size=400" alt="Letkol Ahmad" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-4 left-4">
                        <span class="px-2.5 py-1 text-xs font-bold bg-accent text-accent-foreground rounded shadow-sm uppercase tracking-wider mb-2 inline-block">Pemerintahan</span>
                        <h3 class="text-xl font-bold text-white">Letkol Caj Ahmad Rifai</h3>
                    </div>
                </div>
                <div class="p-6 flex flex-col flex-grow">
                    <p class="text-muted-foreground text-sm mb-4 italic flex-grow">"Disiplin ala pesantren adalah tempaan terbaik yang mempersiapkan mental dan fisik saya mengabdi di instansi militer."</p>
                    <div class="pt-4 border-t border-border-subtle">
                        <div class="text-sm font-bold text-foreground">Perwira Pembinaan Mental TNI</div>
                        <div class="text-xs text-muted-foreground">Alumni Angkatan 2005</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-8 text-center">
            <button class="btn btn-outline">Lihat Semua Kisah</button>
        </div>
    </div>

    {{-- CTA Pendaftaran Alumni --}}
    <div id="daftar-alumni" class="mb-8">
        <div class="cta-block relative overflow-hidden">
            <div class="absolute inset-0 islamic-pattern-bg opacity-10 mix-blend-overlay"></div>
            
            <div class="relative z-10 max-w-2xl mx-auto">
                <svg class="w-16 h-16 mx-auto mb-6 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                
                <h2 class="text-3xl font-extrabold mb-4">Belum Bergabung dengan IKA Dayama?</h2>
                <p class="text-primary-foreground/80 mb-8">
                    Mari pererat tali silaturahmi. Daftarkan diri Anda di database alumni untuk mendapatkan update kegiatan reuni, info beasiswa lanjutan, serta loker dari jejaring alumni.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="#" class="btn bg-white text-primary px-8 py-3 rounded-full shadow-lg font-semibold hover:bg-white/90">Isi Formulir Alumni</a>
                    <a href="#" class="btn btn-outline border-white/30 text-white px-8 py-3 rounded-full hover:bg-white/10 transition-colors">Cari Teman Angkatan</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
