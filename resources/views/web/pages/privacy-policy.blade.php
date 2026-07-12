@extends('web.layouts.app')
@section('title', __('Privacy Policy') . ' — ' . config('app.name'))
@section('robots', 'index, follow')
@section('content')
<section class="container-page py-12">
    <div class="max-w-[720px] mx-auto prose-blog">
        <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-6">{{ __('Privacy Policy') }}</h1>
        <p>{{ __('This Privacy Policy explains how :app collects, uses, and protects your personal data.', ['app' => config('app.name')]) }}</p>
        <h2>{{ __('Information We Collect') }}</h2>
        <p>{{ __('We collect information you provide directly, such as your name and email address when subscribing to our newsletter or leaving a comment.') }}</p>
        <h2>{{ __('How We Use Your Information') }}</h2>
        <p>{{ __('We use your information to improve our content, send newsletters if subscribed, and respond to inquiries. We never sell your data to third parties.') }}</p>
        <h2>{{ __('Cookies') }}</h2>
        <p>{{ __('We use minimal cookies for essential functionality and analytics. You can control cookie preferences in your browser settings.') }}</p>
        <p><em>{{ __('Last updated:') }} {{ date('F j, Y') }}</em></p>
    </div>
</section>
@endsection
