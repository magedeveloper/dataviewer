<?php
declare(strict_types=1);

namespace Aix\Dataviewer\ViewHelpers\String;

use Aix\Dataviewer\ViewHelpers\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;


class ExplodeViewHelper extends AbstractViewHelper
{
    /**
     * Initialize arguments.
     *
     * @return void
     * @api
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('string', 'string', 'String to explode', true, '');
        $this->registerArgument('delimeter', 'string', 'Delimeter', false, ',');
        $this->registerArgument('removeEmptyValues', 'bool', 'Remove empty values', false, true);
        $this->registerArgument('limit', 'int', 'Limit', false, 0);
        parent::initializeArguments();
    }

    /**
     * Explodes an string
     *
     * @return array
     */
    public function render(): array
    {
        return GeneralUtility::trimExplode($this->arguments['delimeter'], $this->arguments['string'], $this->arguments['removeEmptyValues'], $this->arguments['limit']);
    }
}