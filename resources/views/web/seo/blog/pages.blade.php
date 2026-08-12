<{!! '?xml version="1.0" encoding="UTF-8"?' !!}>
<{!! '?xml-stylesheet type="text/xsl" href="/sitemap-blog.xsl"?' !!}>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ route('home') ?? url('/') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ route('categories.index') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>{{ route('tags.index') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>{{ route('authors.index') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
</urlset>
