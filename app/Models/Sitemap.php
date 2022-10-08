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
 * @property string $is_template
 * @property string $created_at
 * @property string $updated_at
 * @method static \Illuminate\Database\Query\Builder|Sitemap whereName($value)
 * @method static \Illuminate\Database\Query\Builder|Sitemap whereOwnerId($value)
 * @method static \Illuminate\Database\Query\Builder|Sitemap whereIsTemplate($value)
 * @mixin \Eloquent
 */
class Sitemap extends Model
{
    use HasValidationRules;

    protected $guarded = ["id", "created_at", "updated_at"];
    public static $bulkEditableFields = ['name', 'owner_id', 'is_template'];

    public static function findRequested()
    {
        $query = Sitemap::query();

        // search results based on user input
        if (\Request::get('name')) $query->where('name', 'like', '%' . request('name') . '%');
        if (\Request::get('owner_id')) $query->where('owner_id', request('owner_id'));
        if (\Request::has('is_template')) $query->where('is_template', request('is_template'));

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
        ];
    }

}
