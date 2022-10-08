<?php

$frontendBaseDir = base_path('frontend/sp2-admin/src/');

return [

    /*
     * Directory containing the templates
     * If you want to use your custom templates, specify them here
     * */

    'templates' => 'vendor.naveed.scaff',

    /*
     * Namespace to generate the models in
     * the directory will be dynamically determined using the namespace
     * */

    'model-namespace' => "App\Models",

    /*
     * Namespace to of the parent model which the generated model will extend
     * */

    'parent-model-namespace' => "Illuminate\Database\Eloquent\Model",

    /*
     * Namespace to generate the controllers in
     * the directory will be dynamically determined using the namespace
     * */

    'controller-namespace' => "App\Http\Controllers",

    /*
     * Fields that should be skipped on the listing page
     * */

    'skipped-fields' => ['created_at', 'updated_at', 'password', 'token', 'remember_token'],

    /*
     * All the crud routes will be inserted in a separate file called crud-routes.php
     * and the file will be included in the following file
     * */

    'routes-file' => base_path('routes') . "/api.php",

    /*
     * Where to store the views
     * */
    'views-directory' => $frontendBaseDir . 'views/',

    /*
     * What views to generate
     * */
    'views' => ['index.vue', 'create.vue', 'store.ts'],

    /*
     * If you want some extra code to be generated in some existing files, this is where you configure that
     * filename: The file in which the code will be inserted
     * template: the template file to be used as a stub for code generation
     * identifier: the generator will look for this identifier string and put the content before it
     * */
    'extra-entries' => [
        [
            'filename' => $frontendBaseDir . 'router/index.ts',
            'template' => 'vue-route',
            'identifier' => '// vue routes generated here. Do not remove this line.',
        ],
    ],
];
