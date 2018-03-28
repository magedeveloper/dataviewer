<?php
namespace MageDeveloper\Dataviewer\Form\Fieldvalue;

use MageDeveloper\Dataviewer\Domain\Model\Record;
use MageDeveloper\Dataviewer\Domain\Model\Field;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;

/**
 * MageDeveloper Dataviewer Extension
 * -----------------------------------
 *
 * @category    TYPO3 Extension
 * @package     MageDeveloper\Dataviewer
 * @author		Bastian Zagar
 * @copyright   Magento Developers / magedeveloper.de <kontakt@magedeveloper.de>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MultiSelect extends Select
{
    /**
     * Record Repository
     *
     * @var \MageDeveloper\Dataviewer\Domain\Repository\RecordRepository
     * @inject
     */
    protected $recordRepository;

    /**
     * Gets the database connection model
     *
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function _getDatabaseConnection()
    {
        return $GLOBALS["TYPO3_DB"];
    }

    /**
     * Gets the order by selection for the
     * according elements
     *
     * @return string
     */
    public function getSortField()
    {
        return $this->getField()->getConfig("sort_field");
    }

    /**
     * Gets the order direction for the
     * according elements
     *
     * @return string
     */
    public function getSortOrder()
    {
        return $this->getField()->getConfig("sort_order");
    }

    /**
     * Gets the where clause for the
     * foreign table
     *
     * @return string
     */
    public function getForeignTableWhere()
    {
        return $this->getField()->getConfig("foreign_table_where");
    }

    /**
     * Gets the final frontend value, that is
     * pushed in {record.field.value}
     *
     * This or these values are the most different
     * part of the whole output, so if you handle
     * this, you need to have some knowledge,
     * what value is returned.
     *
     * @return array
     */
    public function getFrontendValue()
    {
        $value = $this->getValue();
        $ids = GeneralUtility::trimExplode(",", $value, true);
        $table = $this->getForeignTable();
        $modelClass = $this->getModelClass();
        $sortField = $this->getSortField();
        $sortOrder = $this->getSortOrder();
        $foreignTableWhere = $this->getForeignTableWhere();

        $items = [];

        if(count($ids)) {

            /* @var DataMapper $dataMapper */
            $canMapData = false;
            $dataMapper = null;
            if (!is_null($modelClass) && $modelClass !== "") {
                if ($this->objectManager->isRegistered($modelClass)) {
                    $dataMapper = GeneralUtility::makeInstance(DataMapper::class);
                    $canMapData = true;
                }
            }

            // We need to decide for what the datamapper is now used.
            // if we need a sorting of records by recordvalues, we need to create a custom query
            if(is_numeric($sortField) && $modelClass == \MageDeveloper\Dataviewer\Domain\Model\Record::class) {
                // When the selection of the sortField is numeric, we can suggest, it is a dataviewer-field id,
                // and therefore, we have to create our custom query to obtain sorted records
                /* @var \MageDeveloper\Dataviewer\Domain\Repository\RecordRepository $recordRepository */
                $recordRepository = $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\RecordRepository::class);

                $conditions = [
                    [
                        "field_id"  => "RECORD.uid",
                        "filter_condition" => "in",
                        "field_value" => $value,
                        "filter_combination" => "AND",
                        "filter_field" => "value_content",
                    ],
                ];

                $items = $recordRepository->findByAdvancedConditions($conditions, $sortField, $sortOrder);
            } else {
                // Any other sort field was selected, so we need to create a custom query for our results and
                // maybe finally can add a datamap over the results

                $sorting = $sortField." ".$sortOrder;
                if($sortField == "" || !$sortField) {
                    $sorting = "FIELD(uid, {$value}) {$sortOrder}";
                }

                $items = $this->_getDatabaseConnection()->exec_SELECTgetRows("*", $table, "uid IN ({$value}) {$foreignTableWhere}", "", $sorting,"");

                // We try to add a datamap over the results
                if ($canMapData === true && is_array($items)) {
                    $items = $dataMapper->map($modelClass, $items);
                }
            }

        }

        return $items;
    }

}
