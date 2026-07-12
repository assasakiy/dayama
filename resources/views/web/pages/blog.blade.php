@extends('web.layouts.app')

@section('title', __('Post Archive') . ' — ' . config('app.name'))
@section('description', __('Browse all articles, tutorials, and stories.'))
@section('og_title', __('Post Archive') . ' — ' . config('app.name'))

@section('content')
<section class="container-page py-12">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight">{{ __('Post Archive') }}</h1>
            <p class="text-muted-foreground mt-1">{{ __('All articles, tutorials, and stories.') }}</p>
        </div>
        @if(request('q'))
        <a href="{{ route('blog.index') }}" class="btn btn-ghost text-sm">{{ __('Clear filter') }}</a>
        @endif
    </div>

    @if($posts->isEmpty())
    <div class="text-center py-16">
        <p class="text-muted-foreground">{{ __('No articles found.') }}</p>
    </div>
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
