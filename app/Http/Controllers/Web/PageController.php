<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ContentBlock;
use App\Models\Faq;
use App\Models\LegalPage;
use App\Models\Screen;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function home(Request $request): Response
    {
        $locale = $this->locale($request);

        return Inertia::render('Home', [
            'locale' => $locale,
            'contentBlocks' => ContentBlock::where('active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn (ContentBlock $block) => $block->publicPayload($locale))
                ->keyBy('key'),
            'faqs' => Faq::where('active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn (Faq $faq) => $faq->publicPayload($locale))
                ->values(),
            'contact' => Setting::where('is_public', true)->pluck('value', 'key'),
            'summary' => [
                'totalScreens' => Screen::count(),
                'activeScreens' => Screen::active()->count(),
                'availableScreens' => Screen::publiclyVisible()->count(),
            ],
            'screens' => Screen::with('tags')->publiclyVisible()->limit(8)->get()->map->publicPayload()->values(),
        ]);
    }

    public function screens(): Response
    {
        return Inertia::render('Screens/Index', [
            'screens' => Screen::with('tags')->publiclyVisible()->get()->map->publicPayload()->values(),
        ]);
    }

    public function advice(): Response
    {
        return Inertia::render('Advice', [
            'screens' => Screen::with('tags')->publiclyVisible()->get()->map->publicPayload()->values(),
            'turnstileSiteKey' => config('services.turnstile.enabled') ? config('services.turnstile.site_key') : null,
        ]);
    }

    public function venues(): Response
    {
        return Inertia::render('Venues/Index');
    }

    public function advertisers(): Response
    {
        return Inertia::render('Advertisers/Index', [
            'screens' => Screen::with('tags')->publiclyVisible()->get()->map->publicPayload()->values(),
        ]);
    }

    public function thanks(): Response
    {
        return Inertia::render('Thanks');
    }

    public function privacy(Request $request): Response
    {
        return $this->legal('privacidad', 'Política de privacidad', $request);
    }

    public function cookies(Request $request): Response
    {
        return $this->legal('cookies', 'Política de cookies', $request);
    }

    public function legalNotice(Request $request): Response
    {
        return $this->legal('aviso-legal', 'Aviso legal', $request);
    }

    private function legal(string $slug, string $fallbackTitle, Request $request): Response
    {
        $locale = $this->locale($request);
        $page = LegalPage::where('slug', $slug)->where('active', true)->first();

        return Inertia::render('Legal/Page', [
            'page' => $page?->publicPayload($locale) ?? [
                'slug' => $slug,
                'title' => $fallbackTitle,
                'content' => 'Texto pendiente de configurar desde admin.',
            ],
        ]);
    }

    private function locale(Request $request): string
    {
        return app()->getLocale();
    }
}
