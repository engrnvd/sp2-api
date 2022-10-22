<?php

namespace App\Helpers;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use Spatie\Crawler\CrawlObservers\CrawlObserver;

class ApmCrawlerObserver extends CrawlObserver
{
    public function willCrawl(UriInterface $url): void
    {
    }

    public function crawled(UriInterface $url, ResponseInterface $response, ?UriInterface $foundOnUrl = null): void
    {
        echo "<br>crawled: " . $url . " ";
        echo "<br> " . var_export($response->getHeader('Last-Modified'), true);
        echo $response->getStatusCode();
    }

    public function crawlFailed(UriInterface $url, RequestException $requestException, ?UriInterface $foundOnUrl = null): void
    {
        echo '<br>crawlFailed: ' . $url;
    }

    public function finishedCrawling(): void
    {
        echo "<br>finished";
    }
}
