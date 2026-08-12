@extends('web.layouts.app')

@section('content')
<section class="hero-subpage py-16 md:py-20 pb-24 md:pb-32 relative z-0" x-data="faqSection()">
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
                        <span class="ml-1 md:ml-2 font-medium text-white">Layanan</span>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-white/60" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 md:ml-2 font-medium text-white">{{ $title ?? 'FAQ' }}</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
            <div class="max-w-3xl lg:w-1/2">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black tracking-tight text-white mb-6 leading-tight">
                {!! $pageModel?->sections['hero']['title'] ?? $title ?? '' !!}
                @if(!empty($pageModel?->sections['hero']['highlight']))
                <span class="block text-primary">{!! $pageModel->sections['hero']['highlight'] !!}</span>
                @endif
            </h1>
            <p class="text-lg md:text-xl text-white/90 leading-relaxed max-w-3xl">
                {{ $pageModel?->sections['hero']['subtitle'] ?? 'Temukan jawaban cepat untuk pertanyaan umum seputar Pendaftaran Santri Baru, program pendidikan, dan layanan Dayama.' }}
            </p>
            </div>
            
            <div class="lg:w-1/2 max-w-xl lg:ml-auto w-full">
                {{-- Search Bar --}}
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input 
                        type="text" 
                        x-model="searchQuery" 
                        @input="filterFaqs()"
                        class="w-full pl-12 pr-4 py-4 border-0 rounded-2xl bg-white text-white shadow-lg focus:outline-none focus:ring-4 focus:ring-primary/30 transition-all text-lg" 
                        placeholder="Cari pertanyaan... (Misal: biaya, jadwal)"
                    >
                </div>
            </div>
        </div>
    </div>
    
    <div class="absolute bottom-0 inset-x-0 pointer-events-none z-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto text-background">
            <path d="M0 80V40C240 0 480 0 720 40C960 80 1200 80 1440 40V80H0Z" fill="currentColor"/>
        </svg>
    </div>
</section>

