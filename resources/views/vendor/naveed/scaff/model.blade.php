<?php
/* @var $table \Naveed\Scaff\Helpers\Table */
/* @var $gen \Naveed\Scaff\Generators\ModelGenerator */
?>
<?='<?php
'?>

namespace {{config('naveed-scaff.model-namespace')}};

use Illuminate\Support\Arr;
use {{config('naveed-scaff.parent-model-namespace')}};
use App\Traits\HasValidationRules;

/**
 * {{config('naveed-scaff.model-namespace')}}\{{$table->studly(true)}}
 *
@if ($table->idField)
 * {{'@'}}property string ${{$table->idField}}
@endif
@foreach ($table->fields as $field)
 * {{'@'}}property string ${{$field->name}}
@endforeach
@if ($table->timestamps)
 * {{'@'}}property string $created_at
 * {{'@'}}property string $updated_at
@endif
@foreach ( $table->fields as $field )
 * {{'@'}}method static \Illuminate\Database\Query\Builder|{{$table->studly(true)}} where{{$field->studly()}}($value)
@endforeach
 * @mixin \Eloquent
 */
class {{$table->studly(true)}} extends Model
{
    use HasValidationRules;

    protected $guarded = ["{{$table->idField}}", "created_at", "updated_at"];
    public static $bulkEditableFields = ['{!! join("', '", $gen->getBulkEditableFields($table)) !!}'];
@if (!$table->timestamps)
    public $timestamps = false;
@endif

    public static function findRequested()
    {
        $query = {{$table->studly(true)}}::query();

        // search results based on user input
@foreach ($table->fields as $field)
        if (\Request::{{$field->type === 'boolean' ? 'has' : 'get'}}('{{$field->name}}')) $query->where({!! $gen->getConditionStr($field) !!});
@endforeach

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
@foreach ($table->fields as $field)
@if($rule = $gen->getValidationRule($field))
            {!! $rule !!}
@endif
@endforeach
        ];
    }

}
