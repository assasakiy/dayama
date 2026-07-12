@props(['breadcrumbs' => []])
@if (count($breadcrumbs))
    <nav aria-label="Breadcrumb" class="mb-4">
        <ol class="flex items-center gap-1.5 text-sm text-muted-foreground flex-wrap">
            <li><a href="{{ route('home') }}" class="hover:text-foreground transition-colors">Home</a></li>
            @foreach ($breadcrumbs as $crumb)
                <li class="flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                    @if (isset($crumb['url']) && !$loop->last)
                        <a href="{{ $crumb['url'] }}" class="hover:text-foreground transition-colors">{{ $crumb['label'] }}</a>
                    @else
                        <span aria-current="page">{{ $crumb['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
        <script type="application/ld+json">{"@@context":"https://schema.org","@@type":"BreadcrumbList","itemListElement":[{{ implode(',', array_map(fn($c, $i) => '{"@@type":"ListItem","position":'.($i+1).',"name":"'.$c['label'].'","item":"'.($c['url'] ?? request()->url()).'"}', $breadcrumbs, array_keys($breadcrumbs))) }}]}</script>
    </nav>
@endif
