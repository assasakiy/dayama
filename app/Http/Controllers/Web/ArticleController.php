<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Modules\CMS\Models\Post;
use App\Services\PostMetricsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function __construct(
        protected PostMetricsService $metrics,
    ) {}

    public function show(Post $post, Request $request): View
    {
        if (!$post->is_published) {
            abort(404);
        }

        $this->metrics->recordView($post);

        $post->load(['author', 'primaryCategory', 'tags', 'comments.user', 'comments.replies.user']);

        $related = Post::published()
            ->where('id', '!=', $post->id)
            ->where(function ($q) use ($post) {
                if ($post->category_id) {
                    $q->where('category_id', $post->category_id);
                }
            })
            ->with(['author', 'primaryCategory'])
            ->latest('published_at')
            ->take(3)
            ->get();

        $prevPost = Post::published()
            ->where('published_at', '<', $post->published_at)
            ->latest('published_at')
            ->first();

        $nextPost = Post::published()
            ->where('published_at', '>', $post->published_at)
            ->oldest('published_at')
            ->first();

        $comments = $post->comments()->whereNull('parent_id')->latest()->get();

        $toc = $this->generateToc($post->body);

        $breadcrumbs = [
            ['label' => 'Blog', 'url' => route('blog.index')],
            ['label' => $post->title],
        ];

        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $post->title,
            'description' => $post->excerpt,
            'image' => $post->cover_url,
            'datePublished' => $post->published_at?->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => $post->author?->name ?? 'Unknown',
            ],
        ];

        return view('web.pages.article.show', compact(
            'post', 'related', 'comments', 'prevPost', 'nextPost', 'breadcrumbs', 'jsonLd', 'toc'
        ));
    }

    protected function generateToc(?string $html): array
    {
        if (!$html) return [];

        $headings = [];
        preg_match_all('/<h([2-3])\s+[^>]*id=["\']([^"\']+)["\'][^>]*>(.*?)<\/h[2-3]>/i', $html, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $headings[] = [
                'level' => (int) $match[1],
                'id' => $match[2],
                'text' => strip_tags($match[3]),
            ];
        }

        return $headings;
    }
}
