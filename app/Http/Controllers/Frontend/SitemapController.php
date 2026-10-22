<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\CaseStudy;
use App\Models\Project;
use App\Models\Service;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = [];

        // Static pages (loc, changefreq, priority)
        $static = [
            ['route' => 'home',     'freq' => 'daily',   'priority' => '1.0'],
            ['route' => 'about',    'freq' => 'monthly', 'priority' => '0.8'],
            ['route' => 'services', 'freq' => 'weekly',  'priority' => '0.9'],
            ['route' => 'projects', 'freq' => 'weekly',  'priority' => '0.8'],
            ['route' => 'our-work', 'freq' => 'weekly',  'priority' => '0.8'],
            ['route' => 'blog',     'freq' => 'daily',   'priority' => '0.8'],
            ['route' => 'contact',  'freq' => 'monthly', 'priority' => '0.7'],
            ['route' => 'privacy',  'freq' => 'yearly',  'priority' => '0.3'],
            ['route' => 'terms',    'freq' => 'yearly',  'priority' => '0.3'],
        ];
        foreach ($static as $page) {
            $urls[] = [
                'loc'     => route($page['route']),
                'lastmod' => now()->toAtomString(),
                'freq'    => $page['freq'],
                'priority' => $page['priority'],
            ];
        }

        // Dynamic content
        foreach (Service::active()->get() as $s) {
            $urls[] = $this->entry(route('services.show', $s->slug), $s->updated_at, 'monthly', '0.7');
        }
        foreach (Project::active()->get() as $p) {
            $urls[] = $this->entry(route('projects.show', $p->slug), $p->updated_at, 'monthly', '0.6');
        }
        foreach (CaseStudy::active()->get() as $c) {
            $urls[] = $this->entry(route('our-work.show', $c->slug), $c->updated_at, 'monthly', '0.6');
        }
        foreach (Blog::active()->get() as $b) {
            $urls[] = $this->entry(route('blog.show', $b->slug), $b->updated_at, 'weekly', '0.6');
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $u) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . htmlspecialchars($u['loc'], ENT_XML1) . "</loc>\n";
            $xml .= '    <lastmod>' . $u['lastmod'] . "</lastmod>\n";
            $xml .= '    <changefreq>' . $u['freq'] . "</changefreq>\n";
            $xml .= '    <priority>' . $u['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    private function entry($loc, $lastmod, $freq, $priority)
    {
        return [
            'loc'      => $loc,
            'lastmod'  => $lastmod ? $lastmod->toAtomString() : now()->toAtomString(),
            'freq'     => $freq,
            'priority' => $priority,
        ];
    }
}
