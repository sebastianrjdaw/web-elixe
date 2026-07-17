<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $paths = ['', '/locales', '/anunciantes', '/red-de-pantallas', '/asesoramiento', '/privacidad', '/cookies', '/aviso-legal'];
        $baseUrl = rtrim(config('app.url'), '/');

        $urls = collect($paths)
            ->flatMap(fn (string $path) => [$baseUrl.$path, $baseUrl.'/gl'.$path])
            ->map(fn (string $url) => '  <url><loc>'.e($url).'</loc></url>')
            ->join("\n");

        return response("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n{$urls}\n</urlset>", 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}
