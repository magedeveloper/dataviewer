<?php
declare(strict_types=1);

namespace Aix\Dataviewer\ViewHelpers\Format;

use Aix\Dataviewer\Service\FlexForm\FlexFormService;
use Aix\Dataviewer\ViewHelpers\AbstractViewHelper;

class FlexFormToArrayViewHelper extends AbstractViewHelper
{
    /**
     * @var FlexFormService
     */
    protected $flexFormService;

    /**
     * @param FlexFormService $flexFormService
     */
    public function injectFlexFormService(FlexFormService $flexFormService)
    {
        $this->flexFormService = $flexFormService;
    }

    /**
     * Initialize arguments.
     *
     * @return void
     * @api
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('flex', 'string', 'FlexForm Input', true);
        $this->registerArgument('languagePointer', 'string', 'Language Pointer', false, 'lDEF');
        $this->registerArgument('valuePointer', 'string', 'Value Pointer', false, 'vDEF');
        parent::initializeArguments();
    }

    /**
     * Creates code from a string
     *
     * @return array
     */
    public function render(): array
    {
        if($this->arguments['flex'] == '') {
            return [];
        }

        return $this->flexFormService->convertFlexFormContentToArray($this->arguments['flex'], $this->arguments['languagePointer'], $this->arguments['valuePointer']);
    }
}