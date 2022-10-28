<?php

namespace App\Http\Controllers;

use App\Helpers\ApmCrawlerObserver;
use App\Models\Sitemap;
use App\Models\SitemapVersion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlProfiles\CrawlInternalUrls;

class SitemapController extends Controller
{
    public function toSitemapXml(Sitemap $sitemap): string
    {
        return $sitemap->toSitemapXml();
    }

    public function index()
    {
        return Sitemap::findRequested();
    }

    public function import(Request $request): string
    {
        $request->validate(['website' => 'url']);
        $website = \request('website');
        $user = User::current();
        $observer = new ApmCrawlerObserver($website, $user);

        dispatch(function () use ($observer, $website, $user) {
            Crawler::create()
                ->ignoreRobots()
                ->setCrawlObserver($observer)
                ->setTotalCrawlLimit(500)
                ->setCrawlProfile(new CrawlInternalUrls($website))
                ->startCrawling($website);
        })->catch(function (\Throwable $e) use ($website, $observer) {
            $observer->notify("Failed to crawl {$website}: " . $e->getMessage(), true, true);
            $observer->log("Failed to crawl {$website}: " . $e->getMessage() . "\n" . $e->getTraceAsString());
        });

        return 'started';
    }

    public function store(Request $request)
    {
        $sitemap = new Sitemap($request->all());
        $sitemap->is_template = $request->is_template ?? false;
        $sitemap->owner_id = \auth('sanctum')->user()->id;
        if (!$sitemap->tree) $sitemap->tree = [
            ['name' => 'Home', 'childIds' => []]
        ];
        if (!$sitemap->sections) $sitemap->sections = [];
        if (!$sitemap->notes) $sitemap->notes = [];
        $this->validate($request, $sitemap->getValidationRules());
        $sitemap->save();
        return $sitemap;
    }

    public function saveCommand($id)
    {
        $command = \request()->all();

        SitemapVersion::create([
            "sitemap_id" => $id,
            "user_id" => User::current()->id,
            ...$command,
        ]);

        Sitemap::where('id', $id)->update([
            'tree' => json_encode(Arr::get($command, 'payload.tree')),
            'sections' => json_encode(Arr::get($command, 'payload.sections', [])),
            'notes' => json_encode(Arr::get($command, 'payload.notes', [])),
        ]);

        return '';
    }

    public function undoCommand($id)
    {
        SitemapVersion::where('sitemap_id', $id)->orderByDesc('id')->limit(1)->delete();
        return '';
    }

    public function show(Sitemap $sitemap)
    {
        return $sitemap;
    }

    public function archive(Sitemap $sitemap)
    {
        $sitemap->archived = !$sitemap->archived;
        $sitemap->save();
        return '';
    }

    public function clone(Sitemap $sitemap)
    {
        $cloned = new Sitemap();
        $cloned->name = $sitemap->name . ' Copy';
        $cloned->tree = $sitemap->tree;
        $cloned->sections = $sitemap->sections;
        $cloned->notes = $sitemap->notes;
        $cloned->is_template = false;
        $cloned->owner_id = \auth('sanctum')->user()->id;
        $cloned->save();
        return $cloned;
    }

    public function update(Request $request, Sitemap $sitemap)
    {
        if ($request->wantsJson()) {
            $this->validateUpdatedRequest($request->name, $request->value, $sitemap);
            $data = [$request->name => $request->value];
            $sitemap->update($data);
            return $sitemap;
        }

        $this->validate($request, $sitemap->getValidationRules());
        $sitemap->update($request->all());
        return $sitemap;
    }

    public function destroy(Sitemap $sitemap)
    {
        $sitemap->delete();
        return "Sitemap deleted";
    }

    public function bulkDelete(Request $request)
    {
        $items = $request->items;
        if (!$items) {
            abort(403, "Please select some items.");
        }

        if (!$ids = collect($items)->pluck('id')->all()) {
            abort(403, "No ids provided.");
        }

        Sitemap::whereIn('id', $ids)->delete();
        return response("Deleted");
    }

    public function bulkEdit(Request $request)
    {
        if (!$field = $request->field) {
            abort(403, "Invalid request. Please provide a field.");
        }

        if (!$fieldName = Arr::get($field, 'name')) {
            abort(403, "Invalid request. Please provide a field name.");
        }

        if (!in_array($fieldName, Sitemap::$bulkEditableFields)) {
            abort(403, "Bulk editing the {$fieldName} is not allowed.");
        }

        if (!$items = $request->items) {
            abort(403, "Please select some items.");
        }

        if (!$ids = collect($items)->pluck('id')->all()) {
            abort(403, "No ids provided.");
        }

        $this->validateUpdatedRequest($fieldName, Arr::get($field, 'value'));

        Sitemap::whereIn('id', $ids)->update([$fieldName => Arr::get($field, 'value')]);
        return response("Updated");
    }

    protected function validateUpdatedRequest($field, $value, $sitemap = null)
    {
        if (!$sitemap) $sitemap = new Sitemap();
        $data = [$field => $value];
        $validator = \Validator::make($data, $sitemap->getValidationRules($field));
        if ($validator->fails()) {
            abort(403, $validator->errors()->first($field));
        }
    }

    public function findSitemap(Request $request)
    {
        $request->validate(['website' => 'url']);
        $website = \request('website');
        $email = $request->email;

        $smH = new \App\Helpers\SitemapHelper($website);
        $res = $smH->findSitemap();

        if ($email) {
            // todo: save email in db
            if ($res->ok()) {
                $smH->emailSitemap($email, $res);
                return ['message' => 'We have emailed the sitemap to you.'];
            } else {
                dispatch(function () use ($website, $email) {
                    $observer = (new class($website, null, $email) extends ApmCrawlerObserver {
                        private string $email;

                        public function __construct(string $website, User $user = null, $email = '')
                        {
                            $this->email = $email;
                            parent::__construct($website, $user);
                        }

                        public function finishedCrawling(): void
                        {
                            parent::finishedCrawling();
                            $smH = new \App\Helpers\SitemapHelper($this->website);
                            $sitemap = $smH->generateSitemap($this->pages);
                            $smH->emailSitemap($this->email, $sitemap);
                        }
                    });

                    Crawler::create()
                        ->ignoreRobots()
                        ->setCrawlObserver($observer)
                        ->setTotalCrawlLimit(500)
                        ->setCrawlProfile(new CrawlInternalUrls($website))
                        ->startCrawling($website);
                })->catch(function (\Throwable $e) use ($website) {
                    \Log::error("Failed to crawl {$website}: " . $e->getMessage() . "\n" . $e->getTraceAsString());
                });
                return ['message' => 'We will email the sitemap to you when it is ready. Crawling your website may take several minutes depending on the number of pages it has.'];
            }
        }

        if ($res->failed()) {
            return ['message' => "We could not find the sitemap for {$website}. Please enter your email so we can generate the sitemap and email it to you."];
        } else {
            return ['message' => "We found the sitemap for {$website}. Please enter your email so we can email it to you."];
        }
    }
}
