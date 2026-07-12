<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Core System Domains
    |--------------------------------------------------------------------------
    |
    | Sistem inti CMS ini terdiri dari API, Dashboard (Admin), dan Auth (Akun).
    | Ketiganya diisolasi di subdomainnya masing-masing.
    |
    */
    'core' => [
        'api'       => env('DOMAIN_API', 'api.test-blog.test'),
        'dashboard' => env('DOMAIN_DASHBOARD', 'dashboard.test-blog.test'),
        'auth'      => env('DOMAIN_AUTH', 'account.test-blog.test'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Managed Projects (Frontends)
    |--------------------------------------------------------------------------
    |
    | Di sinilah Anda mendaftarkan "wajah depan" dari sistem ini.
    | Bisa berupa Landing Page, Blog, Microsite, dll. Setiap proyek
    | berdiri sendiri dengan file routing dan domainnya masing-masing.
    |
    */
    'projects' => [
        'landing' => [
            'domain'     => env('DOMAIN_MAIN', 'test-blog.test'),
            'route_file' => 'routes/projects/landing.php',
            'active'     => true,
        ],
        'blog' => [
            'domain'     => env('DOMAIN_BLOG', 'blog.test-blog.test'),
            'route_file' => 'routes/projects/blog.php',
            'active'     => true,
        ],
    ],
];
