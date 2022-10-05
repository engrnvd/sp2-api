<?php
/* @var $table \Naveed\Scaff\Helpers\Table */
/* @var $gen \Naveed\Scaff\Generators\MigrationGenerator */
?>
<?="<?php\n"
?>

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Create{{$table->studly()}}Table extends Migration
{
    public function up()
    {
        Schema::create('{{$table->name}}', function (Blueprint $table) {
@if ($table->idField)
            {!! $gen->getLineForID() !!}
@endif
@foreach($table->fields as $field)
            {!! $gen->getMigrationLine($field) !!}
@if($field->unique)
            $table->unique('{{$field->name}}');
@elseif($field->index)
            $table->index('{{$field->name}}');
@endif
@endforeach
@if ($table->timestamps)
            $table->timestamps();
@endif
        });
    }

    public function down()
    {
        Schema::dropIfExists('{{$table->name}}');
    }
}
