<?php
namespace MageDeveloper\Dataviewer\Form\Fieldtype;

use MageDeveloper\Dataviewer\Domain\Model\Field;
use MageDeveloper\Dataviewer\Domain\Model\RecordValue;

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
class Group extends AbstractFieldtype implements FieldtypeInterface
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
		$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\TcaColumnsProcessFieldLabels::class;
		$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\TcaGroup::class;
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
						"exclude" => (int)$this->getField()->isExclude(),
						"label" => $this->getField()->getFrontendLabel(),
						"config" => [
							"type" => "group",
							"internal_type" => $this->getField()->getConfig("internal_type"),
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

		//maxitems
		if($maxitems = $this->getField()->getConfig("maxitems"))
			$tca["processedTca"]["columns"][$fieldName]["config"]["maxitems"] = $maxitems;

		//minitems
		if($minitems = $this->getField()->getConfig("minitems"))
			$tca["processedTca"]["columns"][$fieldName]["config"]["minitems"] = $minitems;

		//show_thumbs
		$tca["processedTca"]["columns"][$fieldName]["config"]["show_thumbs"] = (int)$this->getField()->getConfig("show_thumbs");

		//size
		if($size = $this->getField()->getConfig("size"))
			$tca["processedTca"]["columns"][$fieldName]["config"]["size"] = $size;

		//multiple
		if ($multiple = $this->getField()->getConfig("multiple"))
			$tca["processedTca"]["columns"][$fieldName]["config"]["multiple"] = (bool)$multiple;

		//selectedListStyle
		if($selectedListStyle = $this->getField()->getConfig("selectedListStyle"))
			$tca["processedTca"]["columns"][$fieldName]["config"]["selectedListStyle"] = $selectedListStyle;

		//allowed
		if($allowed = $this->getField()->getConfig("allowed"))
			$tca["processedTca"]["columns"][$fieldName]["config"]["allowed"] = $allowed;

		//disallowed
		if($disallowed = $this->getField()->getConfig("disallowed"))
			$tca["processedTca"]["columns"][$fieldName]["config"]["disallowed"] = $disallowed;

		//max_size
		if($max_size = $this->getField()->getConfig("max_size"))
			$tca["processedTca"]["columns"][$fieldName]["config"]["max_size"] = $max_size;

		//uploadfolder
		if($uploadfolder = $this->getField()->getConfig("uploadfolder"))
			$tca["processedTca"]["columns"][$fieldName]["config"]["uploadfolder"] = $uploadfolder;

		//hideMoveIcons
		if($hideMoveIcons = $this->getField()->getConfig("hideMoveIcons"))
			$tca["processedTca"]["columns"][$fieldName]["config"]["hideMoveIcons"] = (bool)$hideMoveIcons;

		//disable_controls
		if($disable_controls = $this->getField()->getConfig("disable_controls"))
			$tca["processedTca"]["columns"][$fieldName]["config"]["disable_controls"] = $disable_controls;

		//foreign_table
		if($foreignTable = $this->getField()->getConfig("foreign_table"))
			$tca["processedTca"]["columns"][$fieldName]["config"]["foreign_table"] = $foreignTable;

		// internal_type == db
		if($this->getField()->getConfig("internal_type") == "db")
		{
			// Suggest Wizard
			if((bool)$this->getField()->getConfig("suggest_wizard") == true)
				$tca["processedTca"]["columns"][$fieldName]["config"]["wizards"] = [
					"suggest" => [
						"type" => "suggest",
					],
				];
		}	

		parent::prepareTca($tca);
	}
}
