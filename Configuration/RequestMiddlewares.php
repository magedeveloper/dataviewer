<?php
declare(strict_types=1);

return [
    'backend' => [
        'aix/dataviewer/tca-generator' => [
            'target' => \Aix\Dataviewer\Tca\Generator::class,
            'after' => [
                'typo3/cms-backend/site-resolver',
            ],
        ],
        'aix/dataviewer/fieldtype-configuration-generator' => [
            'target' =>  \Aix\Dataviewer\Middleware\FieldtypeConfigurationMiddleware::class,
            'after' => [
                'aix/dataviewer/tca-generator',
            ],
        ],
    ],
    'frontend' => [
        'aix/dataviewer/tca-generator' => [
            'target' => \Aix\Dataviewer\Tca\Generator::class,
            'after' => [
                'typo3/cms-frontend/prepare-tsfe-rendering',
            ],
            'before' => [
                'typo3/cms-frontend/shortcut-and-mountpoint-redirect',
            ],
        ],
        'aix/dataviewer/fieldtype-configuration-generator' => [
            'target' => \Aix\Dataviewer\Middleware\FieldtypeConfigurationMiddleware::class,
            'after' => [
                'aix/dataviewer/tca-generator',
            ],
        ],
    ],
];
