@php 
    $context = $context ?? 'blog';
@endphp
<header 
    class="sticky top-0 z-50 flex flex-col"
>
    {{-- Include Header 1 (Main Navigation) --}}
    @include('web.partials.header.desktop', ['context' => $context])
    
    {{-- Include Header 2 (Categories Scroller) only if not on the landing page --}}
    @if($context !== 'landing')
        @include('web.partials.header.scroller', ['context' => $context])
    @endif
</header>




@include('web.partials.header.search-modal')
@include('web.partials.header.mobile', ['context' => $context])

<script>
    window.mobileMenuState = false;
    window.scrollPosition = 0;
    
    // Create backdrop element
    const backdrop = document.createElement('div');
    backdrop.className = 'fixed inset-0 bg-black/50 z-40 hidden opacity-0 transition-opacity duration-300 lg:hidden backdrop-blur-sm';
    backdrop.id = 'mobile-menu-backdrop';
    document.body.appendChild(backdrop);
    
    backdrop.addEventListener('click', () => {
        if (window.mobileMenuState) {
            window.toggleMobileMenu(false);
        }
    });

    window.toggleMobileMenu = function(forceState) {
        const menu = document.getElementById('mobile-menu');
        if (!menu) return;
        
        window.mobileMenuState = forceState !== undefined ? forceState : !window.mobileMenuState;
        
        if (window.mobileMenuState) {
            // Opening: lock scroll but keep scrollbar using fixed positioning
            window.scrollPosition = window.scrollY;
            document.body.style.position = 'fixed';
            document.body.style.top = `-${window.scrollPosition}px`;
            document.body.style.width = '100%';
            document.body.style.overflowY = 'scroll'; // Force scrollbar to stay
            
            menu.classList.remove('hidden');
            backdrop.classList.remove('hidden');
            // Small delay to allow transition
            setTimeout(() => backdrop.classList.remove('opacity-0'), 10);
            
            window.dispatchEvent(new CustomEvent('mobile-menu-changed', { detail: true }));
        } else {
            // Closing
            document.body.style.position = '';
            document.body.style.top = '';
            document.body.style.width = '';
            document.body.style.overflowY = '';
            window.scrollTo(0, window.scrollPosition);
            
            backdrop.classList.add('opacity-0');
            setTimeout(() => backdrop.classList.add('hidden'), 300);
            
            menu.classList.add('hidden');
            window.dispatchEvent(new CustomEvent('mobile-menu-changed', { detail: false }));
        }
    };

    // Close menu when resizing to desktop to prevent permanent scroll lock
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024 && window.mobileMenuState) {
            window.toggleMobileMenu(false);
        }
    });
</script>
