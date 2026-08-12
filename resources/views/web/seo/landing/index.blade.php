<{!! '?xml version="1.0" encoding="UTF-8"?' !!}>
<{!! '?xml-stylesheet type="text/xsl" href="/sitemap-landing.xsl"?' !!}>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <sitemap>
        <loc>{{ route('landing.sitemap.section', ['section' => 'profil']) }}</loc>
    </sitemap>
    <sitemap>
        <loc>{{ route('landing.sitemap.section', ['section' => 'pendidikan']) }}</loc>
    </sitemap>
    <sitemap>
        <loc>{{ route('landing.sitemap.section', ['section' => 'layanan']) }}</loc>
    </sitemap>
    <sitemap>
        <loc>{{ route('landing.sitemap.section', ['section' => 'media']) }}</loc>
    </sitemap>
</sitemapindex>
