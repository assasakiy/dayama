@extends('web.layouts.app')
@section('title', __('Terms of Service') . ' — ' . config('app.name'))
@section('robots', 'index, follow')
@section('content')
<section class="container-page py-12">
    <div class="max-w-[720px] mx-auto prose-blog">
        <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-6">{{ __('Terms of Service') }}</h1>
        <p>{{ __('By using :app, you agree to these terms. If you do not agree, please do not use our service.', ['app' => config('app.name')]) }}</p>
        <h2>{{ __('Content') }}</h2>
        <p>{{ __('All content published on this site is for informational purposes only. We reserve the right to modify or remove content at any time.') }}</p>
        <h2>{{ __('Comments') }}</h2>
        <p>{{ __('You are responsible for the comments you post. We reserve the right to moderate, edit, or delete comments that violate our policies.') }}</p>
        <h2>{{ __('Changes') }}</h2>
        <p>{{ __('We may update these terms from time to time. Continued use of the site after changes constitutes acceptance.') }}</p>
        <p><em>{{ __('Last updated:') }} {{ date('F j, Y') }}</em></p>
    </div>
</section>
@endsection
