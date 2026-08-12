@extends('web.layouts.app')

@section('content')
<div class="relative bg-background min-h-screen">
    {{-- Hero Section --}}
<section class="hero-subpage py-16 md:py-20 pb-24 md:pb-32 relative z-0" style="{{ $institution->cover_url ? 'background-image: url('.$institution->cover_url.'); background-size: cover; background-position: center;' : '' }}">
    @if($institution->cover_url)
    <div class="absolute inset-0 bg-primary/80 mix-blend-multiply z-0"></div>
    @endif
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
                        <span class="ml-1 md:ml-2 font-medium text-white">{{ $section ?? 'Pendidikan' }}</span>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-white/60" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 md:ml-2 font-medium text-white">{{ $title ?? $institution->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <div class="max-w-4xl flex items-center gap-6">
            @if($institution->logo_url)
                <img src="{{ $institution->logo_url }}" alt="Logo {{ $institution->name }}" class="w-24 h-24 md:w-32 md:h-32 object-contain bg-white rounded-xl p-2 hidden sm:block shadow-lg">
            @endif
            <div>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black tracking-tight text-white mb-4 leading-tight">
                {!! $pageModel?->sections['hero']['title'] ?? $title ?? '' !!}
                @if(!empty($pageModel?->sections['hero']['highlight']))
                <span class="block text-primary">{!! $pageModel->sections['hero']['highlight'] !!}</span>
                @endif
            </h1>
            <p class="text-lg md:text-xl text-white/90 leading-relaxed max-w-3xl">
                {{ $pageModel?->sections['hero']['subtitle'] ?? ($institution->short_description ?? 'Pendidikan yang berfokus pada penguasaan ilmu agama dan sains modern.') }}
            </p>
            </div>
        </div>
    </div>
    
    <div class="absolute bottom-0 inset-x-0 pointer-events-none z-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto text-background">
            <path d="M0 80V40C240 0 480 0 720 40C960 80 1200 80 1440 40V80H0Z" fill="currentColor"/>
        </svg>
    </div>
</section>

<div class="container-page py-12 md:py-20 relative z-10 -mt-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 md:gap-12">
        <div class="lg:col-span-2 space-y-8">
            @if($institution->content)
            <section class="card p-8">
                <h2 class="text-2xl font-bold text-primary mb-4 flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    Profil & Sistem Pendidikan
                </h2>
                <div class="prose-blog">
                    {!! $institution->content !!}
                </div>
            </section>
            @endif

            @if($institution->facilities && count($institution->facilities) > 0)
            <section class="card p-8">
                <h2 class="text-2xl font-bold text-primary mb-4 flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Fasilitas
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($institution->facilities as $facility)
                    <div class="flex items-start gap-3 p-4 bg-surface rounded-lg border border-border-subtle">
                        <div class="w-10 h-10 rounded bg-primary/10 text-primary flex items-center justify-center shrink-0">
                            <x-icon :icon="$facility['icon'] ?? 'check-circle'" class="w-5 h-5" />
                        </div>
                        <div>
                            <h4 class="font-bold text-foreground">{{ $facility['name'] }}</h4>
                            <p class="text-sm text-muted-foreground mt-1">{{ $facility['description'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            @if($institution->registration_url)
            <div class="card p-6 bg-surface">
                <h3 class="font-bold text-foreground mb-4 border-b border-border-subtle pb-2">Informasi Pendaftaran</h3>
                <p class="text-sm text-muted-foreground mb-4">Penerimaan Santri Baru (PSB) dibuka setiap tahun ajaran baru. Daftarkan putra/putri Anda secara online.</p>
                <a href="{{ url($institution->registration_url) }}" class="btn btn-primary w-full">Daftar Sekarang</a>
            </div>
            @endif

            @if($institution->extracurriculars && count($institution->extracurriculars) > 0)
            <div class="card p-6">
                <h3 class="font-bold text-foreground mb-4 border-b border-border-subtle pb-2">Kegiatan Ekstrakurikuler</h3>
                <ul class="space-y-3 text-sm text-foreground">
                    @foreach($institution->extracurriculars as $ekskul)
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $ekskul }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
