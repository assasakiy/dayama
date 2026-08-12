<?php

declare(strict_types=1);

namespace Database\Factories;

use Modules\Core\Models\Person;
use Modules\Core\Models\User;
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

        $nama_lengkap = fake()->name();
        $nameParts = explode(' ', $nama_lengkap, 2);

        return [
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
            $nameParts = explode(' ', $user->name ?? fake()->name(), 2);
            $person = Person::firstOrCreate(
                ['nama_lengkap' => $user->name ?? fake()->name()],
                [
                    'nama_depan' => $nameParts[0],
                    'nama_belakang' => $nameParts[1] ?? null,
                    'nama_lengkap' => $user->name ?? fake()->name(),
                ]
            );
            $user->person_id = $person->id;
            $user->save();

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
            'username' => 'superadmin',
            'email' => 'superadmin@blog.com',
        ]);
    }
}
