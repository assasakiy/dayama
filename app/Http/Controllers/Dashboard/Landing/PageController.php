<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\Landing;

use App\Http\Controllers\Controller;

use Modules\Landing\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function index(): Response
    {
        $pages = Page::orderBy('sort_order')->get();

        return Inertia::render('Landing/Pages/Index', [
            'pages' => $pages,
        ]);
    }

    public function edit(Page $page): Response
    {
        return Inertia::render('Landing/Pages/Edit', [
            'page' => $page,
        ]);
    }

    private function processSectionsMedia(array &$sections)
    {
        $systemAsset = \Modules\System\Models\SystemAsset::firstOrCreate(['id' => 1], ['name' => 'Global']);

        $process = function (&$item) use (&$process, $systemAsset) {
            if (is_array($item)) {
                if (isset($item['image_media_id'])) {
                    $mediaId = (int) $item['image_media_id'];
                    /** @var \Modules\Core\Models\Media|null $media */
                    $media = \Modules\Core\Models\Media::find($mediaId);
                    
                    if ($media) {
                        if ($media->model_type !== \Modules\System\Models\SystemAsset::class) {
                            $newMedia = $media->copy($systemAsset, 'landing_pages');
                            $item['image_media_id'] = $newMedia->id;
                            $item['image'] = parse_url($newMedia->getUrl(), PHP_URL_PATH);
                        }
                    }
                }
                
                foreach ($item as $key => &$value) {
                    if (is_array($value)) {
                        $process($value);
                    }
                }
            }
        };

        $process($sections);
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'sections'  => ['required', 'array'],
            'is_active' => ['boolean'],
        ]);

        $this->processSectionsMedia($data['sections']);

        $page->update($data);

        return redirect()->route('dashboard.landing.pages.index')
            ->with('success', 'Halaman berhasil diperbarui.');
    }

    /**
     * Update only a single section of a page (used by Tab editors).
     */
    public function updateSection(Request $request, Page $page): RedirectResponse
    {
        $data = $request->validate([
            'section_key' => ['required', 'string'],
            'section_data' => ['required', 'array'],
        ]);

        $this->processSectionsMedia($data['section_data']);

        $page->setSection($data['section_key'], $data['section_data']);
        $page->save();

        return redirect()->back()->with('success', 'Section berhasil diperbarui.');
    }
}
