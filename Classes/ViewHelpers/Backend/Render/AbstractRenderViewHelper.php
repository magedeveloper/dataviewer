<?php
declare(strict_types=1);

namespace Aix\Dataviewer\ViewHelpers\Backend\Render;

use Aix\Dataviewer\ViewHelpers\AbstractViewHelper;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

abstract class AbstractRenderViewHelper extends AbstractViewHelper
{
    /**
     * Specifies whether the escaping interceptors should be disabled or enabled for the render-result of this ViewHelper
     * @see isOutputEscapingEnabled()
     *
     * @var boolean
     * @api
     */
    protected $escapeOutput = false;

    /**
     * @var IconFactory
     */
    protected $iconFactory;

    /**
     * @param IconFactory $iconFactory
     */
    public function injectIconFactory(IconFactory $iconFactory)
    {
        $this->iconFactory = $iconFactory;
    }

    /**
     * Arguments initialization
     * @throws Exception
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
    }

}
