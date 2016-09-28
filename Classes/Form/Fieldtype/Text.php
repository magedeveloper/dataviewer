<?php
namespace MageDeveloper\Dataviewer\Form\Fieldtype;

use MageDeveloper\Dataviewer\Domain\Model\Field;
use MageDeveloper\Dataviewer\Domain\Model\RecordValue;
use TYPO3\CMS\Backend\Form\Container\SingleFieldContainer;

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
class Text extends AbstractFieldtype implements FieldtypeInterface
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
		$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInputPlaceholders::class;
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
							"type" => "input",
							"size" => 30,
							"eval" => $this->getField()->getEval(),
							"placeholder" => $this->getField()->getConfig("placeholder"),
							"wrap" => $this->getField()->getConfig("wrap"),
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

		if($max = $this->getField()->getConfig("max"))
			$tca["processedTca"]["columns"][$fieldName]["config"]["max"] = $max;

		//is_in
		if($isIn = $this->getField()->hasEval("is_in"))
			$tca["processedTca"]["columns"][$fieldName]["config"]["is_in"] = $isIn;

		//range
		if($rangeLower = $this->getField()->hasEval("range"))
			$tca["processedTca"]["columns"][$fieldName]["config"]["range"] = ["lower" => $this->getField()->getConfig("range_lower"), 
			                                                                  "upper" => $this->getField()->getConfig("range_upper")];

		parent::prepareTca($tca);
	}

}
