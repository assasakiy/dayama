<{!! '?xml version="1.0" encoding="UTF-8"?' !!}>
<{!! '?xml-stylesheet type="text/xsl" href="/sitemap-landing.xsl"?' !!}>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/media/galeri-foto') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>{{ url('/media/galeri-video') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>{{ url('/media/pengumuman') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>{{ url('/media/agenda') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>{{ url('/media/unduhan') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
</urlset>
