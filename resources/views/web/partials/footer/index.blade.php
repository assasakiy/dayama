@php
    $context = $context ?? 'blog';
    $siteName = \App\Services\SettingService::get('general.site_name', config('app.name'), $context);
    $tagline = \App\Services\SettingService::get('general.tagline', 'Lembaga Pendidikan & Dakwah Islamiyah', $context);
    $footerDesc = \App\Services\SettingService::get('general.site_description', 'Pondok Pesantren Darul Yatama Wal Masakin berkomitmen untuk mencetak generasi Qurani yang berakhlak mulia dan berwawasan luas.', $context);
    $logoUrl = \App\Services\SettingService::get('general.logo_url', null, $context);
    
    // Get URLs for routing
    $landingDomain = config('projects.projects.landing.domain', env('DOMAIN_MAIN', 'test-blog.test'));
    $landingUrl = 'http://' . $landingDomain;
    
    $blogDomain = config('projects.projects.blog.domain', env('DOMAIN_BLOG', 'blog.test-blog.test'));
    $blogUrl = 'http://' . $blogDomain;
    
    // Determine which footer to load
    $isBlog = $context === 'blog' || request()->getHost() === $blogDomain;
@endphp

<footer class="mt-auto border-t border-border-subtle bg-surface-muted/50">
    <div class="container-page py-12">
        
        @if($isBlog)
            @include('web.partials.footer.blog')
        @else
            @include('web.partials.footer.landing')
        @endif

        @include('web.partials.footer.copyright')
    </div>
</footer>
