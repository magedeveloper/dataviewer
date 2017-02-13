<?php
namespace MageDeveloper\Dataviewer\ViewHelpers\Filter;

use MageDeveloper\Dataviewer\ViewHelpers\AbstractViewHelper;
use TYPO3\CMS\Extbase\Object\InvalidObjectException;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * This ViewHelpers filters given records by a field value condition
 * Usage:
 * 
 * {dv:filter.records(records:movies,field:'length',value:'120',condition:'gte')}
 */

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
class RecordsByFieldViewHelper extends AbstractFilterViewHelper
{
	/**
	 * Initialize arguments.
	 *
	 * @return void
	 */
	public function initializeArguments()
	{
		$this->registerArgument("records", "mixed", "Records to filter through", false, null);
	}


	/**
	 * Gets a configuration setting
	 *
	 * @param mixed $field Name of the field, Id of the field or the field itself
	 * @param string $value
	 * @param string $condition The Sorting Condition
	 * @param string $sortField The Sort Field
	 * @param string $sortOrder Sort Order
	 * @throws \TYPO3\CMS\Extbase\Object\InvalidObjectException
	 * @return array
	 */
	public function render($field, $value, $condition = "eq", $sortField = "title", $sortOrder = QueryInterface::ORDER_ASCENDING)
	{
		if(!is_numeric($field))
		{
			if($field instanceof \MageDeveloper\Dataviewer\Domain\Model\Field)
				$fieldModel = $field;
			else
				$fieldModel = $this->fieldRepository->findOneByVariableName($field);
		}
		else
			$fieldModel = $this->fieldRepository->findByUid($field, false);

		if(!$fieldModel instanceof \MageDeveloper\Dataviewer\Domain\Model\Field)
			throw new InvalidObjectException("Field not found!");
			
		$records = $this->arguments["records"];	
		if(is_null($records) || $records = "") $records = [];
		if(!$records instanceof QueryResult && !is_array($records))
			throw new InvalidObjectException("Records not traversable. Either needs to be array or instance of TYPO3\\CMS\\Extbase\\Persistence\\Generic\\QueryResult");
			
			
		$uids = []; $storagePids = [];
		foreach($records as $_record) {
			$pid = $_record->getPid();
			$uids[] = $_record->getUid();
			$storagePids[md5($pid)] = $pid;
		}

		$filters = [];

		if(!empty($uids))
		{
			// Adding the record uids to the filter
			
			$filters[] = [ // A filter for selecting only the records that we got here
				"field_id" => "RECORD.uid",
				"filter_condition" => "in",
				"field_value" => implode(",", $uids),
				"filter_combination" => "AND",
				"filter_field" => "search",
			];
		}

		// Main filter for the field
		$filters[] = [
				"field_id" => $fieldModel->getUid(),
				"filter_condition" => $condition,
				"field_value" => $value,
				"filter_combination" => "AND",
				"filter_field" => "search",
		];

		$validRecords = $this->recordRepository->findByAdvancedConditions($filters, $sortField, $sortOrder, null, $storagePids);
		$validRecordIds = array_column($validRecords, "uid");
		$storagePids = array_column($validRecords, "pid");

		$records = [];
		if(!empty($validRecordIds))
			$records = $this->recordRepository->findByRecordIds($validRecordIds, $storagePids);
		
		return $records;
	}
}
