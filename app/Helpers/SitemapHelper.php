<?php

namespace App\Helpers;

use App\Mail\EmailSitemap;
use Carbon\Carbon;

class SitemapHelper
{
    private string $website;

    public function __construct($website)
    {
        $this->website = str($website)->replaceLast('/', '')->value();
    }

    public function findSitemap(): \Illuminate\Http\Client\Response
    {
        return \Http::get("$this->website/sitemap.xml");
    }

    public function generateSitemap($pages): string
    {
        $output = "<?xml version='1.0' encoding='utf-8'?>\n";
        $output .= "<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>\n";

        foreach ($pages as $page) {
            $url = \Arr::get($page, 'url', '');
            $mod = \Arr::get($page, 'last_modified', Carbon::now()->format('Y-m-d'));
            $output .= "\t<url>\n";
            $output .= "\t\t<loc>{$this->website}{$url}</loc>\n";
            $output .= "\t\t<lastmod>{$mod}</lastmod>\n";
            $output .= "\t\t<changefreq>weekly</changefreq>\n";
            $output .= "\t\t<priority>1</priority>\n";
            $output .= "\t</url>\n";
        }

        $output .= '</urlset>';
        return $output;
    }

    public function emailSitemap($email, $sitemap): void
    {
        \Mail::to($email)->send(new EmailSitemap($sitemap));
    }
}
