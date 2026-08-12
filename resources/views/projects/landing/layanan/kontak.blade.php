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
                        <span class="ml-1 md:ml-2 font-medium">Layanan</span>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-white/60" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 md:ml-2 font-medium text-white">{{ $title ?? 'Hubungi Kami' }}</span>
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
                {{ $pageModel?->sections['hero']['subtitle'] ?? 'Pintu pesantren selalu terbuka. Silakan hubungi kami untuk informasi pendaftaran, kerja sama, kunjungan studi, atau pertanyaan lainnya.' }}
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
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20">
        {{-- Bagian Kiri: Informasi Kontak & Maps --}}
        <div class="space-y-8">
            <div class="card p-8 space-y-6">
                <h3 class="text-2xl font-bold text-foreground">Informasi Kontak</h3>
                
                {{-- Alamat --}}
                <div class="flex gap-4 items-start group">
                    <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center shrink-0 group-hover:bg-primary group-hover:text-primary-foreground transition-colors text-primary">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-foreground text-sm uppercase tracking-wider mb-1">Alamat Utama</h4>
                        <p class="text-muted-foreground">Jl. TGH. Mutawalli, Desa Jerowaru, Kec. Jerowaru, Kabupaten Lombok Timur, Nusa Tenggara Barat 83672</p>
                    </div>
                </div>

                {{-- Telepon & WA --}}
                <div class="flex gap-4 items-start group">
                    <div class="w-12 h-12 rounded-full bg-accent/10 flex items-center justify-center shrink-0 group-hover:bg-accent group-hover:text-accent-foreground transition-colors text-accent">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-foreground text-sm uppercase tracking-wider mb-1">Telepon & WhatsApp</h4>
                        <p class="text-foreground font-medium mb-1">(0376) 211234 <span class="text-muted-foreground text-sm font-normal">(Jam Kerja)</span></p>
                        <a href="https://wa.me/6281234567890" target="_blank" class="text-accent hover:underline font-medium inline-flex items-center gap-1">
                            +62 812-3456-7890 (Admin PSB)
                        </a>
                    </div>
                </div>

                {{-- Email --}}
                <div class="flex gap-4 items-start group">
                    <div class="w-12 h-12 rounded-full bg-secondary/10 flex items-center justify-center shrink-0 group-hover:bg-secondary group-hover:text-secondary-foreground transition-colors text-secondary">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-foreground text-sm uppercase tracking-wider mb-1">Email Surel</h4>
                        <a href="mailto:info@dayama.web.id" class="text-secondary hover:underline font-medium block">info@dayama.web.id</a>
                        <a href="mailto:humas@dayama.web.id" class="text-muted-foreground hover:text-foreground transition-colors block">humas@dayama.web.id</a>
                    </div>
                </div>

                {{-- Jam Operasional --}}
                <div class="flex gap-4 items-start group">
                    <div class="w-12 h-12 rounded-full bg-muted flex items-center justify-center shrink-0 group-hover:bg-foreground group-hover:text-background transition-colors text-foreground">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-foreground text-sm uppercase tracking-wider mb-1">Jam Operasional Kantor</h4>
                        <p class="text-muted-foreground"><span class="font-medium text-foreground">Sabtu - Kamis:</span> 08.00 - 14.30 WITA</p>
                        <p class="text-red-500 font-medium mt-1">Jumat: Libur / Tutup</p>
                    </div>
                </div>
            </div>

            {{-- Map --}}
            <div class="h-64 rounded-lg overflow-hidden border border-border-subtle relative bg-muted">
                {{-- Google Maps iframe (Placeholder coordinate for Jerowaru) --}}
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15777.01234!2d116.4800!3d-8.7750!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zOMKwNDYnMzAuMCJTIDExNsKwMjgnNDguMCJF!5e0!3m2!1sen!2sid!4v1620000000000!5m2!1sen!2sid" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>

        {{-- Bagian Kanan: Form --}}
        <div>
            <div class="card p-8">
                <h3 class="text-2xl font-bold text-foreground mb-2">Kirim Pesan</h3>
                <p class="text-muted-foreground text-sm mb-6">Kami akan merespons pesan Anda ke alamat email yang Anda berikan dalam 1-2 hari kerja.</p>
                
                <form action="#" method="POST" class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="name" class="block text-sm font-medium text-foreground mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" required class="w-full px-4 py-2.5 bg-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-foreground placeholder-muted-foreground/60 transition-colors" placeholder="Fulan bin Fulan">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-foreground mb-1.5">No. HP / WhatsApp <span class="text-red-500">*</span></label>
                            <input type="tel" id="phone" name="phone" required class="w-full px-4 py-2.5 bg-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-foreground placeholder-muted-foreground/60 transition-colors" placeholder="081234567890">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-foreground mb-1.5">Email Aktif <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" required class="w-full px-4 py-2.5 bg-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-foreground placeholder-muted-foreground/60 transition-colors" placeholder="fulan@email.com">
                    </div>

                    <div>
                        <label for="subject" class="block text-sm font-medium text-foreground mb-1.5">Subjek / Perihal <span class="text-red-500">*</span></label>
                        <select id="subject" name="subject" class="w-full px-4 py-2.5 bg-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-foreground transition-colors appearance-none">
                            <option value="psb">Informasi Pendaftaran Santri Baru (PSB)</option>
                            <option value="kemitraan">Penawaran Kemitraan / Kerjasama Bisnis</option>
                            <option value="kunjungan">Permohonan Kunjungan / Studi Banding</option>
                            <option value="donasi">Konfirmasi Donasi / Wakaf</option>
                            <option value="lainnya">Lainnya / Pertanyaan Umum</option>
                        </select>
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-foreground mb-1.5">Isi Pesan <span class="text-red-500">*</span></label>
                        <textarea id="message" name="message" rows="5" required class="w-full px-4 py-2.5 bg-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-foreground placeholder-muted-foreground/60 transition-colors resize-y" placeholder="Tuliskan pesan Anda di sini..."></textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="btn btn-primary w-full py-3 justify-center text-base shadow-sm">Kirim Pesan Sekarang</button>
                    </div>
                </form>
            </div>
            
            {{-- Media Sosial --}}
            <div class="mt-8 text-center">
                <p class="text-sm text-muted-foreground font-medium mb-4">Atau terhubung dengan kami melalui platform resmi:</p>
                <div class="flex items-center justify-center gap-4">
                    <a href="#" class="w-10 h-10 rounded-full bg-surface border border-border-subtle flex items-center justify-center text-foreground hover:bg-primary hover:text-primary-foreground hover:border-primary transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-surface border border-border-subtle flex items-center justify-center text-foreground hover:bg-accent hover:text-accent-foreground hover:border-accent transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" /></svg>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-surface border border-border-subtle flex items-center justify-center text-foreground hover:bg-red-600 hover:text-white hover:border-red-600 transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M19.812 5.418c.861.23 1.538.907 1.768 1.768C21.998 8.746 22 12 22 12s0 3.255-.418 4.814a2.504 2.504 0 0 1-1.768 1.768c-1.56.419-7.814.419-7.814.419s-6.255 0-7.814-.419a2.505 2.505 0 0 1-1.768-1.768C2 15.255 2 12 2 12s0-3.255.417-4.814a2.507 2.507 0 0 1 1.768-1.768C5.744 5 11.998 5 11.998 5s6.255 0 7.814.418ZM15.194 12 10 15V9l5.194 3Z" clip-rule="evenodd" /></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
