<?php
declare(strict_types=1);

namespace Aix\Dataviewer\ViewHelpers\Group;

use Aix\Dataviewer\Service\Query\ExtbaseQueryService;
use Aix\Dataviewer\ViewHelpers\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class RecordsByPropertyViewHelper extends AbstractViewHelper
{
    /**
     * Initialize arguments.
     *
     * @return void
     * @api
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('records', '\\Iterator', 'Records to filter', true, '');
        $this->registerArgument('property', 'string','Field Name', true, null);
        $this->registerArgument('returnOnlyGroups', 'bool', 'Return only groups', false, false);
        $this->registerArgument('multiple', 'bool', 'Groups have multiple values', false, false);
        parent::initializeArguments();
    }

    /**
     * @return array
     */
    public function render()
    {
        $records = $this->arguments['records'];
        $property = $this->arguments['property'];
        $sorted = [];

        if($records instanceof QueryResult || $records instanceof ObjectStorage) {
            if(count($records)) {
                foreach($records as $_record) {
                    if($this->arguments['multiple'] === true) {
                        $values = GeneralUtility::trimExplode(',', $_record->_getProperty($property));
                        foreach($values as $_v) {
                            $sorted[$_v][] = $_record;
                        }
                    } else {
                        $sorted[$_record->_getProperty($property)][] = $_record;
                    }

                }
            }
        } else if(is_array($records)) {
            if(count($records)) {
                foreach($records as $_record) {
                    if($this->arguments['multiple'] === true) {
                        $values = GeneralUtility::trimExplode(',', $_record->_getProperty($property));
                        foreach($values as $_v) {
                            $sorted[$_v][] = $_record;
                        }
                    } else {
                        $sorted[$_record[$property]][] = $_record;
                    }
                }
            }
        } else {
            throw new \InvalidArgumentException('Argument \'records\' given with incompatible type!');
        }

        if($this->arguments['returnOnlyGroups']) {
            $sorted = array_keys($sorted);
        }

        return $sorted;
    }
}
