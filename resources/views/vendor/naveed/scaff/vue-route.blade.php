<?php
/* @var $table \Naveed\Scaff\Helpers\Table */
/* @var $gen \Naveed\Scaff\Generators\ModelGenerator */
?>
        {
            path: '{{$table->slug()}}',
            component: () => import(/* webpackChunkName: "{{$table->slug()}}" */ '../views/app/{{$table->slug()}}'),
        },
