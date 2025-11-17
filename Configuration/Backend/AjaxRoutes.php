<?php
return [
    // Table Routes
    'dataviewer_table_status' => [
        'path' => '/dataviewer/table/status',
        'target' => \Aix\Dataviewer\Controller\Backend\TableController::class . '::tableStatusAction'
    ],
    'dataviewer_table_migrate' => [
        'path' => '/dataviewer/table/migrate',
        'target' => \Aix\Dataviewer\Controller\Backend\TableController::class . '::tableMigrateAction'
    ],
    'dataviewer_table_delete' => [
        'path' => '/dataviewer/table/delete',
        'target' => \Aix\Dataviewer\Controller\Backend\TableController::class . '::tableDeleteAction'
    ],

    // Class Routes
    'dataviewer_class_status' => [
        'path' => '/dataviewer/class/status',
        'target' => \Aix\Dataviewer\Controller\Backend\ClassController::class . '::classStatusAction'
    ],
    'dataviewer_class_migrate' => [
        'path' => '/dataviewer/class/migrate',
        'target' => \Aix\Dataviewer\Controller\Backend\ClassController::class . '::classMigrateAction'
    ],
    'dataviewer_class_delete' => [
        'path' => '/dataviewer/class/delete',
        'target' => \Aix\Dataviewer\Controller\Backend\ClassController::class . '::classDeleteAction'
    ],

    // Querybuilder related
    'dataviewer_querybuilder_configuration_get' => [
        'path' => '/dataviewer/querybuilder/configuration/get',
        'target' => \Aix\Dataviewer\Controller\Backend\QueryBuilderController::class . '::getConfigurationAction'
    ],
];
