<?php

namespace App\Helpers;

use App\Mail\EmailSitemap;

class SitemapHelper
{
    private string $website;

    public function __construct($website)
    {
        $this->website = $website;
    }

    public function findSitemap()
    {
        return \Http::get("$this->website/sitemap.xml");
    }

    public function generateSitemap($pages)
    {
        return $pages;
    }

    public function emailSitemap($email, $sitemap)
    {
        \Mail::to($email)->send(new EmailSitemap($sitemap));
    }
}
