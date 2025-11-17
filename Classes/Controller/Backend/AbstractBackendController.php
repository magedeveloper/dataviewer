<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Controller\Backend;


use Aix\Dataviewer\Factory\ClassFactory;
use TYPO3\CMS\Install\Service\ClearCacheService;

abstract class AbstractBackendController
{
    /**
     * @var ClassFactory
     */
    protected $classFactory;

    /**
     * @var ClearCacheService
     */
    protected $clearCacheService;

    /**
     * @param ClassFactory $classFactory
     */
    public function injectClassFactory(ClassFactory $classFactory)
    {
        $this->classFactory = $classFactory;
    }

    /**
     * @param ClearCacheService $clearCacheService
     */
    public function injectClearCacheService(ClearCacheService $clearCacheService)
    {
        $this->clearCacheService = $clearCacheService;
    }

    public function clearAutoloadAndCache(): void
    {
        // Dump autoload, if TYPO3 is not in composer mode
        if (!defined('TYPO3_COMPOSER_MODE')) {
            $this->classFactory->dumpAutoload();
        }

        // Clear all cache
        $this->clearCacheService->clearAll();
    }
}
