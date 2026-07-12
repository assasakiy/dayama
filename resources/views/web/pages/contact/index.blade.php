@extends('web.layouts.clean')

@section('title', 'Contact - ' . ($siteSettings['site_name'] ?? 'Modern Blog'))
@section('description', 'Get in touch with us.')
@section('og_type', 'website')

@section('page-content')
    <h1 class="text-3xl md:text-4xl font-bold text-balance mb-6">Contact us</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <div>
            <form x-data="{ name: '', email: '', subject: '', message: '', agreed: false, submitted: false, loading: false, error: '' }" x-on:submit.prevent="loading = true; error = ''; fetch('/contact', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }, body: JSON.stringify({ name, email, subject, message, privacy: agreed }) }).then(r => r.json()).then(d => { loading = false; if (d.success) { submitted = true; } else { error = d.message || 'Something went wrong.'; } }).catch(() => { loading = false; error = 'Something went wrong.'; })" class="space-y-4">
                <div x-show="submitted" x-cloak class="p-4 bg-success/10 border border-success/30 rounded-md text-sm text-foreground" role="alert">
                    Thank you for your message. We'll get back to you soon.
                </div>
                <template x-if="!submitted">
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="contact-name" class="block text-sm font-medium mb-1">Name <span class="text-danger">*</span></label>
                                <input id="contact-name" x-model="name" type="text" required class="w-full px-3 py-2 text-sm bg-surface border border-border-subtle rounded-sm focus:outline-none focus:border-primary">
                            </div>
                            <div>
                                <label for="contact-email" class="block text-sm font-medium mb-1">Email <span class="text-danger">*</span></label>
                                <input id="contact-email" x-model="email" type="email" required class="w-full px-3 py-2 text-sm bg-surface border border-border-subtle rounded-sm focus:outline-none focus:border-primary">
                            </div>
                        </div>
                        <div>
                            <label for="contact-subject" class="block text-sm font-medium mb-1">Subject <span class="text-danger">*</span></label>
                            <input id="contact-subject" x-model="subject" type="text" required class="w-full px-3 py-2 text-sm bg-surface border border-border-subtle rounded-sm focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label for="contact-message" class="block text-sm font-medium mb-1">Message <span class="text-danger">*</span></label>
                            <textarea id="contact-message" x-model="message" rows="5" required class="w-full px-3 py-2 text-sm bg-surface border border-border-subtle rounded-sm focus:outline-none focus:border-primary"></textarea>
                        </div>
                        <label class="flex items-start gap-2 text-sm text-muted-foreground">
                            <input x-model="agreed" type="checkbox" required class="mt-1 accent-primary">
                            <span>I agree to the <a href="{{ route('legal.privacy') }}" class="link">Privacy Policy</a> and consent to being contacted. <span class="text-danger">*</span></span>
                        </label>
                        <p x-show="error" x-cloak class="text-sm text-danger" x-text="error"></p>
                        <button type="submit" class="btn btn-primary" x-bind:disabled="loading || !name || !email || !subject || !message || !agreed">
                            <span x-show="loading">Sending...</span>
                            <span x-show="!loading">Send Message</span>
                        </button>
                    </div>
                </template>
            </form>
        </div>
        <div class="space-y-6">
            <div>
                <h2 class="text-sm font-semibold mb-2">Email</h2>
                <p class="text-sm text-muted-foreground">hello@modernblog.com</p>
            </div>
            <div>
                <h2 class="text-sm font-semibold mb-2">Follow us</h2>
                <x-social-links />
            </div>
            <div>
                <h2 class="text-sm font-semibold mb-2">Response time</h2>
                <p class="text-sm text-muted-foreground">We typically respond within 24 hours during business days.</p>
            </div>
        </div>
    </div>
@endsection
