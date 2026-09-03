@extends('web.layouts.app')

@section('title', 'Struktur Organisasi — Pondok Pesantren Darul Yatama Wal Masakin')
@section('description', 'Struktur organisasi dan kepengurusan Pondok Pesantren Darul Yatama Wal Masakin.')

@php
    $landingDomain = config('platform.sites.landing.domain', env('APP_ROOT_DOMAIN', 'dayama.test'));
    $landingUrl = 'http://' . $landingDomain;
@endphp

@section('content')

{{-- HERO --}}
{{-- HERO --}}
<section class="hero-subpage py-16 md:py-20 pb-24 md:pb-32 relative z-0">
    <div class="container-page relative z-10">
        <nav class="flex text-sm text-white/80 mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('landing.home') ?? '/' }}" class="inline-flex items-center hover:text-white transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        Beranda
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-white/60" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 md:ml-2 font-medium">Profil</span>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-white/60" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 md:ml-2 font-medium text-white">{{ $title ?? 'Struktur Organisasi' }}</span>
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
                {{ $pageModel?->sections['hero']['subtitle'] ?? 'Kepengurusan dan struktur organisasi Pondok Pesantren Dayama.' }}
            </p>
        </div>
    </div>
    
    <div class="absolute bottom-0 inset-x-0 pointer-events-none z-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto text-background">
            <path d="M0 80V40C240 0 480 0 720 40C960 80 1200 80 1440 40V80H0Z" fill="currentColor"/>
        </svg>
    </div>
</section>

{{-- ORG CHART --}}
<section class="py-12 md:py-20">
    <div class="container-page">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="badge badge-primary mb-4">Organisasi</span>
            <h2 class="section-title">Bagan Organisasi</h2>
            <p class="section-subtitle mt-3">Struktur kepengurusan yang menjalankan roda organisasi pesantren.</p>
            <div class="section-accent-bar mx-auto"></div>
        </div>

        {{-- Simplified Org Chart --}}
        <div class="max-w-4xl mx-auto">
            {{-- Top Level: Ketua Yayasan --}}
            <div class="flex justify-center mb-6">
                <div class="profile-card w-64 text-center">
                    <div class="bg-primary/5 py-6 flex justify-center">
                        <div class="w-20 h-20 rounded-full bg-surface-muted border-2 border-primary/20 flex items-center justify-center">
                            <svg class="w-10 h-10 text-primary/30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                    </div>
                    <div class="p-4">
                        <span class="badge badge-accent text-[10px] mb-2">Ketua Yayasan</span>
                        <h3 class="font-semibold text-foreground">Nama Ketua Yayasan</h3>
                        <p class="text-xs text-muted-foreground/60 mt-1">(Placeholder)</p>
                    </div>
                </div>
            </div>

            {{-- Connector --}}
            <div class="flex justify-center mb-6">
                <div class="w-px h-8 bg-border-strong"></div>
            </div>

            {{-- Second Level: Pimpinan Pesantren --}}
            <div class="flex justify-center mb-6">
                <div class="profile-card w-64 text-center">
                    <div class="bg-primary/5 py-6 flex justify-center">
                        <div class="w-20 h-20 rounded-full bg-surface-muted border-2 border-primary/20 flex items-center justify-center">
                            <svg class="w-10 h-10 text-primary/30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                    </div>
                    <div class="p-4">
                        <span class="badge badge-primary text-[10px] mb-2">Pimpinan Pesantren</span>
                        <h3 class="font-semibold text-foreground">Nama Pimpinan</h3>
                        <p class="text-xs text-muted-foreground/60 mt-1">(Placeholder)</p>
                    </div>
                </div>
            </div>

            {{-- Connector --}}
            <div class="flex justify-center mb-6">
                <div class="w-px h-8 bg-border-strong"></div>
            </div>

            {{-- Third Level: Bidang --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @php
                    $bidang = [
                        ['jabatan' => 'Kepala Madrasah', 'nama' => 'Nama Kepala Madrasah'],
                        ['jabatan' => 'Kepala Asrama', 'nama' => 'Nama Kepala Asrama'],
                        ['jabatan' => 'Bidang Kurikulum', 'nama' => 'Nama Wakil Kurikulum'],
                        ['jabatan' => 'Bidang Kesiswaan', 'nama' => 'Nama Wakil Kesiswaan'],
                    ];
                @endphp
                @foreach($bidang as $item)
                <div class="profile-card text-center">
                    <div class="bg-surface-muted py-4 flex justify-center">
                        <div class="w-14 h-14 rounded-full bg-background border border-border-subtle flex items-center justify-center">
                            <svg class="w-7 h-7 text-primary/30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                    </div>
                    <div class="p-3">
                        <span class="text-[10px] font-semibold text-primary uppercase tracking-wider">{{ $item['jabatan'] }}</span>
                        <h3 class="font-semibold text-foreground text-sm mt-1">{{ $item['nama'] }}</h3>
                        <p class="text-xs text-muted-foreground/60 mt-1">(Placeholder)</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- DAFTAR PENGURUS --}}
<section class="py-12 md:py-20 bg-surface-muted/50 islamic-pattern-bg">
    <div class="container-page">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="badge badge-primary mb-4">Pengurus</span>
            <h2 class="section-title">Daftar Pengurus Lengkap</h2>
            <div class="section-accent-bar mx-auto"></div>
        </div>

        <div class="max-w-4xl mx-auto">
            <div class="bg-background border border-border-subtle rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-surface-muted">
                            <th class="px-4 py-3 text-left font-semibold text-foreground">No</th>
                            <th class="px-4 py-3 text-left font-semibold text-foreground">Nama</th>
                            <th class="px-4 py-3 text-left font-semibold text-foreground">Jabatan</th>
                            <th class="px-4 py-3 text-left font-semibold text-foreground hidden md:table-cell">Masa Jabatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $pengurus = [
                                ['Nama Ketua Yayasan', 'Ketua Yayasan', '2020 — Sekarang'],
                                ['Nama Sekretaris', 'Sekretaris Yayasan', '2020 — Sekarang'],
                                ['Nama Bendahara', 'Bendahara Yayasan', '2020 — Sekarang'],
                                ['Nama Pimpinan', 'Pimpinan Pesantren', '2020 — Sekarang'],
                                ['Nama Kepala Madrasah', 'Kepala Madrasah', '2021 — Sekarang'],
                                ['Nama Kepala Asrama', 'Kepala Asrama', '2021 — Sekarang'],
                                ['Nama Wakil Kurikulum', 'Wakil Bidang Kurikulum', '2022 — Sekarang'],
                                ['Nama Wakil Kesiswaan', 'Wakil Bidang Kesiswaan', '2022 — Sekarang'],
                            ];
                        @endphp
                        @foreach($pengurus as $index => $item)
                        <tr class="border-t border-border-subtle hover:bg-surface-muted/50 transition-colors">
                            <td class="px-4 py-3 text-muted-foreground">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-foreground">{{ $item[0] }}</td>
                            <td class="px-4 py-3 text-muted-foreground">{{ $item[1] }}</td>
                            <td class="px-4 py-3 text-muted-foreground hidden md:table-cell">{{ $item[2] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-xs text-muted-foreground/60 text-center mt-4">(Placeholder — data akan diperbarui dengan nama pengurus yang sebenarnya)</p>
        </div>
    </div>
</section>

@endsection
