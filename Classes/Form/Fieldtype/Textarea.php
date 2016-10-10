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
class Textarea extends AbstractFieldtype implements FieldtypeInterface
{
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
							"type" => "text",
							"cols" => $this->getField()->getConfig("cols"),
							"rows" => $this->getField()->getConfig("rows"),
							"eval" => $this->getField()->getEval(),
							"placeholder" => $this->getField()->getConfig("placeholder"),
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

		parent::prepareTca($tca);
	}
}
