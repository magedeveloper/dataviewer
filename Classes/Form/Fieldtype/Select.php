<?php
namespace MageDeveloper\Dataviewer\Form\Fieldtype;

use MageDeveloper\Dataviewer\Domain\Model\Field;
use MageDeveloper\Dataviewer\Domain\Model\RecordValue;
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
class Select extends AbstractFieldtype implements FieldtypeInterface
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
		$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\TcaSelectItems::class;
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
							"type" => "select",
							"renderType" => "selectSingle",
							"multiple" => 0,
							"maxitems" => 1,
							"items" => $this->getFieldItems($this->getField()),
						],
					],
				],
			],
			"inlineStructure" => [],
			"rootline" => [],
		];

		if($this->getField()->getConfig("foreign"))
			$tca["processedTca"]["columns"][$fieldName]["config"]["items"] = [];

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

		//maxitems
		if($maxitems = $this->getField()->getConfig("maxitems"))
			$tca["processedTca"]["columns"][$fieldName]["config"]["maxitems"] = $maxitems;

		//minitems
		if($minitems = $this->getField()->getConfig("minitems"))
			$tca["processedTca"]["columns"][$fieldName]["config"]["minitems"] = $minitems;

		//size
		if($size = $this->getField()->getConfig("size"))
			$tca["processedTca"]["columns"][$fieldName]["config"]["size"] = $size;

		//multiple
		if ($multiple = $this->getField()->getConfig("multiple"))
			$tca["processedTca"]["columns"][$fieldName]["config"]["multiple"] = (bool)$multiple;

		//selectedListStyle
		if($selectedListStyle = $this->getField()->getConfig("selectedListStyle"))
			$tca["processedTca"]["columns"][$fieldName]["config"]["selectedListStyle"] = $selectedListStyle;

		/* foreign is enabled */		
		if($this->getField()->getConfig("foreign"))
		{
			//foreign_table
			if($foreignTable = $this->getField()->getConfig("foreign_table"))
				$tca["processedTca"]["columns"][$fieldName]["config"]["foreign_table"] = $foreignTable;

			//foreign_table_where
			if($foreignTableWhere = $this->getField()->getConfig("foreign_table_where"))
				$tca["processedTca"]["columns"][$fieldName]["config"]["foreign_table_where"] = $foreignTableWhere;
		}

		parent::prepareTca($tca);
	}
}
