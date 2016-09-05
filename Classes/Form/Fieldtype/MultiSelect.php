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
class MultiSelect extends Select
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

		$tca = array(
			"tableName" => $tableName,
			"databaseRow" => $databaseRow,
			"fieldName" => $fieldName,
			"processedTca" => array(
				"columns" => array(
					$fieldName => array(
						"exclude" => 1,
						"label" => $this->getField()->getFrontendLabel(),
						"config" => array(
							"type" => "select",
							"multiple" => 1,
							"renderType" => "selectMultipleSideBySide",
							"items" => $this->getFieldItems($this->getField()),
						),
					),
				),
			),
			"inlineStructure" => array(),
			"rootline" => array(),
		);
		
		if($this->getField()->getConfig("foreign"))
			$tca["processedTca"]["columns"][$fieldName]["config"]["items"] = array();
		
		$this->prepareTca($tca);
		$this->tca = $tca;
		return $this->tca;
	}


}
