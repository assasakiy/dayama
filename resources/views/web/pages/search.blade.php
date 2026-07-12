@extends('web.layouts.app')

@section('title', __('Search') . ' — ' . config('app.name'))
@section('description', __('Search articles'))

@section('content')
<section class="container-page py-12">
    <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-6">{{ __('Search Articles') }}</h1>

    <form action="{{ route('search') }}" method="GET" class="mb-8" role="search">
        <div class="flex gap-2 max-w-lg">
            <label for="search-q" class="sr-only">{{ __('Search') }}</label>
            <input id="search-q" type="search" name="q" value="{{ $query }}" placeholder="{{ __('Search articles...') }}"
                   class="flex-1 h-10 px-3 text-sm bg-surface border border-border-subtle rounded-sm outline-none focus:border-primary transition-colors" autofocus>
            <button type="submit" class="btn btn-primary">{{ __('Search') }}</button>
        </div>
    </form>

    @if(strlen($query) > 0)
        @if($posts->isEmpty())
        <p class="text-muted-foreground py-8">{{ __('No results found for') }} "{{ $query }}".</p>
        @else
        <p class="text-sm text-muted-foreground mb-6">{{ __('Found :count result(s) for', ['count' => $posts->total()]) }} "{{ $query }}".</p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($posts as $post)
            <x-web.partials.post-card :post="$post" />
            @endforeach
        </div>
        <div class="mt-10">{{ $posts->withQueryString()->links() }}</div>
        @endif
    @endif
</section>
@endsection
