@guest
<section class="container-page py-12 text-center" aria-labelledby="newsletter-title">
    <div class="max-w-md mx-auto">
        <h2 id="newsletter-title" class="text-xl font-semibold mb-2">{{ __('Stay in the loop') }}</h2>
        <p class="text-sm text-muted-foreground mb-6">{{ __('Get the latest articles delivered straight to your inbox.') }}</p>
        <form action="#" method="POST" class="flex gap-2 max-w-sm mx-auto" role="form" aria-label="{{ __('Newsletter signup') }}">
            @csrf
            <label for="newsletter-email" class="sr-only">{{ __('Email address') }}</label>
            <input id="newsletter-email" type="email" name="email" required placeholder="{{ __('your@email.com') }}"
                   class="flex-1 h-10 px-3 text-sm bg-surface border border-border-subtle rounded-sm outline-none focus:border-primary transition-colors">
            <button type="submit" class="btn btn-primary whitespace-nowrap">{{ __('Subscribe') }}</button>
        </form>
    </div>
</section>
@endguest
