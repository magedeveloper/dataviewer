<?php
return [
    // required import configurations of other extensions,
    // in case a module imports from another package
    'dependencies' => [
        'core',
        'backend',
    ],
    'tags' => [
        'backend.form',
        'backend.module',
        'backend.navigation-component',
        'backend.contextmenu',
    ],
    'imports' => [
        // recursive definiton, all *.js files in this folder are import-mapped
        // trailing slash is required per importmap-specification
        '@aix/dataviewer/' => [
            'path' => 'EXT:dataviewer/Resources/Public/JavaScript/',
            'exclude' => [
                'EXT:dataviewer/Resources/Public/JavaScript/Contrib/',
            ],
        ],
        'jquery-extendext' => 'EXT:dataviewer/Resources/Public/JavaScript/Contrib/jquery-extendext.js',
        'query-builder' => 'EXT:dataviewer/Resources/Public/JavaScript/Contrib/query-builder.js',
        'query-builder-templates' => 'EXT:dataviewer/Resources/Public/JavaScript/QueryBuilderTemplates.js',
    ],
];
