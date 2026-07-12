<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            EmailTemplateSeeder::class,
            RoleAndPermissionSeeder::class,
        ]);

        $defaultUsers = [
            ['name' => 'Super Admin', 'username' => 'superadmin', 'email' => 'superadmin@blog.com', 'role' => 'Super Admin'],
            ['name' => 'Admin',        'username' => 'admin01',    'email' => 'admin@blog.com',      'role' => 'Administrator'],
            ['name' => 'Editor',       'username' => 'editor01',   'email' => 'editor@blog.com',     'role' => 'Editor'],
            ['name' => 'Author',       'username' => 'author01',   'email' => 'author@blog.com',     'role' => 'Author'],
            ['name' => 'Contributor',  'username' => 'contributor01', 'email' => 'contributor@blog.com', 'role' => 'Contributor'],
        ];

        foreach ($defaultUsers as $index => $userData) {
            $isPrimarySuperAdmin = ($index === 0);
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'username' => $userData['username'],
                    'email' => $userData['email'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'is_primary_super_admin' => $isPrimarySuperAdmin,
                    'is_protected' => $isPrimarySuperAdmin,
                    'is_verified' => $isPrimarySuperAdmin,
                ]
            );
            $user->assignRole($userData['role']);
            if (!$user->profile) {
                $user->profile()->create([]);
            }
        }

        // Skip bulk data if already exists
        if (Category::count() > 0) {
            return;
        }

        // Additional users
        User::factory(10)->create()->each(fn (User $u) => $u->assignRole('Author'));

        // Categories
        $categories = collect();
        $parentNames = ['Technology', 'Design', 'Business', 'Science', 'Lifestyle'];
        foreach ($parentNames as $name) {
            $parent = Category::create(['name' => $name, 'slug' => \Illuminate\Support\Str::slug($name), 'parent_id' => null]);
            $categories->push($parent);

            $childNames = match ($name) {
                'Technology' => ['Web Development', 'Mobile Apps', 'AI & ML', 'Cybersecurity'],
                'Design'     => ['UI/UX', 'Graphic Design', 'Typography'],
                'Business'   => ['Startups', 'Marketing', 'Finance'],
                'Science'    => ['Physics', 'Biology', 'Space'],
                'Lifestyle'  => ['Health', 'Travel', 'Food'],
                default      => [],
            };
            foreach ($childNames as $childName) {
                $child = Category::create([
                    'name' => $childName,
                    'slug' => \Illuminate\Support\Str::slug($childName),
                    'parent_id' => $parent->id,
                ]);
                $categories->push($child);
            }
        }

        // Tags
        $tagNames = [
            'PHP', 'Laravel', 'React', 'Vue.js', 'JavaScript', 'TypeScript',
            'Python', 'Machine Learning', 'AWS', 'Docker', 'DevOps',
            'REST API', 'GraphQL', 'Tailwind CSS', 'Testing', 'Performance',
            'Security', 'Database', 'Mobile', 'Open Source',
        ];
        $tags = collect();
        foreach ($tagNames as $name) {
            $tags->push(Tag::create(['name' => $name, 'slug' => \Illuminate\Support\Str::slug($name)]));
        }

        // Posts
        Post::factory(100)
            ->published()
            ->sequence(fn ($s) => [
                'category_id' => $categories->random()->id,
                'author_id' => User::inRandomOrder()->first()->id,
            ])
            ->create()
            ->each(function (Post $post) use ($tags): void {
                $post->tags()->attach($tags->random(random_int(2, 5))->pluck('id'));
            });

        Post::factory(20)->draft()->sequence(fn ($s) => [
            'category_id' => $categories->random()->id,
            'author_id' => User::inRandomOrder()->first()->id,
        ])->create();

        Post::factory(5)->featured()->published()->sequence(fn ($s) => [
            'category_id' => $categories->random()->id,
            'author_id' => User::where('email', 'author@blog.com')->first()->id,
        ])->create();

        // Comments
        $publishedPosts = Post::where('status', 'published')->get();

        Comment::factory(300)->approved()->sequence(fn ($s) => [
            'post_id' => $publishedPosts->random()->id,
            'author_id' => User::inRandomOrder()->first()->id,
        ])->create();

        Comment::factory(100)->approved()->sequence(fn ($s) => [
            'post_id' => $publishedPosts->random()->id,
            'author_id' => User::inRandomOrder()->first()->id,
            'parent_id' => Comment::inRandomOrder()->first()->id,
        ])->create();
    }
}