<div class="container-page py-12 md:py-16 max-w-4xl mx-auto" x-data="faqSection()">
    {{-- Kategori Filter --}}
    <div class="flex flex-wrap justify-center gap-2 mb-10">
        <button @click="setCategory('Semua')" :class="activeCategory === 'Semua' ? 'bg-primary text-primary-foreground' : 'bg-surface text-foreground hover:bg-surface-muted'" class="px-5 py-2 rounded-full text-sm font-bold transition-colors border border-border-subtle shadow-sm">
            Semua
        </button>
        <button @click="setCategory('PSB')" :class="activeCategory === 'PSB' ? 'bg-primary text-primary-foreground' : 'bg-surface text-foreground hover:bg-surface-muted'" class="px-5 py-2 rounded-full text-sm font-bold transition-colors border border-border-subtle shadow-sm">
            PSB & Pendaftaran
        </button>
        <button @click="setCategory('Pendidikan')" :class="activeCategory === 'Pendidikan' ? 'bg-primary text-primary-foreground' : 'bg-surface text-foreground hover:bg-surface-muted'" class="px-5 py-2 rounded-full text-sm font-bold transition-colors border border-border-subtle shadow-sm">
            Pendidikan
        </button>
        <button @click="setCategory('Pondok')" :class="activeCategory === 'Pondok' ? 'bg-primary text-primary-foreground' : 'bg-surface text-foreground hover:bg-surface-muted'" class="px-5 py-2 rounded-full text-sm font-bold transition-colors border border-border-subtle shadow-sm">
            Asrama & Fasilitas
        </button>
        <button @click="setCategory('Biaya')" :class="activeCategory === 'Biaya' ? 'bg-primary text-primary-foreground' : 'bg-surface text-foreground hover:bg-surface-muted'" class="px-5 py-2 rounded-full text-sm font-bold transition-colors border border-border-subtle shadow-sm">
            Biaya & Administrasi
        </button>
    </div>

    {{-- Accordion List --}}
    <div class="space-y-4">
        <template x-for="faq in filteredFaqs" :key="faq.id">
            <div class="card p-0 overflow-hidden transition-all duration-200" :class="faq.isOpen ? 'ring-2 ring-primary/20 border-primary/30' : ''">
                <button 
                    @click="toggleFaq(faq.id)" 
                    class="w-full px-6 py-4 flex items-center justify-between text-left focus:outline-none"
                >
                    <div class="flex items-center gap-3">
                        <span class="px-2.5 py-1 text-[10px] font-bold bg-primary/10 text-primary rounded uppercase tracking-wider" x-text="faq.category"></span>
                        <h3 class="text-base md:text-lg font-bold text-foreground" x-text="faq.question" :class="faq.isOpen ? 'text-primary' : ''"></h3>
                    </div>
                    <svg class="w-5 h-5 text-muted-foreground shrink-0 transition-transform duration-300" :class="faq.isOpen ? 'rotate-180 text-primary' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div 
                    x-show="faq.isOpen" 
                    x-collapse
                    class="px-6 pb-5 pt-1 border-t border-border-subtle/50 text-muted-foreground"
                >
                    <div x-html="faq.answer" class="prose prose-sm dark:prose-invert max-w-none mt-4"></div>
                </div>
            </div>
        </template>

        {{-- Empty State --}}
        <div x-show="filteredFaqs.length === 0" x-cloak class="card p-12 text-center text-muted-foreground">
            <svg class="w-16 h-16 text-muted-foreground/30 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <h3 class="text-xl font-bold text-foreground mb-2">Pertanyaan Tidak Ditemukan</h3>
            <p class="text-muted-foreground">Tidak ada hasil yang cocok dengan pencarian "<span x-text="searchQuery" class="font-bold"></span>".<br>Silakan coba kata kunci lain atau hubungi admin.</p>
            <button @click="searchQuery = ''; filterFaqs()" class="btn btn-outline mt-4">Reset Pencarian</button>
        </div>
    </div>

    {{-- Bantuan Tambahan --}}
    <div class="mt-16 card p-8 border-primary/20 bg-primary/5 text-center flex flex-col items-center">
        <h3 class="text-xl font-bold text-foreground mb-2">Belum menemukan jawaban?</h3>
        <p class="text-muted-foreground mb-6 max-w-lg">Tim administrasi kami siap membantu menjawab pertanyaan spesifik Anda seputar Dayama.</p>
        <a href="{{ $landingUrl ?? 'http://test-blog.test' }}/layanan/kontak" class="btn btn-primary px-8">Hubungi Admin</a>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('faqSection', () => ({
            searchQuery: '',
            activeCategory: 'Semua',
            faqs: [
                {
                    id: 1,
                    category: 'PSB',
                    question: 'Kapan Pendaftaran Santri Baru (PSB) dibuka?',
                    answer: 'Pendaftaran Santri Baru (PSB) umumnya dibuka dalam dua gelombang. Gelombang 1 biasanya dimulai pada awal <strong>Januari hingga Maret</strong>, sedangkan Gelombang 2 (jika kuota masih tersedia) dibuka pada bulan <strong>April hingga Mei</strong>. Untuk tanggal pastinya, silakan pantau terus website pendaftaran resmi kami di <a href="https://psb.dayama.web.id" class="text-primary hover:underline">psb.dayama.web.id</a>.',
                    isOpen: false
                },
                {
                    id: 2,
                    category: 'PSB',
                    question: 'Apakah bisa mendaftar secara Online?',
                    answer: 'Tentu. Dayama menggunakan sistem pendaftaran 100% online untuk memudahkan pendaftar dari luar daerah. Anda dapat mengisi formulir, mengunggah berkas, dan memantau status kelulusan langsung melalui portal PSB.',
                    isOpen: false
                },
                {
                    id: 3,
                    category: 'Pendidikan',
                    question: 'Lembaga pendidikan apa saja yang tersedia di Dayama?',
                    answer: 'Dayama memiliki jenjang pendidikan yang komprehensif mulai dari tingkat dasar hingga menengah atas, meliputi: PAUD/TK, TPQ (Taman Pendidikan Al-Qur\'an), Madrasah Ibtidaiyah (MI), Madrasah Tsanawiyah (MTs), Madrasah Aliyah (MA), SMK Vokasi, Madrasah Diniyah Salafiyah, dan Pondok Pesantren Tahfidz.',
                    isOpen: false
                },
                {
                    id: 4,
                    category: 'Pendidikan',
                    question: 'Apakah santri wajib tinggal di asrama?',
                    answer: 'Untuk santri tingkat MTs dan MA/SMK <strong>diwajibkan</strong> tinggal di asrama agar dapat mengikuti kurikulum pesantren dan pembinaan karakter secara penuh (Full Day & Boarding School). Sementara untuk santri PAUD, TPQ, dan MI diperbolehkan pulang (Non-Mukim).',
                    isOpen: false
                },
                {
                    id: 5,
                    category: 'Pondok',
                    question: 'Bagaimana fasilitas asrama santri?',
                    answer: 'Fasilitas asrama terpisah antara putra dan putri, dilengkapi ranjang susun, lemari pribadi, kipas angin, dan kamar mandi di setiap blok. Tersedia juga dapur umum, fasilitas air minum RO (Reverse Osmosis) gratis, klinik kesehatan, dan lapangan olahraga.',
                    isOpen: false
                },
                {
                    id: 6,
                    category: 'Pondok',
                    question: 'Berapa kali jam besuk atau penjengukan santri?',
                    answer: 'Wali santri dapat menjenguk putranya/putrinya <strong>satu kali dalam sebulan</strong>, tepatnya pada hari Ahad minggu pertama. Selain itu, komunikasi bisa dilakukan melalui layanan telepon wartel pesantren pada jadwal yang telah ditentukan.',
                    isOpen: false
                },
                {
                    id: 7,
                    category: 'Biaya',
                    question: 'Berapa estimasi biaya masuk dan SPP bulanan?',
                    answer: 'Biaya pendaftaran awal berkisar antara Rp 1.500.000 - Rp 2.500.000 (sudah termasuk seragam, kasur, lemari, dan perlengkapan awal). Sedangkan SPP bulanan berkisar antara Rp 450.000 - Rp 600.000 yang sudah mencakup biaya pendidikan, asrama, dan makan 3x sehari. <em>*Rincian biaya valid akan diterbitkan menjelang pembukaan PSB.</em>',
                    isOpen: false
                },
                {
                    id: 8,
                    category: 'Biaya',
                    question: 'Apakah ada beasiswa untuk anak Yatim / Dhuafa?',
                    answer: 'Ya, Dayama (Darul Yatama Wal-Masakin) sangat memprioritaskan pendidikan anak yatim dan dhuafa. Kami menyediakan kuota beasiswa penuh (gratis biaya pendidikan dan asrama 100%) dengan syarat melampirkan Surat Keterangan Tidak Mampu (SKTM) dari desa dan melewati tahap survey dari tim kami.',
                    isOpen: false
                }
            ],
            filteredFaqs: [],
            
            init() {
                this.filteredFaqs = this.faqs;
            },
            
            setCategory(category) {
                this.activeCategory = category;
                this.filterFaqs();
            },
            
            filterFaqs() {
                const query = this.searchQuery.toLowerCase();
                
                this.filteredFaqs = this.faqs.filter(faq => {
                    const matchesCategory = this.activeCategory === 'Semua' || faq.category === this.activeCategory;
                    const matchesSearch = faq.question.toLowerCase().includes(query) || faq.answer.toLowerCase().includes(query);
                    return matchesCategory && matchesSearch;
                });
            },
            
            toggleFaq(id) {
                // Close others
                this.faqs.forEach(faq => {
                    if (faq.id !== id) faq.isOpen = false;
                });
                
                // Toggle clicked
                const faq = this.faqs.find(f => f.id === id);
                if (faq) faq.isOpen = !faq.isOpen;
                
                // Refresh filtered array
                this.filterFaqs();
            }
        }))
    })
</script>
@endsection
