<?php
namespace MageDeveloper\Dataviewer\Form\Fieldvalue;

use MageDeveloper\Dataviewer\Domain\Model\Record;
use MageDeveloper\Dataviewer\Domain\Model\Field;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
class Group extends Select
{
	/**
	 * Gets the optimized value for the field
	 *
	 * @return string
	 */
	public function getValueContent()
	{
		return $this->getValue();
	}

	/**
	 * Gets solved values and creates an
	 * value array with the following
	 * keys
	 * 
	 * table 	=> Name of the according table
	 * uid		=> The Uid of the according record
	 * 
	 * @return array
	 */
	protected function _getSolvedValues()
	{
		$value = $this->getValue();
		$values = GeneralUtility::trimExplode(",", $value, true);
		$solved = [];

		foreach($values as $_value)
		{
			preg_match('/(?<table>.*)_(?<uid>[0-9]{0,11})/', $_value, $match);

			if($match["table"] && $match["uid"])
			{
				$table = $match["table"];
				$uid = $match["uid"];

				$solved[] = [
					"table" => $table,
					"uid" => $uid,
				];
			}
			else
			{
				// Unique allowed with unique id
				if(is_numeric($_value) && !strpos($this->getField()->getConfig("allowed"), ","))
				{
					$solved[] = [
						"table" => $this->getField()->getConfig("allowed"),
						"uid" => $_value,
					];
				}
			}
		}
	
		return $solved;
	}
	
	/**
	 * Gets the optimized search string for the field
	 *
	 * @return string
	 */
	public function getSearch()
	{
		$solvedValues = $this->_getSolvedValues();
		$searchParams = [];
		
		foreach($solvedValues as $_value)
		{
			$table = $_value["table"];
			$uid = $_value["uid"];

			// We check the tca configuration, if we can find the searchFields field
			if(isset($GLOBALS["TCA"][$table]["ctrl"]["searchFields"]))
			{
				$record 		= BackendUtility::getRecord($table, $uid);
				if(is_array($record))
				{
					$searchFields = $GLOBALS["TCA"][$table]["ctrl"]["searchFields"];
					$searchFieldsArr = GeneralUtility::trimExplode(",", $searchFields, true);
					$searchData = array_intersect_key($record, array_flip($searchFieldsArr));
					$searchParams[] = implode(" ", $searchData);
				}
			}
		}

		return implode(",", $searchParams);
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
		$solvedValues = $this->_getSolvedValues();
		$modelClass = $this->getField()->getConfig("modelClass");
		$valueArr = [];

		foreach($solvedValues as $_value) 
		{
			$table = $_value["table"];
			$uid = $_value["uid"];
			
			if($modelClass)
				$record = $this->getItemById($uid, $table, $modelClass);
			else
				$record = BackendUtility::getRecord($table, $uid);
		
			if($record instanceof Record || is_array($record))
				$valueArr[] = $record;
		}
		
		return $valueArr;
	}

	/**
	 * Gets the value or values as a plain string-array for
	 * usage in different possitions to show
	 * and use it when needed as a string
	 *
	 * @return array
	 */
	public function getValueArray()
	{
		$value = $this->getValue();
		$values = GeneralUtility::trimExplode(",", $value, true);
		$valueArr = [];

		$match = [];
		foreach($values as $_value)
		{
			preg_match('/(?<table>.*)_(?<id>[0-9]{0,11})/', $_value, $match);

			if($match["table"] && $match["id"])
			{
				$table = $match["table"];
				$id = $match["id"];


				// We check the tca configuration, if we can find the searchFields field
				if(isset($GLOBALS["TCA"][$table]["ctrl"]["searchFields"]))
				{
					$record = BackendUtility::getRecord($table, $id);
					if(is_array($record))
					{
						$searchFields = $GLOBALS["TCA"][$table]["ctrl"]["searchFields"];
						$searchFieldsArr = GeneralUtility::trimExplode(",", $searchFields, true);
						$searchData = array_intersect_key($record, array_flip($searchFieldsArr));
						$firstSearchData = reset($searchData);
						$valueArr[] = $firstSearchData;
					}

				}

			}
		}

		return $valueArr;
	}
}
