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
                        <span class="ml-1 md:ml-2 font-medium">Media & Informasi</span>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-white/60" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 md:ml-2 font-medium text-white">{{ $title ?? 'Kalender Kegiatan' }}</span>
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
                {{ $pageModel?->sections['hero']['subtitle'] ?? 'Jadwal acara, kajian, dan hari besar di lingkungan Dayama.' }}
            </p>
        </div>
    </div>
    
    <div class="absolute bottom-0 inset-x-0 pointer-events-none z-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto text-background">
            <path d="M0 80V40C240 0 480 0 720 40C960 80 1200 80 1440 40V80H0Z" fill="currentColor"/>
        </svg>
    </div>
</section>

<div class="container-page py-12 md:py-16" x-data="{ view: 'list' }">
    {{-- Toggle View & Filter --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        {{-- Navigation Bulan (Header Kalender) --}}
        <div class="flex items-center gap-4 bg-surface border border-border-subtle p-2 px-4 rounded-lg shadow-sm">
            <button class="btn btn-ghost btn-icon p-1 hover:bg-surface-muted rounded-md">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <h2 class="text-lg font-bold text-foreground">April 2026</h2>
            <button class="btn btn-ghost btn-icon p-1 hover:bg-surface-muted rounded-md">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="bg-surface rounded-lg p-1 border border-border-subtle flex">
                <button @click="view = 'list'" :class="view === 'list' ? 'bg-background shadow-sm text-foreground' : 'text-muted-foreground hover:text-foreground'" class="px-3 py-1.5 rounded-md text-sm font-medium flex items-center gap-2 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    List
                </button>
                <button @click="view = 'calendar'" :class="view === 'calendar' ? 'bg-background shadow-sm text-foreground' : 'text-muted-foreground hover:text-foreground'" class="px-3 py-1.5 rounded-md text-sm font-medium flex items-center gap-2 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Kalender
                </button>
            </div>
            
            <select class="px-4 py-2 border border-border-subtle rounded-lg bg-surface text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                <option>Semua Kategori</option>
                <option>Akademik</option>
                <option>Kajian Umum</option>
                <option>Hari Besar Islam</option>
                <option>Ekstrakurikuler</option>
            </select>
        </div>
    </div>

    {{-- LIST VIEW --}}
    <div x-show="view === 'list'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
        
        {{-- Agenda Item 1 --}}
        <div class="card p-0 overflow-hidden flex flex-col md:flex-row hover:shadow-md transition-shadow group">
            <div class="md:w-48 bg-primary/5 border-b md:border-b-0 md:border-r border-border-subtle p-6 flex flex-col items-center justify-center shrink-0">
                <span class="text-sm font-bold text-primary uppercase tracking-wider mb-1">Selasa</span>
                <span class="text-4xl font-black text-foreground leading-none mb-1 group-hover:text-primary transition-colors">14</span>
                <span class="text-sm text-muted-foreground font-medium">April 2026</span>
            </div>
            <div class="p-6 flex-grow flex flex-col justify-center">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="px-2 py-0.5 text-[10px] font-bold bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded uppercase tracking-wider">Kajian Umum</span>
                    <span class="px-2 py-0.5 text-[10px] font-bold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded uppercase tracking-wider flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Terbuka Untuk Umum
                    </span>
                </div>
                <h3 class="text-xl font-bold text-foreground mb-2">Tabligh Akbar & Doa Bersama Jelang Ujian Nasional</h3>
                <p class="text-sm text-muted-foreground mb-4">Pengajian akbar yang akan diisi oleh Tuan Guru dari Pancor. Diharapkan kehadiran seluruh wali santri kelas akhir.</p>
                
                <div class="flex flex-wrap gap-4 text-sm text-muted-foreground">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        08.00 - 11.30 WITA
                    </div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Masjid Jami' Dayama
                    </div>
                </div>
            </div>
        </div>

        {{-- Agenda Item 2 --}}
        <div class="card p-0 overflow-hidden flex flex-col md:flex-row hover:shadow-md transition-shadow group">
            <div class="md:w-48 bg-primary/5 border-b md:border-b-0 md:border-r border-border-subtle p-6 flex flex-col items-center justify-center shrink-0">
                <span class="text-sm font-bold text-primary uppercase tracking-wider mb-1">Senin-Rabu</span>
                <span class="text-4xl font-black text-foreground leading-none mb-1 group-hover:text-primary transition-colors">20-22</span>
                <span class="text-sm text-muted-foreground font-medium">April 2026</span>
            </div>
            <div class="p-6 flex-grow flex flex-col justify-center">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="px-2 py-0.5 text-[10px] font-bold bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded uppercase tracking-wider">Akademik</span>
                    <span class="px-2 py-0.5 text-[10px] font-bold bg-surface-muted text-muted-foreground border border-border-subtle rounded uppercase tracking-wider">Internal</span>
                </div>
                <h3 class="text-xl font-bold text-foreground mb-2">Ujian Akhir Madrasah Berstandar Nasional (UAMBN)</h3>
                <p class="text-sm text-muted-foreground mb-4">Pelaksanaan ujian serentak untuk santri kelas akhir tingkat MTs dan MA. Mohon doa restu dari seluruh pihak.</p>
                
                <div class="flex flex-wrap gap-4 text-sm text-muted-foreground">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        07.30 - Selesai
                    </div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Gedung MA & MTs Dayama
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-8 text-center">
            <button class="btn btn-outline">Muat Bulan Selanjutnya</button>
        </div>
    </div>

    {{-- CALENDAR VIEW (Static representation of a month grid) --}}
    <div x-show="view === 'calendar'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="card p-0 overflow-hidden">
            {{-- Days of week header --}}
            <div class="grid grid-cols-7 border-b border-border-subtle bg-surface-muted/50">
                <div class="p-2 md:p-3 text-center text-xs md:text-sm font-bold text-red-500">Ahad</div>
                <div class="p-2 md:p-3 text-center text-xs md:text-sm font-bold text-foreground">Senin</div>
                <div class="p-2 md:p-3 text-center text-xs md:text-sm font-bold text-foreground">Selasa</div>
                <div class="p-2 md:p-3 text-center text-xs md:text-sm font-bold text-foreground">Rabu</div>
                <div class="p-2 md:p-3 text-center text-xs md:text-sm font-bold text-foreground">Kamis</div>
                <div class="p-2 md:p-3 text-center text-xs md:text-sm font-bold text-primary">Jumat</div>
                <div class="p-2 md:p-3 text-center text-xs md:text-sm font-bold text-foreground">Sabtu</div>
            </div>
            
            {{-- Calendar Grid (Sample data for April 2026) --}}
            <div class="grid grid-cols-7 border-l border-t border-border-subtle bg-surface">
                {{-- Row 1 --}}
                <div class="min-h-[100px] md:min-h-[120px] p-2 border-r border-b border-border-subtle bg-background/50 text-muted-foreground/50">
                    <div class="font-medium text-sm">29</div>
                </div>
                <div class="min-h-[100px] md:min-h-[120px] p-2 border-r border-b border-border-subtle bg-background/50 text-muted-foreground/50">
                    <div class="font-medium text-sm">30</div>
                </div>
                <div class="min-h-[100px] md:min-h-[120px] p-2 border-r border-b border-border-subtle bg-background/50 text-muted-foreground/50">
                    <div class="font-medium text-sm">31</div>
                </div>
                <div class="min-h-[100px] md:min-h-[120px] p-2 border-r border-b border-border-subtle">
                    <div class="font-medium text-sm">1</div>
                </div>
                <div class="min-h-[100px] md:min-h-[120px] p-2 border-r border-b border-border-subtle">
                    <div class="font-medium text-sm">2</div>
                </div>
                <div class="min-h-[100px] md:min-h-[120px] p-2 border-r border-b border-border-subtle text-primary">
                    <div class="font-bold text-sm">3</div>
                </div>
                <div class="min-h-[100px] md:min-h-[120px] p-2 border-r border-b border-border-subtle">
                    <div class="font-medium text-sm">4</div>
                </div>
                
                {{-- Row 2 --}}
                <div class="min-h-[100px] md:min-h-[120px] p-2 border-r border-b border-border-subtle text-red-500">
                    <div class="font-bold text-sm">5</div>
                </div>
                <div class="min-h-[100px] md:min-h-[120px] p-2 border-r border-b border-border-subtle">
                    <div class="font-medium text-sm">6</div>
                </div>
                <div class="min-h-[100px] md:min-h-[120px] p-2 border-r border-b border-border-subtle">
                    <div class="font-medium text-sm">7</div>
                </div>
                <div class="min-h-[100px] md:min-h-[120px] p-2 border-r border-b border-border-subtle">
                    <div class="font-medium text-sm">8</div>
                </div>
                <div class="min-h-[100px] md:min-h-[120px] p-2 border-r border-b border-border-subtle">
                    <div class="font-medium text-sm">9</div>
                </div>
                <div class="min-h-[100px] md:min-h-[120px] p-2 border-r border-b border-border-subtle text-primary">
                    <div class="font-bold text-sm">10</div>
                </div>
                <div class="min-h-[100px] md:min-h-[120px] p-2 border-r border-b border-border-subtle">
                    <div class="font-medium text-sm">11</div>
                </div>
                
                {{-- Row 3 (With Events) --}}
                <div class="min-h-[100px] md:min-h-[120px] p-2 border-r border-b border-border-subtle text-red-500 bg-red-50/10">
                    <div class="font-bold text-sm mb-1">12</div>
                </div>
                <div class="min-h-[100px] md:min-h-[120px] p-2 border-r border-b border-border-subtle">
                    <div class="font-medium text-sm mb-1">13</div>
                </div>
                <div class="min-h-[100px] md:min-h-[120px] p-2 border-r border-b border-border-subtle bg-blue-50/30">
                    <div class="font-medium text-sm mb-1">14</div>
                    <div class="bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 text-[10px] p-1 rounded font-bold leading-tight line-clamp-2 cursor-pointer hover:bg-blue-200 transition-colors" title="Tabligh Akbar">
                        08.00 Tabligh Akbar
                    </div>
                </div>
                <div class="min-h-[100px] md:min-h-[120px] p-2 border-r border-b border-border-subtle">
                    <div class="font-medium text-sm mb-1">15</div>
                </div>
                <div class="min-h-[100px] md:min-h-[120px] p-2 border-r border-b border-border-subtle">
                    <div class="font-medium text-sm mb-1">16</div>
                </div>
                <div class="min-h-[100px] md:min-h-[120px] p-2 border-r border-b border-border-subtle text-primary">
                    <div class="font-bold text-sm mb-1">17</div>
                </div>
                <div class="min-h-[100px] md:min-h-[120px] p-2 border-r border-b border-border-subtle bg-primary/5">
                    <div class="flex items-center justify-between mb-1">
                        <div class="font-medium text-sm">18</div>
                        <div class="w-1.5 h-1.5 rounded-full bg-primary"></div>
                    </div>
                    <div class="bg-primary text-primary-foreground text-[10px] p-1 rounded font-bold leading-tight line-clamp-2 cursor-pointer hover:bg-primary/90 transition-colors">
                        Hari Santri
                    </div>
                </div>
                
                {{-- Row 4 (Multi-day event) --}}
                <div class="min-h-[100px] md:min-h-[120px] p-2 border-r border-b border-border-subtle text-red-500">
                    <div class="font-bold text-sm mb-1">19</div>
                </div>
                <div class="min-h-[100px] md:min-h-[120px] p-2 border-r border-b border-border-subtle bg-purple-50/30">
                    <div class="font-medium text-sm mb-1">20</div>
                    <div class="bg-purple-100 dark:bg-purple-900/50 border-l-2 border-purple-500 text-purple-700 dark:text-purple-300 text-[10px] p-1 rounded-r font-bold leading-tight whitespace-nowrap overflow-hidden text-ellipsis relative z-10 -mr-3">
                        UAMBN MTs & MA
                    </div>
                </div>
                <div class="min-h-[100px] md:min-h-[120px] p-2 border-r border-b border-border-subtle bg-purple-50/30 overflow-visible relative">
                    <div class="font-medium text-sm mb-1">21</div>
                    <div class="bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300 text-[10px] p-1 h-5 absolute w-full left-0 z-0"></div>
                </div>
                <div class="min-h-[100px] md:min-h-[120px] p-2 border-r border-b border-border-subtle bg-purple-50/30">
                    <div class="font-medium text-sm mb-1">22</div>
                    <div class="bg-purple-100 dark:bg-purple-900/50 border-r-2 border-purple-500 text-transparent text-[10px] p-1 rounded-l relative z-10 -ml-3">
                        -
                    </div>
                </div>
                <div class="min-h-[100px] md:min-h-[120px] p-2 border-r border-b border-border-subtle">
                    <div class="font-medium text-sm mb-1">23</div>
                </div>
                <div class="min-h-[100px] md:min-h-[120px] p-2 border-r border-b border-border-subtle text-primary">
                    <div class="font-bold text-sm mb-1">24</div>
                </div>
                <div class="min-h-[100px] md:min-h-[120px] p-2 border-r border-b border-border-subtle">
                    <div class="font-medium text-sm mb-1">25</div>
                </div>
            </div>
        </div>
        
        <div class="mt-4 flex gap-4 text-xs font-medium text-white/80 justify-center">
            <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Kajian Umum</div>
            <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-purple-500"></span> Akademik</div>
            <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-primary"></span> Hari Besar</div>
        </div>
    </div>
</div>
@endsection
