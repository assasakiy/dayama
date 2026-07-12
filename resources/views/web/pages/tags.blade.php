@extends('web.layouts.app')

@section('title', __('Tags') . ' — ' . config('app.name'))
@section('description', __('Browse all tags.'))
@section('og_title', __('Tags') . ' — ' . config('app.name'))

@section('content')
<section class="container-page py-12">
    <div class="mb-10">
        <a href="{{ route('blog.index') }}" class="text-sm text-muted-foreground hover:text-foreground transition-colors">&larr; {{ __('Back to Archive') }}</a>
        <h1 class="text-2xl md:text-3xl font-bold tracking-tight mt-2">{{ __('Tags') }}</h1>
        <p class="text-muted-foreground mt-1">{{ __('Browse all tags.') }}</p>
    </div>

    @if($tags->isEmpty())
    <p class="text-muted-foreground py-16 text-center">{{ __('No tags yet.') }}</p>
    @else
    <div class="flex flex-wrap gap-3">
        @foreach($tags as $tag)
        <a href="{{ route('tag.show', $tag) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-surface-muted rounded-md text-sm font-medium hover:bg-primary hover:text-primary-foreground transition-colors border border-border-subtle group">
            <span class="text-muted-foreground group-hover:text-primary-foreground/70 transition-colors">#</span>
            {{ $tag->name }}
            <span class="text-xs bg-background/50 px-1.5 py-0.5 rounded text-muted-foreground group-hover:text-primary-foreground group-hover:bg-primary-foreground/20 ml-1">{{ $tag->posts_count }}</span>
        </a>
        @endforeach
    </div>
    @endif
</section>
@endsection
