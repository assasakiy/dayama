<?php

declare(strict_types=1);

$rootDomain = env('APP_ROOT_DOMAIN', env('DOMAIN_MAIN', 'dayama.test'));

return [
    /*
    |--------------------------------------------------------------------------
    | Platform Root Domain
    |--------------------------------------------------------------------------
    |
    | Sumber kebenaran utama domain. Di local: dayama.test, di prod: dayama.id.
    | Seluruh subdomain platform dan lembaga default diturunkan dari nilai ini.
    |
    */
    'root_domain' => $rootDomain,

    /*
    |--------------------------------------------------------------------------
    | Platform Applications
    |--------------------------------------------------------------------------
    |
    | Aplikasi internal platform DAYAMA yang menangani fungsi sistem.
    | Jika environment override (DOMAIN_*) tidak diset, otomatis menyusun:
    | {subdomain}.{root_domain}.
    |
    */
    'apps' => [
        'account' => [
            'name'        => 'Account',
            'type'        => 'identity',
            'subdomain'   => 'account',
            'domain'      => env('DOMAIN_AUTH', "account.{$rootDomain}"),
            'route_file'  => 'routes/apps/account.php',
            'middleware'  => ['web'],
            'enabled'     => true,
        ],
        'dashboard' => [
            'name'        => 'Dashboard',
            'type'        => 'workspace',
            'subdomain'   => 'dashboard',
            'domain'      => env('DOMAIN_DASHBOARD', "dashboard.{$rootDomain}"),
            'route_file'  => 'routes/apps/dashboard.php',
            'middleware'  => ['web'],
            'enabled'     => true,
        ],
        'portal' => [
            'name'        => 'Portal',
            'type'        => 'personal',
            'subdomain'   => 'portal',
            'domain'      => env('DOMAIN_PORTAL', "portal.{$rootDomain}"),
            'route_file'  => 'routes/apps/portal.php',
            'middleware'  => ['web'],
            'enabled'     => true,
        ],
        'psb' => [
            'name'        => 'PSB',
            'type'        => 'admission',
            'subdomain'   => 'psb',
            'domain'      => env('DOMAIN_PSB', "psb.{$rootDomain}"),
            'route_file'  => 'routes/apps/psb.php',
            'middleware'  => ['web'],
            'enabled'     => true,
        ],
        'datacenter' => [
            'name'        => 'Data Center',
            'type'        => 'registry',
            'subdomain'   => 'data',
            'domain'      => env('DOMAIN_DATACENTER', "data.{$rootDomain}"),
            'route_file'  => 'routes/apps/datacenter.php',
            'middleware'  => ['web'],
            'enabled'     => true,
        ],
        'api' => [
            'name'        => 'API Gateway',
            'type'        => 'gateway',
            'subdomain'   => 'api',
            'domain'      => env('DOMAIN_API', "api.{$rootDomain}"),
            'route_file'  => 'routes/apps/api.php',
            'middleware'  => ['api'],
            'enabled'     => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sites & Content Surfaces
    |--------------------------------------------------------------------------
    |
    | Halaman muka, portal publik, media, dan microsite.
    | - Landing: berjalan langsung di root domain ({rootDomain}).
    | - Blog: berjalan di subdomain blog ({subdomain}.{rootDomain}).
    |
    */
    'sites' => [
        'landing' => [
            'name'        => 'Yayasan Landing',
            'type'        => 'landing',
            'domain'      => env('DOMAIN_MAIN', $rootDomain),
            'route_file'  => 'routes/sites/landing.php',
            'middleware'  => ['web'],
            'enabled'     => true,
        ],
        'blog' => [
            'name'        => 'Blog CMS',
            'type'        => 'blog',
            'subdomain'   => 'blog',
            'domain'      => env('DOMAIN_BLOG', "blog.{$rootDomain}"),
            'route_file'  => 'routes/sites/blog.php',
            'middleware'  => ['web'],
            'enabled'     => true,
        ],
    ],
];
