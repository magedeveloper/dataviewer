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
class Datatype extends AbstractFieldtype implements FieldtypeInterface
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
		$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseRowInitializeNew::class;
		$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInlineIsOnSymmetricSide::class;

		$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\InlineOverrideChildTca::class;
		$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\TcaSelectTreeItems::class;
		$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInlineExpandCollapseState::class;
		$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInlineConfiguration::class;
		$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\TcaRecordTitle::class;
		$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\TcaSelectTreeItems::class;
		$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class;


		//$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\TcaFlexFetch::class;
		//$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\TcaFlexPrepare::class;
		//$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\TcaFlexProcess::class;

		//$this->formDataProviders[] = \MageDeveloper\Dataviewer\Form\FormDataGroup\InlineParentRecord::class;

		parent::initializeFormDataProviders();
	}

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
			"command" => "edit",
			"tableName" => $tableName,
			"databaseRow" => $databaseRow,
			"fieldName" => $fieldName,
			"processedTca" => [
				"ctrl" => [
					"label" => $this->getField()->getFrontendLabel(),
				],
				"columns" => [
					$fieldName => [
						"exclude" => (int)$this->getField()->isExclude(),
						"label" => $this->getField()->getFrontendLabel(),
						"config" => [
							"type" => "inline",
							"foreign_table" => "tx_dataviewer_domain_model_record",
							"foreign_record_defaults" => [
								"datatype" => $this->getField()->getConfig("datatype"),
							],	
							"maxitems"      => 9999,
							"appearance" => [
								"collapseAll" => 1,
								"levelLinksPosition" => "top",
								"showSynchronizationLink" => 1,
								"showPossibleLocalizationRecords" => 1,
								"useSortable" => 1,
								"showAllLocalizationLink" => 1,
							],
						],
					],
				],
			],
			"inlineStructure" => [],
			"inlineFirstPid" => $this->getRecord()->getPid(),
			"inlineResolveExistingChildren" => true,
			"inlineCompileExistingChildren"=> true,
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

		//size
		if($size = $this->getField()->getConfig("size"))
			$tca["processedTca"]["columns"][$fieldName]["config"]["size"] = $size;

		parent::prepareTca($tca);
	}


}
