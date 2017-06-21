<?php
namespace MageDeveloper\Dataviewer\Form\Fieldvalue;

use MageDeveloper\Dataviewer\Domain\Model\Field;
use MageDeveloper\Dataviewer\Utility\CheckboxUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Object\UnknownClassException;

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
class Inline extends MultiSelect
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
	 * Gets the optimized search string for the field
	 *
	 * @return string
	 */
	public function getSearch()
	{
		$value 			= $this->getValue();
		$foreignTable 	= $this->getForeignTable();

		// We check the tca configuration, if we can find the searchFields field
		if(isset($GLOBALS["TCA"][$foreignTable]["ctrl"]["searchFields"]))
		{
			$searchFields = $GLOBALS["TCA"][$foreignTable]["ctrl"]["searchFields"];
			$elements = GeneralUtility::trimExplode(",", $value, true);

			$inlineStringValue = "";
			foreach($elements as $_element)
			{
				if (!is_numeric($value) && strpos($value, "NEW") !== false)
				{
					// We found NEW12345 ID, so we can read the data directly from the post array
					$valueArr = $this->_getDirectPost($foreignTable, $_element, $searchFields);
				}
				else
				{
					$id = $_element;
					$valueArr = BackendUtility::getRecord($foreignTable, $id, $searchFields, BackendUtility::BEenableFields($foreignTable), true);
				}

				if(is_array($valueArr))
					$inlineStringValue .= implode(",", $valueArr);
			}
		}

		return $inlineStringValue;
	}

	/**
	 * Gets an information array from POST
	 *
	 * @param string $table
	 * @param string $newId
	 * @param string $searchFields
	 * @return array
	 */
	protected function _getDirectPost($table, $newId, $searchFields)
	{
		$searchFieldsArr = GeneralUtility::trimExplode(",", $searchFields, true);

		$postedData = $_POST["data"][$table][$newId];
		if(is_array($postedData))
			return array_intersect_key($postedData, array_flip($searchFieldsArr));

		return [];
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
		$items = $this->getFrontendValue();
		$stringArray = [];

		foreach($items as $_item)
		{
			if($_item instanceof AbstractEntity)
			{
				$stringArray[] = $_item->getUid();
			}

			if(is_array($_item))
			{
				$stringArray[] = $_item["uid"];
			}

		}

		return $stringArray;
	}

}
