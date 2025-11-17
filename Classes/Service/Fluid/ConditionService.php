<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Service\Fluid;

use Aix\Dataviewer\Fluid\View\StandaloneView;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConditionService implements SingletonInterface
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
        }

        return $this->view;
    }

    /**
     * Validates a fluid condition against variables
     * @param string $condition
     * @param array $variables
     * @return bool
     */
    public function isValid(string $condition, array $variables = []): bool
    {
        if($condition == '') {
            return true;
        }

        $conditionText = "<f:if condition=\"{$condition}\">1</f:if>";
        $this->getView()->assignMultiple($variables);
        return (bool)$this->getView()->renderSource($conditionText);
    }
}