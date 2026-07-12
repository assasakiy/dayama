<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Tag> */
class TagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'PHP', 'Laravel', 'React', 'Vue', 'JavaScript',
                'TypeScript', 'Python', 'AWS', 'Docker', 'Kubernetes',
                'Tailwind CSS', 'API', 'Database', 'Testing', 'Performance',
                'Redis', 'GraphQL', 'Microservices', 'CI/CD', 'Linux',
            ]),
            'slug' => fn (array $attrs) => Str::slug($attrs['name'] ?? fake()->word()),
            'description' => fake()->sentence(),
            'is_visible' => true,
            'posts_count' => 0,
        ];
    }
}
