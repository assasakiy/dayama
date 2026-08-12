<{!! '?xml version="1.0" encoding="UTF-8"?' !!}>
<{!! '?xml-stylesheet type="text/xsl" href="/sitemap-landing.xsl"?' !!}>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/layanan/psb') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>{{ url('/layanan/donasi') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>

    <url>
        <loc>{{ url('/layanan/faq') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>{{ url('/layanan/kontak') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
</urlset>
