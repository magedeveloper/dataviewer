<?php
declare(strict_types=1);

namespace Aix\Dataviewer\ViewHelpers\Array;

use Aix\Dataviewer\ViewHelpers\AbstractViewHelper;

class KeyValuePairViewHelper extends AbstractViewHelper
{
    /**
     * Initialize arguments.
     *
     * @return void
     * @api
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('key', 'string', 'Key to use for array creation', true);
        $this->registerArgument('value', 'mixed', 'Value to use for array creation', true);
        parent::initializeArguments();
    }

    /**
     * Creates a custom array with key/value pair
     * @return array
     */
    public function render(): array
    {
        return [$this->arguments['key'] => $this->arguments['value']];
    }
}
