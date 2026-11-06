<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use SimpleXMLElement;

class SitemapController
{
    /**
     * Public pages that search engines may index, keyed by route name.
     *
     * @var array<string, array{changefreq: string, priority: string}>
     */
    private const INDEXABLE_PAGES = [
        'home' => ['changefreq' => 'weekly', 'priority' => '1.0'],
    ];

    /**
     * Render the XML sitemap listing every public page of the site.
     */
    public function __invoke(): Response
    {
        $urlset = new SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8"?>'
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"/>'
        );

        $lastModified = $this->lastModified();

        foreach (self::INDEXABLE_PAGES as $routeName => $page) {
            $url = $urlset->addChild('url');

            $url->addChild('loc', route($routeName));
            $url->addChild('lastmod', $lastModified);
            $url->addChild('changefreq', $page['changefreq']);
            $url->addChild('priority', $page['priority']);
        }

        return response($urlset->asXML(), 200, [
            'Content-Type' => 'text/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600, stale-while-revalidate=86400',
        ]);
    }

    /**
     * Most recent modification time of the page content, as W3C datetime.
     *
     * Falls back to today when no content file is found.
     */
    private function lastModified(): string
    {
        $candidates = [
            resource_path('views/welcome.blade.php'),
            resource_path('views/layouts/portfolio.blade.php'),
            config_path('portfolio.php'),
        ];

        $latest = 0;

        foreach ($candidates as $file) {
            if (is_file($file)) {
                $latest = max($latest, (int) filemtime($file));
            }
        }

        $timestamp = $latest > 0 ? $latest : time();

        return date('Y-m-d', $timestamp);
    }
}
