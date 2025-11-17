<?php
declare(strict_types=1);

namespace Aix\Dataviewer\ViewHelpers\String;

use Aix\Dataviewer\Utility\StringUtility;
use Aix\Dataviewer\ViewHelpers\AbstractViewHelper;


class CodeFromStringViewHelper extends AbstractViewHelper
{
    /**
     * Initialize arguments.
     *
     * @return void
     * @api
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('string', 'string', 'String to create code from', true, '');
        parent::initializeArguments();
    }

    /**
     * Creates code from a string
     *
     * @return string
     */
    public function render(): string
    {
        $string = $this->arguments['string'];
        return StringUtility::createCodeFromString($string);
    }
}