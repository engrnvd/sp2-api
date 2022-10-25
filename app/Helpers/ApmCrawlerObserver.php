<?php

namespace App\Helpers;

use App\Models\User;
use App\Traits\HasLogs;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use Spatie\Crawler\CrawlObservers\CrawlObserver;

class ApmCrawlerObserver extends CrawlObserver
{
    use HasLogs;

    private $logDir = 'crawler';
    private string $website;
    private ?User $user;
    private array $pages = [];

    public function __construct(string $website, User $user = null)
    {
        $this->website = $website;
        $this->user = $user;
    }

    public function willCrawl(UriInterface $url): void
    {
    }

    public function crawled(UriInterface $url, ResponseInterface $response, ?UriInterface $foundOnUrl = null): void
    {
        $lastMod = $this->parseLastMod($response);
        $page = [
            'url' => $url->getPath(),
            'last_modified' => $lastMod,
        ];
        $this->pages[] = $page;

        $this->notify("Crawling " . $url . "...");

        $this->log("Crawled " . to_str($page));
    }

    private function parseLastMod(ResponseInterface $response): Carbon
    {
        $lastMod = Arr::get($response->getHeader('Last-Modified'), 0);
        $this->log($lastMod);
        return $lastMod ? Carbon::parse($lastMod) : Carbon::now();
    }

    public function crawlFailed(UriInterface $url, RequestException $requestException, ?UriInterface $foundOnUrl = null): void
    {
        $this->log('Crawl failed: ' . $url);
    }

    public function finishedCrawling(): void
    {
        $this->log("Finished");
        $this->notify("Completed!", true);
    }

    public function notify($message, $completed = false, $failed = false)
    {
        $event = 'crawl-update';
        $data = [
            'website' => $this->website,
            'message' => $message,
            'numPages' => count($this->pages),
            'completed' => $completed,
            'failed' => $failed,
        ];

        if ($completed) {
            $data['pages'] = $this->pages;
        }

        if ($this->user) {
            SocketIo::forUser($event, $data, $this->user);
            return;
        }

        SocketIo::trigger($event, $data);
    }
}
