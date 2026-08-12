<{!! '?xml version="1.0" encoding="UTF-8"?' !!}>
    <{!! '?xml-stylesheet type="text/xsl" href="/sitemap-blog.xsl"?' !!}>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
            @foreach($categories as $category)
                <url>
                    <loc>{{ route('category.show', $category) }}</loc>
                    <changefreq>weekly</changefreq>
                    <priority>0.6</priority>
                </url>
            @endforeach
        </urlset>
