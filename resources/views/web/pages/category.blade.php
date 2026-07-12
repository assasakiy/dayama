@extends('web.layouts.app')

@section('title', ($category->seo_title ?? $category->name) . ' — ' . config('app.name'))
@section('description', $category->seo_description ?? $category->description)
@section('keywords', $category->meta_keywords)
@section('og_title', $category->seo_title ?? $category->name)

@section('content')
<section class="relative">
    @if($category->image_url)
    <div class="w-full h-48 md:h-64 relative overflow-hidden bg-surface-muted">
        <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="w-full h-full object-cover object-center opacity-80" />
        <div class="absolute inset-0 bg-gradient-to-t from-background to-transparent"></div>
    </div>
    @endif
    
    <div class="container-page {{ $category->image_url ? '-mt-16 md:-mt-24 relative z-10' : 'py-12' }}">
        <div class="mb-8 p-6 {{ $category->image_url ? 'bg-background/80 backdrop-blur-xl border border-border-subtle rounded-xl shadow-sm inline-block min-w-[50%]' : '' }}">
            <a href="{{ route('blog.index') }}" class="text-sm text-muted-foreground hover:text-foreground transition-colors">&larr; {{ __('Back to Archive') }}</a>
            <h1 class="text-2xl md:text-4xl font-bold tracking-tight mt-3 flex items-center gap-3">
                @if($category->icon)
                    <span class="text-3xl shrink-0" style="color: {{ $category->color }}">{!! $category->icon !!}</span>
                @elseif($category->color)
                    <span class="w-4 h-4 rounded-full shrink-0" style="background-color: {{ $category->color }}"></span>
                @endif
                {{ $category->title ?? $category->name }}
            </h1>
            
            <div class="flex items-center gap-3 mt-4">
                <span class="text-sm font-medium px-2.5 py-1 rounded-md" style="background-color: {{ $category->color ? $category->color.'20' : 'var(--surface-muted)' }}; color: {{ $category->color ?? 'var(--foreground)' }}">
                    {{ $posts->total() }} {{ $posts->total() === 1 ? 'Article' : 'Articles' }}
                </span>
            </div>

            @if($category->description)
            <p class="text-muted-foreground mt-4 max-w-2xl text-base md:text-lg">{{ $category->description }}</p>
            @endif
        </div>
    </div>
</section>

<section class="container-page pb-12">
    @if($posts->isEmpty())
    <p class="text-muted-foreground py-16 text-center border border-dashed border-border-subtle rounded-xl">{{ __('No articles in this category yet.') }}</p>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($posts as $post)
        <x-web.partials.post-card :post="$post" :current-category="$category" />
        @endforeach
    </div>
    <div class="mt-10">{{ $posts->links() }}</div>
    @endif
</section>
@endsection
