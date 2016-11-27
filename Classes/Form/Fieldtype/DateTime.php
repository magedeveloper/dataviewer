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
class DateTime extends Date
{
	/**
	 * Gets built tca array
	 *
	 * @return array
	 */
	public function buildTca()
	{
		$fieldName 					= $this->getField()->getUid();
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
							"type" => "input",
							"size" => 30,
							"eval" => "datetime,".$this->getField()->getConfig("eval"),
							"placeholder" => $this->getField()->getConfig("placeholder"),
							"dbType" => "datetime",
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
}
