<?php
declare(strict_types=1);

namespace Aix\Dataviewer\ViewHelpers\Backend;

use Closure;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * ViewHelper to create a link to edit a note
 * @internal
 */
class NewLinkViewHelper extends AbstractLinkViewHelper
{
    /**
     * Arguments initialization
     *
     * @return void
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('pid', 'int', 'The target pid', true);
        $this->registerArgument('table', 'string', 'Name of the table', true);
        $this->registerArgument('id', 'int', 'The target id', false);
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
                'pid'       => $this->arguments["pid"],
                'table'     => $this->arguments["table"],
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
     * @return string
     * @throws RouteNotFoundException
     */
    public static function renderStatic(array $arguments, Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): string
    {
        $urlParameters = [
            'edit[' . $arguments['table'] . '][' . $arguments['pid'] . ']' => 'new',
            'returnUrl'                                                    => (isset($arguments["returnUrl"])) ? $arguments["returnUrl"] : GeneralUtility::getIndpEnv('REQUEST_URI'),
        ];

        if (isset($arguments["id"]) && $arguments["id"] > 0) {
            $urlParameters["id"] = $arguments["id"];
        }

        return self::getModuleUrl(
            'record_edit',
            $urlParameters
        );
    }
}
