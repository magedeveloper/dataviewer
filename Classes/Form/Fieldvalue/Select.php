<?php
namespace MageDeveloper\Dataviewer\Form\Fieldvalue;

use MageDeveloper\Dataviewer\Domain\Model\Record;
use MageDeveloper\Dataviewer\Domain\Model\Field;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
class Select extends AbstractFieldvalue implements FieldvalueInterface
{
	/**
	 * Gets the model class for the according
	 * inline elements
	 *
	 * @return string
	 */
	public function getModelClass()
	{
		return $this->getField()->getConfig("modelClass");
	}

	/**
	 * Gets the foreign table for the
	 * according inline elements
	 *
	 * @return string
	 */
	public function getForeignTable()
	{
		return $this->getField()->getConfig("foreign_table");
	}

	/**
	 * Gets an field item by a given id
	 *
	 * @param int $id
	 * @return Record|array|null
	 */
	public function getItemById($id, $table, $modelClass = null)
	{
		$item = null;
		if(!is_null($modelClass) && $modelClass !== "")
		{
			$repoClassName 	= \TYPO3\CMS\Core\Utility\ClassNamingUtility::translateModelNameToRepositoryName($modelClass);
			if($this->objectManager->isRegistered($repoClassName))
			{
				/* @var \TYPO3\CMS\Core\Resource\AbstractRepository $repository */
				$repository = $this->objectManager->get($repoClassName);

				if ($repository instanceof \TYPO3\CMS\Extbase\Persistence\Repository)
				{
					/* @var \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $model */
					$model = $repository->findByUid($id);

					if ($model instanceof $modelClass)
					{
						$item = $model;
					}
				}
			}
		}

		// Try to load an array item
		if(!$item || is_null($item))
		{
			if($table)
			{
				try
				{
					$item = BackendUtility::getRecord($table, $id, "*", BackendUtility::BEenableFields($table), true);
				}
				catch (\Exception $e) {	}
			}
			else
			{
				$item = $id;
			}
		}

		return $item;
	}

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
		$value = $this->getValue();
		$searchParams = [];

		// If the select box uses a foreign field
		if($this->getField()->getConfig("foreign"))
		{
			$foreignTable = $this->getForeignTable();

			// We check the tca configuration, if we can find the searchFields field
			if(isset($GLOBALS["TCA"][$foreignTable]["ctrl"]["searchFields"]))
			{
				$ids = GeneralUtility::trimExplode(",", $value, true);

				foreach($ids as $_id)
				{
					$searchParams[] = $_id;

					$record = BackendUtility::getRecord($foreignTable, $_id, "*", BackendUtility::BEenableFields($foreignTable));
					if(is_array($record))
					{
						$searchFields = $GLOBALS["TCA"][$foreignTable]["ctrl"]["searchFields"];
						$searchFieldsArr = GeneralUtility::trimExplode(",", $searchFields, true);
						$searchData = array_intersect_key($record, array_flip($searchFieldsArr));
						$searchParams[] = implode(" ", $searchData);
					}
				}

			}
		}
		else
		{
			$searchParams[] = $value;
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
	 * @return array|\TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject
	 */
	public function getFrontendValue()
	{
		$value = $this->getValue();
		$table = $this->getForeignTable();
		$modelClass = $this->getModelClass();
		$item = $this->getItemById($value, $table, $modelClass);

		if(!$table)
			return $value;

		return $item;
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
		$valueArr = GeneralUtility::trimExplode(",", $value, true);

		if($this->getField()->getConfig("foreign"))
		{
			$foreignTable = $this->getField()->getConfig("foreign_table");

			// We check the tca configuration, if we can find the searchFields field
			if(isset($GLOBALS["TCA"][$foreignTable]["ctrl"]["searchFields"]))
			{
				$record = BackendUtility::getRecord($foreignTable, $value, "*", BackendUtility::BEenableFields($foreignTable));
				if(is_array($record))
				{
					$searchFields = $GLOBALS["TCA"][$foreignTable]["ctrl"]["searchFields"];
					$searchFieldsArr = GeneralUtility::trimExplode(",", $searchFields, true);
					$searchData = array_intersect_key($record, array_flip($searchFieldsArr));
					$firstSearchData = reset($searchData);
					$valueArr[] = $firstSearchData;
				}
			}
		}

		return $valueArr;
	}
}
