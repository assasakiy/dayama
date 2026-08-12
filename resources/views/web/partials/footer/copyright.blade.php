<div class="mt-8 pt-6 border-t border-border-subtle flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-muted-foreground">
    <p>&copy; {{ date('Y') }} {{ $siteName }}. Hak cipta dilindungi undang-undang.</p>
    <div class="flex items-center gap-4">
        <a href="{{ $landingUrl }}/kebijakan-privasi" class="hover:text-foreground transition-colors">Kebijakan Privasi</a>
        <a href="{{ $landingUrl }}/syarat-ketentuan" class="hover:text-foreground transition-colors">Syarat & Ketentuan</a>
        <a href="/sitemap.xml" class="hover:text-foreground transition-colors">Peta Situs</a>
    </div>
</div>
