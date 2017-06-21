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
class RecordsByConditionsViewHelper extends AbstractFilterViewHelper
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
	 * Gets records by given conditions
	 *
	 * @param array $conditions Conditions
	 * @param string $sortField The Sort Field
	 * @param string $sortOrder Sort Order
	 * @throws \TYPO3\CMS\Extbase\Object\InvalidObjectException
	 * @return array
	 */
	public function render(array $conditions, $sortField = "title", $sortOrder = QueryInterface::ORDER_ASCENDING)
	{
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
		
		$filters = array_merge($filters, $conditions);

		$validRecords = $this->recordRepository->findByAdvancedConditions($filters, $sortField, $sortOrder, null, $storagePids);
		return $validRecords;
	}
}
