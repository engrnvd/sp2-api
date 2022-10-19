<?php

namespace App\Models;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasValidationRules;

/**
 * App\Models\Sitemap
 *
 * @property string $id
 * @property string $name
 * @property string $owner_id
 * @property boolean $is_template
 * @property boolean $archived
 * @property array $tree
 * @property array $sections
 * @property array $notes
 * @property string $created_at
 * @property string $updated_at
 * @property SitemapVersion[] $versions
 * @method static \Illuminate\Database\Query\Builder|Sitemap whereName($value)
 * @method static \Illuminate\Database\Query\Builder|Sitemap whereOwnerId($value)
 * @method static \Illuminate\Database\Query\Builder|Sitemap whereIsTemplate($value)
 * @mixin \Eloquent
 */
class Sitemap extends Model
{
    use HasValidationRules;

    protected $guarded = ["id", "created_at", "updated_at"];
    protected $casts = [
        'tree' => 'array',
        'sections' => 'array',
        'notes' => 'array',
    ];
    public static $bulkEditableFields = ['name', 'owner_id', 'is_template'];

    public static function findRequested()
    {
        $query = Sitemap::query();

        // search results based on user input
        if (\Request::get('name')) $query->where('name', 'like', '%' . request('name') . '%');
        if (\Request::get('owner_id')) $query->where('owner_id', request('owner_id'));
        if (\Request::has('is_template')) $query->where('is_template', request('is_template'));
        if (\Request::get('tree')) $query->where('tree', request('tree'));
        if (\Request::get('sections')) $query->where('sections', request('sections'));

        $user = User::current();
        if (!$user->is_admin) {
            $query->where('owner_id', $user->id);
        }

        // sort results
        if (\Request::has("sort")) $query->orderBy(request("sort"), request("sortType", "asc"));

        // paginate results
        if ($resPerPage = request("perPage"))
            return $query->paginate(intval($resPerPage));
        return $query->get();
    }

    public function validationRules(): array
    {
        return [
            "name" => "required",
            "owner_id" => "",
            "is_template" => "",
            "tree" => "",
            "sections" => "",
            "notes" => "",
        ];
    }

    public function versions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SitemapVersion::class);
    }

}
