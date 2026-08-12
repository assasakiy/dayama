<{!! '?xml version="1.0" encoding="UTF-8"?' !!}>
    <{!! '?xml-stylesheet type="text/xsl" href="/sitemap-blog.xsl"?' !!}>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
            @foreach($posts as $post)
                <url>
                    <loc>{{ route('blog.show', $post) }}</loc>
                    <lastmod>{{ $post->updated_at->toAtomString() }}</lastmod>
                    <changefreq>weekly</changefreq>
                    <priority>0.8</priority>
                </url>
            @endforeach
        </urlset>
