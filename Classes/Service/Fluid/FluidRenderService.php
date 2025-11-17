<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Service\Fluid;

use Aix\Dataviewer\Fluid\View\StandaloneView;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FluidRenderService
{
    /**
     * @var StandaloneView
     */
    protected $view;

    /**
     * @return StandaloneView $view
     */
    public function getView(): StandaloneView
    {
        if(!$this->view) {
            $this->view = GeneralUtility::makeInstance(StandaloneView::class);
            $this->view->setRequest($GLOBALS['TYPO3_REQUEST']);
        }

        return $this->view;
    }

    /**
     * Renders fluid and returns the output
     * @param string $fluid
     * @param array $variables
     * @param bool $enforceString
     * @return string
     */
    public function renderFluid(string $fluid, array $variables = [], bool $enforceString = true)
    {
        $this->getView()->assignMultiple($variables);

        if($enforceString === true) {
            return (string)$this->getView()->renderSource($fluid);
        }

        return $this->getView()->renderSource($fluid);
    }
}
