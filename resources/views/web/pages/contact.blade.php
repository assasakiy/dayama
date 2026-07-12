@extends('web.layouts.app')
@section('title', __('Contact') . ' — ' . config('app.name'))
@section('content')
<section class="container-page py-12">
    <div class="max-w-[600px] mx-auto">
        <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-6">{{ __('Contact Us') }}</h1>
        <form action="#" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium mb-1">{{ __('Name') }}</label>
                <input id="name" type="text" name="name" required class="w-full h-10 px-3 text-sm bg-surface border border-border-subtle rounded-sm outline-none focus:border-primary transition-colors">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium mb-1">{{ __('Email') }}</label>
                <input id="email" type="email" name="email" required class="w-full h-10 px-3 text-sm bg-surface border border-border-subtle rounded-sm outline-none focus:border-primary transition-colors">
            </div>
            <div>
                <label for="message" class="block text-sm font-medium mb-1">{{ __('Message') }}</label>
                <textarea id="message" name="message" rows="5" required class="w-full px-3 py-2 text-sm bg-surface border border-border-subtle rounded-sm outline-none focus:border-primary transition-colors resize-y"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">{{ __('Send Message') }}</button>
        </form>
    </div>
</section>
@endsection
