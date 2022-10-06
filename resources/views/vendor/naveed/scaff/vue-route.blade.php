<?php
/* @var $table \Naveed\Scaff\Helpers\Table */
/* @var $gen \Naveed\Scaff\Generators\ModelGenerator */
?>
{
  path: '/{{$table->slug()}}',
  component: () => import('../views/{{$table->slug()}}/index.vue'),
  children: [
    { path: 'create', component: () => import('../views/{{$table->slug()}}/create.vue') }
  ]
},
