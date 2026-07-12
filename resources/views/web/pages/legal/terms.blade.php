@extends('web.layouts.clean')

@section('title', 'Terms of Service - ' . ($siteSettings['site_name'] ?? 'Modern Blog'))
@section('description', 'Our terms of service govern the use of our website and content.')
@section('robots', 'index, follow')

@section('page-content')
    <h1 class="text-3xl md:text-4xl font-bold text-balance mb-6">Terms of Service</h1>
    <div class="prose-blog max-w-none">
        <p>Last updated: January 1, 2026</p>
        <h2>1. Acceptance of Terms</h2>
        <p>By accessing and using {{ $siteSettings['site_name'] ?? 'Modern Blog' }}, you agree to these terms of service.</p>
        <h2>2. Content</h2>
        <p>All content published on this site is for informational purposes only. We strive for accuracy but make no guarantees.</p>
        <h2>3. User Conduct</h2>
        <p>You agree not to post abusive, defamatory, or illegal content. We reserve the right to moderate comments and remove content at our discretion.</p>
        <h2>4. Intellectual Property</h2>
        <p>All content on this site is owned by {{ $siteSettings['site_name'] ?? 'Modern Blog' }} unless otherwise noted. You may share with proper attribution.</p>
        <h2>5. Limitation of Liability</h2>
        <p>We are not liable for any damages arising from the use of this site or its content.</p>
        <h2>6. Changes</h2>
        <p>We reserve the right to update these terms at any time. Continued use constitutes acceptance of changes.</p>
        <h2>7. Contact</h2>
        <p>For questions about these terms, <a href="{{ route('contact') }}">contact us</a>.</p>
    </div>
@endsection
