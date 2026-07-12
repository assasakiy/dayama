@extends('web.layouts.app')

@section('title', $tag->name . ' — ' . config('app.name'))
@section('description', $tag->description ?? __('Articles tagged with :tag', ['tag' => $tag->name]))

@section('content')
<section class="container-page py-12">
    <div class="mb-8">
        <a href="{{ route('blog.index') }}" class="text-sm text-muted-foreground hover:text-foreground transition-colors">&larr; {{ __('Back to Archive') }}</a>
        <h1 class="text-2xl md:text-3xl font-bold tracking-tight mt-2 flex items-center gap-3">
            #{{ $tag->name }}
            <span class="text-sm font-normal text-muted-foreground bg-surface-muted px-2 py-0.5 rounded-full">{{ $posts->total() }} {{ $posts->total() === 1 ? 'Article' : 'Articles' }}</span>
        </h1>
        @if($tag->description)
        <p class="text-muted-foreground mt-1">{{ $tag->description }}</p>
        @endif
    </div>

    @if($posts->isEmpty())
    <p class="text-muted-foreground py-16 text-center">{{ __('No articles with this tag yet.') }}</p>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($posts as $post)
        <x-web.partials.post-card :post="$post" />
        @endforeach
    </div>
    <div class="mt-10">{{ $posts->links() }}</div>
    @endif
</section>
@endsection
