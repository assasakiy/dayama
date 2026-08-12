<?php

declare(strict_types=1);

namespace Database\Factories;

use Modules\CMS\Models\Comment;
use Modules\CMS\Models\Post;
use Modules\Core\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Comment> */
class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'content' => fake()->paragraphs(fake()->numberBetween(1, 4), true),
            'status' => fake()->randomElement(['approved', 'approved', 'approved', 'pending']),
            'post_id' => Post::factory(),
            'author_id' => User::factory(),
            'likes_count' => fake()->numberBetween(0, 25),
            'moderated_at' => now(),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => 'approved',
            'moderated_at' => now(),
        ]);
    }
}
