<?php
namespace MageDeveloper\Dataviewer\Form\Fieldtype;

use MageDeveloper\Dataviewer\Domain\Model\Field;
use MageDeveloper\Dataviewer\Domain\Model\RecordValue;
use MageDeveloper\Dataviewer\Domain\Model\FieldValue as FieldValue;

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
class Checkbox extends AbstractFieldtype implements FieldtypeInterface
{
	/**
	 * Initializes all form data providers to
	 * $this->formDataProviders
	 *
	 * Will be executed in order of the added providers!
	 *
	 * @return void
	 */
	public function initializeFormDataProviders()
	{
		$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\TcaCheckboxItems::class;
		parent::initializeFormDataProviders();
	}

	/**
	 * Gets built tca array
	 *
	 * @return array
	 */
	public function buildTca()
	{
		$fieldName 					= $this->getField()->getFieldName();
		$tableName 					= "tx_dataviewer_domain_model_record";
		$value 						= $this->getValue();
		$databaseRow 				= $this->getDatabaseRow();
		$databaseRow[$fieldName] 	= $value;

		$tca = [
			"tableName" => $tableName,
			"databaseRow" => $databaseRow,
			"fieldName" => $fieldName,
			"processedTca" => [
				"columns" => [
					$fieldName => [
						"exclude" => 1,
						"label" => $this->getField()->getFrontendLabel(),
						"config" => [
							"type" => "check",
							"items" => $this->getFieldItems($this->getField()),
						],
					],
				],
			],
			"inlineStructure" => [],
		];

		$this->prepareTca($tca);
		$this->tca = $tca;
		return $this->tca;
	}

	/**
	 * Prepares the TCA Array with
	 * the form data providers
	 *
	 * @param array $tca
	 */
	public function prepareTca(array &$tca)
	{
		$fieldName = $tca["fieldName"];

		//cols
		if($cols = $this->getField()->getConfig("cols"))
			$tca["processedTca"]["columns"][$fieldName]["config"]["cols"] = $cols;
			
		parent::prepareTca($tca);
	}

	/**
	 * Determines the value of the field
	 *
	 * @param FieldValue $fieldValue
	 * @param int $position Position of the value
	 * @return mixed
	 */
	protected function _getDefaultValue(FieldValue $fieldValue, $position = 0, $returnFull = false)
	{
		if ($fieldValue->getField()->hasDefaultValue())
		{
			$fieldValues 	= $fieldValue->getField()->getFieldValues();

			$arrForInt = [];
			foreach($fieldValues as $_fieldValue)
			{
				/* @var FieldValue $_fieldValue */
				$items = $this->getFieldValueItems($_fieldValue);

				foreach($items as $_item)
				{
					$set = 0;
					if ($_fieldValue->isDefault())
						$set = 1;

					$arrForInt[] = $set;
				}
			}
			
			$int = \MageDeveloper\Dataviewer\Utility\CheckboxUtility::getIntForSelectionArray($arrForInt);

			return $int;
		}
	}
}
