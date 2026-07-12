<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Reaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\IdentityService;

class ReactionController extends Controller
{
    public function update(Request $request, Post $post): JsonResponse
    {
        $request->validate([
            'type' => 'required|string|in:like,love,laugh,wow,sad,angry',
        ]);

        $identity = IdentityService::current();
        
        $reaction = Reaction::where('post_id', $post->id)
            ->where('identity_key', $identity['key'])
            ->first();

        $type = $request->input('type');

        if ($reaction) {
            if ($reaction->type === $type) {
                $reaction->delete();
                return response()->json(['message' => 'Reaction removed.', 'post' => $post->fresh()]);
            }
            
            $reaction->update(['type' => $type]);
            return response()->json(['message' => 'Reaction updated.', 'post' => $post->fresh()]);
        }

        Reaction::create([
            'post_id' => $post->id,
            'user_id' => $identity['user_id'],
            'identity_key' => $identity['key'],
            'ip_address' => $request->ip(),
            'type' => $type,
        ]);

        return response()->json(['message' => 'Reaction added.', 'post' => $post->fresh()]);
    }
}
