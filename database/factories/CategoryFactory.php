<?php

declare(strict_types=1);

namespace Database\Factories;

use Modules\CMS\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Category> */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Technology', 'Design', 'Development', 'DevOps', 'Mobile',
                'Artificial Intelligence', 'Security', 'Cloud Computing',
                'Data Science', 'Productivity', 'Startups', 'Career',
                'Open Source', 'Blockchain', 'IoT', 'Gaming',
                'Tutorial', 'Case Study', 'Opinion', 'Research',
            ]),
            'slug' => fn (array $attrs) => Str::slug($attrs['name'] ?? fake()->word()),
            'description' => fake()->sentence(),
            'is_visible' => true,
            'sort_order' => fake()->numberBetween(1, 50),
            'posts_count' => 0,
        ];
    }

    public function childOf(string $parentId): static
    {
        return $this->state(fn () => ['parent_id' => $parentId]);
    }
}
