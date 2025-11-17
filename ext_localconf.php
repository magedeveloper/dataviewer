<?php
defined('TYPO3') or die();

$boot = static function (): void {

    /***********************************
     * Custom User Element for
     * running custom userFuncs directly
     ***********************************/
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry']['user'] = [
        'nodeName' => 'user',
        'priority' => '1',
        'class'    => 'Aix\\Dataviewer\\Form\\Element\\UserElement',
    ];

    /***********************************
     * Custom User Element for
     * QueryBuilder
     ***********************************/
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1606422099] = [
        'nodeName' => 'queryBuilder',
        'priority' => 40,
        'class'    => \Aix\Dataviewer\Form\Element\QueryBuilderElement::class,
    ];

    /***********************************
     * Hook when saving record
     ***********************************/
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['dataviewer'] = \Aix\Dataviewer\Hooks\DataHandling::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['dataviewer'] 	= \Aix\Dataviewer\Hooks\DataHandling::class;
    // We need to add our DataHandler on top of the processing array, to leave version management behind
    array_unshift($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'],\Aix\Dataviewer\Hooks\DataHandling::class);
    array_unshift($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'],\Aix\Dataviewer\Hooks\DataHandling::class);

    /***********************************
     * Register Cache
     * for tca storage in files
     ***********************************/
    if (!array_key_exists('dataviewer_tca_cache', $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'])) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['dataviewer_tca_cache'] = [];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['dataviewer_tca_cache']['frontend'] = \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class;
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['dataviewer_tca_cache']['backend'] = \TYPO3\CMS\Core\Cache\Backend\SimpleFileBackend::class;
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['dataviewer_tca_cache']['options']['defaultLifetime'] = 86400;
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['dataviewer_tca_cache']['groups'] = ['system'];
    }

    /***********************************
     * Registering global namespace for Dataviewer ViewHelpers
     ***********************************/
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['dv'][] = 'Aix\Dataviewer\ViewHelpers';

    /***********************************
     * Adding Page TSConfig
     ***********************************/
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
    <INCLUDE_TYPOSCRIPT: source="FILE:EXT:dataviewer/Configuration/TSConfig/mod_wizards.typoscript">
    <INCLUDE_TYPOSCRIPT: source="FILE:EXT:dataviewer/Configuration/TSConfig/mod_web_layout.typoscript">
    ');

    /***********************************
     * Dataviewer Enhancer and Aspect
     * for Routing
     ***********************************/
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['routing']['enhancers']['Dataviewer'] = \Aix\Dataviewer\Routing\Enhancer\DataviewerEnhancer::class;
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['routing']['aspects']['DataviewerMapper'] = \Aix\Dataviewer\Routing\Aspect\DataviewerMapper::class;

    /***********************************
     * Xclassing the PageRouter to
     * make full access to
     * DataviewerEnhancer and DataviewerMapper
     * for better routing
     ***********************************/
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Core\Routing\PageRouter::class] = [
        'className' => Aix\Dataviewer\Xclass\Core\Routing\PageRouter::class
    ];

    /***********************************
     * Dataviewer Plugins
     ***********************************/
    // #1 - List Records
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Dataviewer',
        'List',
        [
            \Aix\Dataviewer\Controller\RecordController::class => 'list',
        ],
        [
            \Aix\Dataviewer\Controller\RecordController::class => 'list',
        ],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_PLUGIN
    );

    // #2 - Detail view for a record
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Dataviewer',
        'Detail',
        [
            \Aix\Dataviewer\Controller\RecordController::class => 'detail',
        ], // Cached
        [
            \Aix\Dataviewer\Controller\RecordController::class => 'detail',
        ], // UnCached
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_PLUGIN
    );

    // #3 - Dynamic Detail view listener for a record
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Dataviewer',
        'Dynamic',
        [
            \Aix\Dataviewer\Controller\RecordController::class => 'dynamicDetail',
        ], // Cached
        [
            \Aix\Dataviewer\Controller\RecordController::class => 'dynamicDetail',
        ], // UnCached
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_PLUGIN
    );

    // #4 - Plain fluid view
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Dataviewer',
        'Plain',
        [
            \Aix\Dataviewer\Controller\RecordController::class => 'plain',
        ], // Cached
        [
            \Aix\Dataviewer\Controller\RecordController::class => 'plain',
        ], // UnCached
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_PLUGIN
    );


};

$boot();
unset($boot);


