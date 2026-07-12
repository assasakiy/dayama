<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
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
            'approved_at' => now(),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => 'approved',
            'approved_at' => now(),
        ]);
    }
}
