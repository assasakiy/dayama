<{!! '?xml version="1.0" encoding="UTF-8"?' !!}>
    <{!! '?xml-stylesheet type="text/xsl" href="/sitemap.xsl"?' !!}>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
            @foreach($tags as $tag)
                <url>
                    <loc>{{ route('tag.show', $tag) }}</loc>
                    <changefreq>weekly</changefreq>
                    <priority>0.4</priority>
                </url>
            @endforeach
        </urlset>