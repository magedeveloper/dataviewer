<?php
declare(strict_types=1);

namespace Aix\Dataviewer\ViewHelpers\Backend\Render;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

class RecordViewHelper extends AbstractRenderViewHelper
{
    /**
     * Arguments initialization
     * @throws Exception
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('uid', 'int', 'Uid of the record', true);
        $this->registerArgument('table', 'string', 'Name of the table', true);
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $table = $this->arguments['table'];
        $record = BackendUtility::getRecordWSOL($table,$this->arguments['uid']);

        $string = '';
        if(is_array($record) && !empty($record)) {
            $labelField = $GLOBALS['TCA'][$table]['ctrl']['label'];

            $string .= $this->iconFactory->getIconForRecord($table,$record,Icon::SIZE_SMALL)->render();
            $string .= ' ';
            $string .= '['.$record['uid'].']'.' '.$record[$labelField];
        } else {
            $string .= $table.':'.$this->arguments['uid'].' not found!';
        }

        return $string;
    }
}
