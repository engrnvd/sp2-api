<?php
/* @var $table \Naveed\Scaff\Helpers\Table */
/* @var $gen \Naveed\Scaff\Generators\ModelGenerator */
?>
<?='<?php
'?>

namespace {{config('naveed-scaff.controller-namespace')}};

use {{config('naveed-scaff.model-namespace')}}\{{$table->studly(true)}};
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class {{$table->studly(true)}}Controller extends Controller
{
    public function index(Request $request)
    {
        return {{$table->studly(true)}}::findRequested();
    }

    public function store(Request $request)
    {
        ${{$table->camel(true)}} = new {{$table->studly(true)}}($request->all());
        $this->validate($request, ${{$table->camel(true)}}->validationRules());
        ${{$table->camel(true)}}->save();
        return ${{$table->camel(true)}};
    }

    public function show(Request $request, {{$table->studly(true)}} ${{$table->camel(true)}})
    {
        return ${{$table->camel(true)}};
    }

    public function update(Request $request, {{$table->studly(true)}} ${{$table->camel(true)}})
    {
        if ($request->wantsJson()) {
            $this->validateUpdatedRequest($request->name, $request->value, ${{$table->camel(true)}});
            $data = [$request->name => $request->value];
            ${{$table->camel(true)}}->update($data);
            return ${{$table->camel(true)}};
        }

        $this->validate($request, ${{$table->camel(true)}}->validationRules());
        ${{$table->camel(true)}}->update($request->all());
        return ${{$table->camel(true)}};
    }

    public function destroy(Request $request, {{$table->studly(true)}} ${{$table->camel(true)}})
    {
        ${{$table->camel(true)}}->delete();
        return "{{$table->title(true)}} deleted";
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

        {{$table->studly(true)}}::whereIn('id', $ids)->delete();
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

        if (!in_array($fieldName, {{$table->studly(true)}}::$bulkEditableFields)) {
            abort(403, "Bulk editing the {$fieldName} is not allowed.");
        }

        if (!$items = $request->items) {
            abort(403, "Please select some items.");
        }

        if (!$ids = collect($items)->pluck('id')->all()) {
            abort(403, "No ids provided.");
        }

        $this->validateUpdatedRequest($fieldName, Arr::get($field, 'value'));

        {{$table->studly(true)}}::whereIn('id', $ids)->update([$fieldName => Arr::get($field, 'value')]);
        return response("Updated");
    }

    protected function validateUpdatedRequest($field, $value, ${{$table->camel(true)}} = null)
    {
        if (!${{$table->camel(true)}}) ${{$table->camel(true)}} = new {{$table->studly(true)}}();
        $data = [$field => $value];
        $validator = \Validator::make($data, ${{$table->camel(true)}}->validationRules($field));
        if ($validator->fails()) {
            abort(403, $validator->errors()->first($field));
        }
    }
}
