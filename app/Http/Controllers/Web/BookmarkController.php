<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Modules\CMS\Models\Post;
use App\Services\BookmarkService;
use App\Services\IdentityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function __construct(
        protected BookmarkService $bookmarkService
    ) {}

    public function update(Request $request, Post $post): JsonResponse
    {
        $identity = IdentityService::current();

        $bookmarked = $this->bookmarkService->toggle($post, $identity);

        return response()->json([
            'bookmarked' => $bookmarked,
            'message' => $bookmarked ? 'Post bookmarked.' : 'Bookmark removed.',
        ]);
    }
}
