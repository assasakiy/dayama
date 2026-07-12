@extends('web.layouts.clean')

@section('title', 'About - ' . ($siteSettings['site_name'] ?? 'Modern Blog'))
@section('description', 'Learn more about our mission and team.')
@section('og_type', 'website')

@section('page-content')
    <h1 class="text-3xl md:text-4xl font-bold text-balance mb-6">About us</h1>
    <div class="prose-blog max-w-none">
        <p>We're a team of writers, developers, and designers passionate about sharing knowledge and building in public.</p>
        <p>Our mission is to create high-quality content that helps developers and designers build better products. We believe in clear writing, thoughtful design, and practical insights.</p>
        <h2>Our values</h2>
        <ul>
            <li><strong>Quality over quantity</strong> — Every article is carefully researched and edited.</li>
            <li><strong>Practical insights</strong> — Real-world advice you can apply today.</li>
            <li><strong>Community first</strong> — We grow and learn together with our readers.</li>
            <li><strong>Transparency</strong> — We write openly about what we build and how we build it.</li>
        </ul>
        <h2>Get in touch</h2>
        <p>Have a question, suggestion, or want to contribute? <a href="{{ route('contact') }}">We'd love to hear from you</a>.</p>
    </div>
@endsection
