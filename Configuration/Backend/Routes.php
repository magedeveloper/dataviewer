<?php

return [
    /**
     * Main form rendering script
     */
    'record_edit' => [
        'path' => '/record/edit',
        'target' => \Aix\Dataviewer\Controller\Backend\EditDocumentController::class . '::mainAction'
    ],
];
