<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        $username = fake()->unique()->userName();

        while (strlen($username) < 4) {
            $username .= fake()->randomDigit();
        }

        return [
            'name' => fake()->name(),
            'username' => $username,
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->profile()->create([
                'biography' => fake()->sentence(12),
                'website' => 'https://' . fake()->domainName(),
                'social_links' => [
                    'twitter' => 'https://twitter.com/' . $user->username,
                    'github' => 'https://github.com/' . $user->username,
                    'linkedin' => 'https://linkedin.com/in/' . $user->username,
                ],
            ]);
        });
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function superAdmin(): static
    {
        return $this->state(fn () => [
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'superadmin@blog.com',
        ]);
    }
}
