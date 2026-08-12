<?php

declare(strict_types=1);

namespace Database\Factories;

use Modules\CMS\Models\Category;
use Modules\CMS\Models\Post;
use Modules\Core\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Post> */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $title = fake()->sentence(random_int(4, 8));

        return [
            'title' => $title,
            'slug' => fn (array $attrs) => Str::slug($attrs['title'] ?? 'untitled-post'),
            'excerpt' => fake()->paragraph(2),
            'content' => fake()->paragraphs(20, true),
            'content_format' => 'tiptap',
            'status' => fake()->randomElement(['draft', 'published', 'published', 'published']),
            'published_at' => now()->subHours(fake()->numberBetween(1, 720)),
            'is_featured' => fake()->boolean(15),
            'is_sticky' => fake()->boolean(5),
            'allow_comments' => true,
            'views_count' => fake()->numberBetween(0, 5000),
            'reading_time' => fake()->numberBetween(3, 15),
            'author_id' => User::factory(),
            'primary_category_id' => Category::factory(),
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => 'published',
            'published_at' => now()->subHours(fake()->numberBetween(1, 720)),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }
}
