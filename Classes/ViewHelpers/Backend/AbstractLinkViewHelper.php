<?php
declare(strict_types=1);

namespace Aix\Dataviewer\ViewHelpers\Backend;

use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper;

/**
 * ViewHelper to create a link to edit a note
 */
class AbstractLinkViewHelper extends AbstractBackendViewHelper
{
    /**
     * Returns the URL to a given module
     *
     * @param string $moduleName Name of the module
     * @param array $urlParameters URL parameters that should be added as key value pairs
     * @return string Calculated URL
     * @throws RouteNotFoundException
     */
    public static function getModuleUrl(string $moduleName, array $urlParameters = []): string
    {
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        try {
            $uri = $uriBuilder->buildUriFromRoute($moduleName, $urlParameters);
        } catch (RouteNotFoundException $e) {
            $uri = $uriBuilder->buildUriFromRoutePath($moduleName, $urlParameters);
        }
        return (string)$uri;
    }
}
