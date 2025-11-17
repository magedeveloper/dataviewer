<?php
declare(strict_types=1);

namespace Aix\Dataviewer\ViewHelpers\Backend;

use Closure;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

/**
 * ViewHelper to create a link to the list module
 * @internal
 */
class WebListViewHelper extends AbstractLinkViewHelper
{
    /**
     * Arguments initialization
     *
     * @throws Exception
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('id', 'int', 'Id of the link', true);
        $this->registerArgument('returnUrl', 'string', 'The return url', false);
    }


    /**
     * @return string
     * @throws RouteNotFoundException
     */
    public function render(): string
    {
        return static::renderStatic(
            [
                'id'        => $this->arguments["id"],
                'returnUrl' => $this->arguments["returnUrl"],
            ],
            $this->buildRenderChildrenClosure(),
            $this->renderingContext
        );
    }

    /**
     * @param array $arguments
     * @param Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     *
     * @return string
     * @throws RouteNotFoundException
     */
    public static function renderStatic(array $arguments, Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): string
    {
        return self::getModuleUrl(
            'web_list',
            [
                'id'        => $arguments['id'],
                'returnUrl' => (isset($arguments["returnUrl"])) ? $arguments["returnUrl"] : GeneralUtility::getIndpEnv('REQUEST_URI'),
            ]
        );
    }
}
