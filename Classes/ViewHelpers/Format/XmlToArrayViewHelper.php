<?php
declare(strict_types=1);

namespace Aix\Dataviewer\ViewHelpers\Format;

use Aix\Dataviewer\Utility\ArrayUtility;
use Aix\Dataviewer\ViewHelpers\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;


class XmlToArrayViewHelper extends AbstractViewHelper
{
    /**
     * Initialize arguments.
     *
     * @return void
     * @api
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('xml', 'string', 'XML Input', true);
        parent::initializeArguments();
    }

    /**
     * Creates an code from a string
     *
     * @return string
     */
    public function render(): array
    {
        if($this->arguments['xml'] == '') {
            return [];
        }

        return GeneralUtility::xml2array($this->arguments['xml']);
    }
}