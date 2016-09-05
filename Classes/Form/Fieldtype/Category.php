<?php
namespace MageDeveloper\Dataviewer\Form\Fieldtype;

use MageDeveloper\Dataviewer\Domain\Model\Field;
use MageDeveloper\Dataviewer\Domain\Model\FieldValue;
use MageDeveloper\Dataviewer\Domain\Model\RecordValue;
use TYPO3\CMS\Backend\Utility\BackendUtility;

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
class Category extends Select
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
		$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\TcaSelectTreeItems::class;
		parent::initializeFormDataProviders();
	}

	/**
	 * Gets built tca array
	 *
	 * @return array
	 */
	public function buildTca()
	{
		$rootline = BackendUtility::BEgetRootLine( $this->getRecord()->getUid() );
	
		$fieldName 					= $this->getField()->getFieldName();
		$tableName 					= "tx_dataviewer_domain_model_record";
		$value 						= $this->getValue();
		$databaseRow 				= $this->getDatabaseRow();
		$databaseRow[$fieldName] 	= $value;
		$sourcePids					= $this->getField()->getConfig("pids");
		$foreignTableWhere			= ($sourcePids)?"AND sys_category.pid IN ({$sourcePids}) ":"";

		$tca = array(
			"command" => "new",
			"tableName" => $tableName,
			"databaseRow" => $databaseRow,
			"fieldName" => $fieldName,
			"rootline" => $rootline,
			"processedTca" => array(
				"columns" => array(
					$fieldName => array(
						"exclude" => 1,
						"label" => $this->getField()->getFrontendLabel(),
						"config" => array(
							"type" => "select",
							"renderType" => "selectTree",
							"foreign_table" => "sys_category",
							"foreign_table_where" => "{$foreignTableWhere}AND sys_category.sys_language_uid IN (-1, 0) ORDER BY sys_category.sorting ASC",
							"MM" => "sys_category_record_mm",
							"MM_opposite_field" => "items",
							"MM_match_fields" => array(
								"tablenames" => "tt_content",
								"fieldname" => "categories",
							),
							"treeConfig" => array(
								"parentField" => "parent",
								"appearance" => array(
									"expandAll" => (int)$this->getField()->getConfig("expandAll"),
									"showHeader" => (int)$this->getField()->getConfig("showHeader"),
								),
							),
						),
					),
				),
			),
			"inlineStructure" => array(),
		);

		$this->prepareTca($tca);
		$this->tca = $tca;
		return $this->tca;
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
			/* @var FieldValue $defaultValue */
			$defaultValues 	= $fieldValue->getField()->getDefaultValues();
			$defaultValue 	= reset($defaultValues);

			return $defaultValue->getValueContent();
		}
	}
}
