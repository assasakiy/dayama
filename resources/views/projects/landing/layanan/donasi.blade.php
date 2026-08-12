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
                        <span class="ml-1 md:ml-2 font-medium text-white">{{ $title ?? 'Tabungan Akhirat Anda' }}</span>
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
                {{ $pageModel?->sections['hero']['subtitle'] ?? 'Mari wujudkan pesantren yang mandiri dan berdaya dengan menyisihkan sebagian rezeki untuk operasional pendidikan, beasiswa santri Yatim-Piatu, serta pembangunan asrama Dayama.' }}
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

    {{-- Program Donasi --}}
    <div class="mb-16">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Program 1 --}}
            <div class="card p-6 border-t-4 border-t-accent bg-surface flex flex-col h-full">
                <div class="w-12 h-12 rounded-full bg-accent/20 flex items-center justify-center text-accent mb-4">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h3 class="text-xl font-bold text-foreground mb-2">Beasiswa Yatim & Dhuafa</h3>
                <p class="text-muted-foreground text-sm mb-4 line-clamp-3 flex-grow">Membantu biaya pendidikan, asrama, dan kitab bagi santri yatim-piatu serta kurang mampu agar tetap bisa menimba ilmu.</p>
                <div class="w-full bg-border/50 rounded-full h-2 mb-2">
                    <div class="bg-accent h-2 rounded-full" style="width: 75%"></div>
                </div>
                <div class="text-xs font-bold text-foreground flex justify-between mb-4">
                    <span>Terkumpul: Rp 750 Jt</span>
                    <span>Target: Rp 1 M</span>
                </div>
                <a href="#cara-donasi" class="btn btn-outline w-full justify-center mt-auto">Donasi Sekarang</a>
            </div>

            {{-- Program 2 (Highlight) --}}
            <div class="card p-6 border-t-4 border-t-primary bg-surface relative overflow-hidden z-10 flex flex-col h-full md:-translate-y-2">
                <div class="absolute top-4 right-4">
                    <span class="relative flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-primary"></span>
                    </span>
                </div>
                <div class="w-12 h-12 rounded-full bg-primary/20 flex items-center justify-center text-primary mb-4">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="text-xl font-bold text-foreground mb-2">Wakaf Pembangunan Asrama</h3>
                <p class="text-muted-foreground text-sm mb-4 line-clamp-3 flex-grow">Pembangunan asrama putra 3 lantai untuk menampung lonjakan jumlah santri baru Dayama tahun ini.</p>
                <div class="w-full bg-border/50 rounded-full h-2 mb-2">
                    <div class="bg-primary h-2 rounded-full" style="width: 45%"></div>
                </div>
                <div class="text-xs font-bold text-foreground flex justify-between mb-4">
                    <span>Terkumpul: Rp 1.3 M</span>
                    <span>Target: Rp 3 M</span>
                </div>
                <a href="#cara-donasi" class="btn btn-primary w-full justify-center mt-auto">Wakaf Sekarang</a>
            </div>

            {{-- Program 3 --}}
            <div class="card p-6 border-t-4 border-t-secondary bg-surface flex flex-col h-full">
                <div class="w-12 h-12 rounded-full bg-secondary/20 flex items-center justify-center text-secondary mb-4">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                </div>
                <h3 class="text-xl font-bold text-foreground mb-2">Infaq Operasional Pesantren</h3>
                <p class="text-muted-foreground text-sm mb-4 line-clamp-3 flex-grow">Dana untuk menunjang kegiatan operasional harian, gaji pengajar honorer, listrik, kebersihan, dan pemeliharaan fasilitas pondok.</p>
                <div class="w-full bg-border/50 rounded-full h-2 mb-2">
                    <div class="bg-secondary h-2 rounded-full" style="width: 100%"></div>
                </div>
                <div class="text-xs font-bold text-foreground flex justify-between mb-4">
                    <span>Target Bulanan Terpenuhi</span>
                </div>
                <a href="#cara-donasi" class="btn btn-outline w-full justify-center mt-auto">Infaq Rutin Bulanan</a>
            </div>
        </div>
    </div>

    {{-- Rekening & QRIS --}}
    <div id="cara-donasi" class="mb-20">
        <div class="card p-8 lg:p-12 flex flex-col md:flex-row gap-10 bg-surface">
            <div class="w-full md:w-1/2">
                <h2 class="text-3xl font-bold text-foreground mb-4">Salurkan Donasi Anda</h2>
                <p class="text-muted-foreground mb-8">Pilih metode transfer antar bank atau gunakan QRIS untuk kemudahan transaksi dari e-wallet apapun (GoPay, OVO, Dana, ShopeePay).</p>
                
                <div class="space-y-4">
                    {{-- Rekening BSI --}}
                    <div class="p-4 rounded-xl border border-border-subtle hover:border-primary/50 transition-colors bg-background flex items-center gap-4 group">
                        <div class="w-16 h-12 bg-white rounded border border-border-subtle flex items-center justify-center p-1">
                            <span class="font-bold text-orange-500 text-sm">BSI</span>
                        </div>
                        <div class="flex-grow">
                            <div class="text-xs text-muted-foreground">Bank Syariah Indonesia</div>
                            <div class="font-bold text-foreground text-lg font-mono tracking-wider">7182 999 123</div>
                            <div class="text-xs text-foreground font-medium">a.n Yayasan Ponpes Dayama</div>
                        </div>
                        <button class="btn btn-ghost btn-icon" title="Salin Nomor Rekening">
                            <svg class="w-5 h-5 text-muted-foreground group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </div>

                    {{-- Rekening NTB Syariah --}}
                    <div class="p-4 rounded-xl border border-border-subtle hover:border-primary/50 transition-colors bg-background flex items-center gap-4 group">
                        <div class="w-16 h-12 bg-white rounded border border-border-subtle flex items-center justify-center p-1">
                            <span class="font-bold text-green-600 text-xs text-center leading-tight">NTB<br>Syariah</span>
                        </div>
                        <div class="flex-grow">
                            <div class="text-xs text-muted-foreground">Bank NTB Syariah</div>
                            <div class="font-bold text-foreground text-lg font-mono tracking-wider">012 02 00311 01 2</div>
                            <div class="text-xs text-foreground font-medium">a.n Ponpes Darul Yatama</div>
                        </div>
                        <button class="btn btn-ghost btn-icon" title="Salin Nomor Rekening">
                            <svg class="w-5 h-5 text-muted-foreground group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </div>
                </div>

                <div class="mt-8 p-4 rounded-xl bg-accent/10 border border-accent/20">
                    <h4 class="font-bold text-accent mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Konfirmasi Donasi
                    </h4>
                    <p class="text-sm text-muted-foreground mb-3">Setelah melakukan transfer, mohon konfirmasi agar donasi Anda tercatat rapi dan kami dapat mendoakan Anda secara khusus.</p>
                    <a href="https://wa.me/6281234567890" target="_blank" class="btn bg-green-500 text-white hover:bg-green-600 w-full justify-center font-bold">Konfirmasi via WhatsApp</a>
                </div>
            </div>

            <div class="w-full md:w-1/2 flex flex-col items-center justify-center">
                <div class="card p-6 border border-border-subtle max-w-sm w-full text-center bg-background">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg" alt="QRIS" class="h-8 mx-auto mb-4">
                    <h3 class="font-bold text-foreground text-sm mb-6">YAYASAN DARUL YATAMA WAL MASAKIN</h3>
                    <div class="aspect-square bg-surface-muted rounded-2xl mb-6 p-4 border-2 border-dashed border-border-strong flex items-center justify-center">
                        {{-- QR Placeholder --}}
                        <svg class="w-32 h-32 text-muted-foreground/30" viewBox="0 0 24 24" fill="currentColor"><path d="M3 3h8v8H3V3zm2 2v4h4V5H5zm8-2h8v8h-8V3zm2 2v4h4V5h-4zM3 13h8v8H3v-8zm2 2v4h4v-4H5zm13-2h3v2h-3v-2zm-3 0h2v2h-2v-2zm3 3h3v2h-3v-2zm-3 0h2v2h-2v-2zm3 3h3v2h-3v-2zm-3 0h2v2h-2v-2z"/></svg>
                    </div>
                    <p class="text-xs text-muted-foreground font-medium">Buka aplikasi mobile banking atau e-wallet Anda (Gopay, OVO, Dana, dll), pilih menu "Scan QR", dan arahkan kamera ke kode di atas.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Laporan Penyaluran (Transparansi) --}}
    <div class="mb-20">
        <div class="text-center mb-12">
            <h2 class="section-title">Transparansi Laporan Keuangan</h2>
            <p class="section-subtitle">Kepercayaan Anda adalah amanah terbesar kami. Berikut adalah laporan rutin penyaluran dana donasi umat.</p>
            <div class="section-accent-bar mx-auto"></div>
        </div>

        <div class="card p-0 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface border-b border-border-subtle">
                            <th class="p-4 text-sm font-bold text-foreground">Periode Laporan</th>
                            <th class="p-4 text-sm font-bold text-foreground">Total Penerimaan</th>
                            <th class="p-4 text-sm font-bold text-foreground">Total Penyaluran</th>
                            <th class="p-4 text-sm font-bold text-foreground">Saldo Akhir</th>
                            <th class="p-4 text-sm font-bold text-foreground text-center">Unduh PDF</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-subtle bg-background">
                        <tr class="hover:bg-surface-muted/50 transition-colors">
                            <td class="p-4 text-sm text-foreground font-medium">Laporan Semester I - 2026</td>
                            <td class="p-4 text-sm text-foreground">Rp 450.000.000</td>
                            <td class="p-4 text-sm text-foreground">Rp 420.000.000</td>
                            <td class="p-4 text-sm text-foreground">Rp 30.000.000</td>
                            <td class="p-4 text-center">
                                <a href="#" class="btn btn-ghost btn-icon mx-auto text-primary" title="Unduh">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </a>
                            </td>
                        </tr>
                        <tr class="hover:bg-surface-muted/50 transition-colors">
                            <td class="p-4 text-sm text-foreground font-medium">Laporan Semester II - 2025</td>
                            <td class="p-4 text-sm text-foreground">Rp 650.000.000</td>
                            <td class="p-4 text-sm text-foreground">Rp 650.000.000</td>
                            <td class="p-4 text-sm text-foreground">Rp 0</td>
                            <td class="p-4 text-center">
                                <a href="#" class="btn btn-ghost btn-icon mx-auto text-primary" title="Unduh">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-border-subtle bg-surface text-center">
                <a href="#" class="text-primary text-sm font-bold hover:underline">Lihat Arsip Laporan Lebih Lama</a>
            </div>
        </div>
    </div>
</div>
@endsection
