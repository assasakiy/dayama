@extends('web.layouts.app')

@section('title', __('Trending') . ' — ' . config('app.name'))
@section('description', __('Most popular and trending articles.'))
@section('og_title', __('Trending') . ' — ' . config('app.name'))

@section('content')
<section class="container-page py-12">
    <div class="mb-8">
        <a href="{{ route('blog.index') }}" class="text-sm text-muted-foreground hover:text-foreground transition-colors">&larr; {{ __('Back to Post Archive') }}</a>
        <h1 class="text-2xl md:text-3xl font-bold tracking-tight mt-2">{{ __('Trending') }}</h1>
        <p class="text-muted-foreground mt-1">{{ __('Most popular articles right now.') }}</p>
    </div>

    @if($posts->isEmpty())
    <p class="text-muted-foreground py-16 text-center">{{ __('No articles yet.') }}</p>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($posts as $post)
        <x-web.partials.post-card :post="$post" />
        @endforeach
    </div>

    <div class="mt-10">
        {{ $posts->links() }}
    </div>
    @endif
</section>
@endsection