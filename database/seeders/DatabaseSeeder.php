<?php

declare(strict_types=1);

namespace Database\Seeders;

use Modules\CMS\Models\Category;
use Modules\CMS\Models\Comment;
use Modules\Core\Models\Person;
use Modules\CMS\Models\Post;
use Modules\CMS\Models\Tag;
use Modules\Core\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            EmailTemplateSeeder::class,
            RoleAndPermissionSeeder::class,
            LandingSeeder::class,
            InstitutionSeeder::class,
            InstitutionTypeSeeder::class,
            PositionSeeder::class,
            EducationLevelSeeder::class,
            ProfessionSeeder::class,
            RelationshipTypeSeeder::class,
            ContactTypeSeeder::class,
            AddressTypeSeeder::class,
            SkillSeeder::class,
            LanguageSeeder::class,
            EmploymentStatusSeeder::class,
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

            $nameParts = explode(' ', $userData['name'], 2);
            $person = Person::firstOrCreate(
                ['nama_lengkap' => $userData['name']],
                [
                    'nama_depan' => $nameParts[0],
                    'nama_belakang' => $nameParts[1] ?? null,
                    'nama_lengkap' => $userData['name'],
                ]
            );

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'person_id' => $person->id,
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

        // Skip bulk demo data if already exists
        if (Category::count() > 0) {
            return;
        }

        // Additional users
        User::factory(10)->create()->each(fn (User $u) => $u->assignRole('Author'));

        // Categories
        $categories = collect();
        $parentNames = ['Teknologi', 'Desain', 'Bisnis', 'Sains', 'Gaya Hidup'];
        foreach ($parentNames as $name) {
            $parent = Category::create(['name' => $name, 'slug' => \Illuminate\Support\Str::slug($name), 'parent_id' => null]);
            $categories->push($parent);

            $childNames = match ($name) {
                'Teknologi' => ['Pengembangan Web', 'Aplikasi Mobile', 'Kecerdasan Buatan', 'Keamanan Siber'],
                'Desain'    => ['UI/UX', 'Desain Grafis', 'Tipografi'],
                'Bisnis'    => ['Startup', 'Pemasaran', 'Keuangan'],
                'Sains'     => ['Fisika', 'Biologi', 'Luar Angkasa'],
                'Gaya Hidup'=> ['Kesehatan', 'Perjalanan', 'Makanan'],
                default     => [],
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
            'REST API', 'GraphQL', 'Tailwind CSS', 'Pengujian', 'Performa',
            'Keamanan', 'Basis Data', 'Mobile', 'Open Source',
        ];
        $tags = collect();
        foreach ($tagNames as $name) {
            $tags->push(Tag::create(['name' => $name, 'slug' => \Illuminate\Support\Str::slug($name)]));
        }

        // Posts
        Post::factory(100)
            ->published()
            ->sequence(fn ($s) => [
                'primary_category_id' => $categories->random()->id,
                'author_id' => User::inRandomOrder()->first()->id,
            ])
            ->create()
            ->each(function (Post $post) use ($tags): void {
                $post->tags()->attach($tags->random(random_int(2, 5))->pluck('id'));
            });

        Post::factory(20)->draft()->sequence(fn ($s) => [
            'primary_category_id' => $categories->random()->id,
            'author_id' => User::inRandomOrder()->first()->id,
        ])->create();

        Post::factory(5)->featured()->published()->sequence(fn ($s) => [
            'primary_category_id' => $categories->random()->id,
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
