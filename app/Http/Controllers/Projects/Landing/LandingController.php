<?php

namespace App\Http\Controllers\Projects\Landing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class LandingController extends Controller
{
    /**
     * Tampilkan halaman utama landing (Home).
     */
    public function index()
    {
        $page = \Modules\Landing\Models\Page::with(['cta', 'statGroup'])->where('slug', 'home')->where('is_active', true)->first();
        $globalCta = \Modules\Landing\Models\Cta::active()->first();
        
        return view('projects.landing.index', [
            'context' => 'landing',
            'page' => $page,
            'globalCta' => $globalCta,
        ]);
    }

    /**
     * Tampilkan halaman statis berdasarkan segmen URL.
     * 
     * Sistem ini menggunakan pendekatan "Specific-first, Fallback-second":
     * 1. Cek apakah ada view spesifik: projects.landing.{section}.{slug}
     * 2. Jika tidak, gunakan view generik: projects.landing.page
     * 
     * Contoh:
     *   /profil/sejarah → cek projects.landing.profil.sejarah
     *                    → fallback projects.landing.page
     */
    public function page(Request $request, $section, $page)
    {
        $validSections = ['profil', 'pendidikan', 'layanan', 'media'];
        
        if (!in_array($section, $validSections)) {
            abort(404);
        }

        // --- Cek Khusus Lembaga Pendidikan ---
        if ($section === 'pendidikan' && str_starts_with($page, 'lembaga/')) {
            $slug = str_replace('lembaga/', '', $page);
            $institution = \Modules\Core\Models\Institution::where('slug', $slug)
                                                  ->where('is_active', true)
                                                  ->first();
            if ($institution) {
                return view('projects.landing.pendidikan.lembaga.show', [
                    'context' => 'landing',
                    'title' => $institution->name,
                    'section' => 'Pendidikan',
                    'institution' => $institution
                ]);
            }
        }
        
        // Format judul halaman dari URL slug
        // contoh: "lembaga/pondok-pesantren" menjadi "Pondok Pesantren"
        $pageName = basename($page);
        $title = ucwords(str_replace('-', ' ', $pageName));
        $sectionTitle = ucwords(str_replace('-', ' ', $section));

        $pageModel = \Modules\Landing\Models\Page::with(['cta', 'statGroup'])
            ->where('slug', $pageName)
            ->first();

        // Get default home page hero image as fallback
        $homePage = \Modules\Landing\Models\Page::where('slug', 'home')->first();
        $heroImage = $pageModel?->hero_image ?? $homePage?->hero_image;

        $data = [
            'context' => 'landing',
            'title' => $title,
            'section' => $sectionTitle,
            'sectionSlug' => $section,
            'slug' => $page,
            'pageModel' => $pageModel,
            'heroImage' => $heroImage,
        ];

        // Cek apakah view spesifik ada (ganti slash dengan dot untuk nested folder)
        $viewPath = str_replace('/', '.', $page);
        $specificView = "projects.landing.{$section}.{$viewPath}";
        
        if (View::exists($specificView)) {
            return view($specificView, $data);
        }

        // Jika tidak ada view spesifik, dan tidak ada data model page, berarti halaman benar-benar tidak ada
        if (!$pageModel) {
            abort(404);
        }

        // Fallback ke view generik
        return view('projects.landing.page', $data);
    }
}
