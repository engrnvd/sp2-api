<?php

namespace App\Http\Controllers;

use App\Models\Sitemap;
use App\Models\SitemapCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SitemapController extends Controller
{
    public function index()
    {
        return Sitemap::findRequested();
    }

    public function store(Request $request)
    {
        $sitemap = new Sitemap($request->all());
        $sitemap->is_template = $request->is_template ?? false;
        $sitemap->owner_id = \auth('sanctum')->user()->id;
        $sitemap->pages = [
            1 => ['id' => 1, 'name' => 'Home', 'childIds' => []]
        ];
        $sitemap->sections = json_encode([]);
        $this->validate($request, $sitemap->getValidationRules());
        $sitemap->save();
        return $sitemap;
    }

    public function saveCommand($id)
    {
        SitemapCommand::create([
            "sitemap_id" => $id,
            ...\request()->all(),
        ]);
        return '';
    }

    public function undoCommand($id)
    {
        SitemapCommand::where('sitemap_id', $id)->orderByDesc('id')->limit(1)->delete();
        return '';
    }

    public function show(Sitemap $sitemap)
    {
        $sitemap->load('commands');
        return $sitemap;
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
}
