<{{ '?xml version="1.0" encoding="UTF-8"?' }}>
    <rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
        <channel>
            <title>{{ config('app.name') }}</title>
            <link>{{ url('/') }}</link>
            <description>{{ __('Latest articles from :app', ['app' => config('app.name')]) }}</description>
            <language>en</language>
            <lastBuildDate>{{ now()->toRssString() }}</lastBuildDate>
            <atom:link href="{{ url('rss.xml') }}" rel="self" type="application/rss+xml" />
            @foreach($posts as $post)
                <item>
                    <title>{{ $post->title }}</title>
                    <link>{{ route('blog.show', $post) }}</link>
                    <guid isPermaLink="true">{{ route('blog.show', $post) }}</guid>
                    @php
                        $rssDesc = $post->excerpt ?: Illuminate\Support\Str::limit(strip_tags($post->content), 160);
                    @endphp
                    <description><![CDATA[{!! $rssDesc !!}]]></description>
                    <author>{{ $post->author?->email ?? 'noreply@blog.com' }} ({{ $post->author?->name ?? '' }})</author>
                    <category>{{ $post->category?->name ?? '' }}</category>
                    <pubDate>{{ $post->published_at->toRssString() }}</pubDate>
                </item>
            @endforeach
        </channel>
    </rss>