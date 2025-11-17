<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Routing\Enhancer;

use TYPO3\CMS\Core\Routing\Route;
use TYPO3\CMS\Extbase\Routing\ExtbasePluginEnhancer;

class DataviewerEnhancer extends ExtbasePluginEnhancer
{
    /**
     * Target Page Id
     * @var int
     */
    protected $pageId;

    public function __construct(array $configuration)
    {
        $configuration['extension'] = 'Dataviewer';
        $configuration['plugin'] = 'Dynamic';
        parent::__construct($configuration);
    }

    /**
     * @return int
     */
    public function getPageId(): int
    {
        return $this->pageId;
    }

    /**
     * @param int $pageId
     */
    public function setPageId(int $pageId): void
    {
        $this->pageId = $pageId;
    }

    /**
     * Check if controller+action combination matches
     *
     * @param Route $route
     * @param array $parameters
     * @return bool
     */
    protected function verifyRequiredParameters(Route $route, array $parameters): bool
    {
        // We modify this method a bit to validate, if the requested page id matches the targetPages setting
        if(array_key_exists('targetPages', $this->configuration) && is_array($this->configuration['targetPages']) && in_array($this->pageId, $this->configuration['targetPages'])) {
            return parent::verifyRequiredParameters($route, $parameters);
        }

        return false;
    }

}