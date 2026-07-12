@extends('web.layouts.app')

@section('title', __('Categories') . ' — ' . config('app.name'))
@section('description', __('Browse all categories and topics.'))
@section('og_title', __('Categories') . ' — ' . config('app.name'))

@section('content')
<section class="container-page py-12">
    <div class="mb-10 text-center md:text-left">
        <a href="{{ route('blog.index') }}" class="text-sm text-muted-foreground hover:text-foreground transition-colors">&larr; {{ __('Back to Archive') }}</a>
        <h1 class="text-3xl md:text-4xl font-bold tracking-tight mt-2">{{ __('Categories') }}</h1>
        <p class="text-muted-foreground mt-2 text-lg">{{ __('Browse all categories and topics.') }}</p>
    </div>

    @if($categories->isEmpty())
    <p class="text-muted-foreground py-16 text-center border border-dashed border-border-subtle rounded-xl">{{ __('No categories yet.') }}</p>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($categories as $category)
        <a href="{{ route('category.show', $category) }}" class="group relative overflow-hidden rounded-xl border border-border-subtle bg-card hover:border-primary-border hover:shadow-elevated transition-all flex flex-col h-full">
            @if($category->image_url)
            <div class="h-32 w-full overflow-hidden bg-surface-muted relative">
                <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
            </div>
            @else
            <div class="h-24 w-full" style="background-color: {{ $category->color ? $category->color.'15' : 'var(--surface-muted)' }};"></div>
            @endif

            <div class="p-5 flex-1 flex flex-col relative z-10 {{ $category->image_url ? '-mt-10' : '-mt-12' }}">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 mb-3 border-2 border-background shadow-sm" style="background-color: {{ $category->color ?? 'var(--surface-muted)' }}; color: white;">
                    @if($category->icon)
                        <span class="text-xl leading-none">{!! $category->icon !!}</span>
                    @else
                        <span class="text-xl font-bold leading-none">{{ substr($category->name, 0, 1) }}</span>
                    @endif
                </div>
                
                <h2 class="text-lg font-semibold group-hover:text-primary transition-colors line-clamp-1 {{ $category->image_url ? 'text-foreground' : '' }}">{{ $category->name }}</h2>
                
                @if($category->description)
                <p class="text-sm text-muted-foreground mt-2 line-clamp-2 flex-1">{{ $category->description }}</p>
                @endif
                
                <p class="text-xs font-medium mt-4 pt-4 border-t border-border-subtle/50 text-muted-foreground group-hover:text-foreground transition-colors">
                    {{ $category->posts_count }} {{ $category->posts_count === 1 ? __('Article') : __('Articles') }} &rarr;
                </p>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</section>
@endsection