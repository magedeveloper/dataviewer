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
class FileRelation extends AbstractFieldtype implements FieldtypeInterface
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
		$this->formDataProviders[] = \MageDeveloper\Dataviewer\Form\FormDataProvider\TcaInlineFile::class;

		// Original
		//$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class;


		//$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\TcaFlexFetch::class;
		//$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\TcaFlexPrepare::class;
		//$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\TcaFlexProcess::class;

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
		
		$tca = array(
			"command" => "edit",
			"tableName" => $tableName,
			"databaseRow" => $databaseRow,
			"fieldName" => $fieldName,
			"processedTca" => array(
				"ctrl" => array(
					"label" => $this->getField()->getFrontendLabel(),
				),
				"columns" => array(
					$fieldName => array(
						"exclude" => 1,
						"label" => $this->getField()->getFrontendLabel(),
						"config" => array(
							"type" => "inline",
							"foreign_table" => "sys_file_reference",
							"foreign_field" => "uid_foreign",
							"foreign_sortby" => "sorting_foreign",
							"foreign_table_field" => "tablenames",
							"foreign_match_fields" => array(
								"fieldname" => $fieldName
							),
							"foreign_label" => "uid_local",
							"foreign_selector" => "uid_local",
							"foreign_selector_fieldTcaOverride" => array(
								"config" => array(
									"appearance" => array(
										"elementBrowserType" => ($this->getField()->getConfig("showUpload") == "1")?"file":"",
										"elementBrowserAllowed" => $this->getField()->getConfig("allowed"),
									)
								)
							),
							"filter" => array(
								array(
									"userFunc" => "TYPO3\\CMS\\Core\\Resource\\Filter\\FileExtensionFilter->filterInlineChildren",
									"parameters" => array(
										"allowedFileExtensions" => $this->getField()->getConfig("allowed"),
										"disallowedFileExtensions" => $this->getField()->getConfig("disallowed"),
									)
								)
							),
							"appearance" => array(
								"useSortable" => TRUE,
								"headerThumbnail" => array(
									"field" => "uid_local",
									"width" => "45",
									"height" => "45c",
								),
								"showPossibleLocalizationRecords" => FALSE,
								"showRemovedLocalizationRecords" => FALSE,
								"showSynchronizationLink" => FALSE,
								"showAllLocalizationLink" => FALSE,

								"enabledControls" => array(
									"info" => true,
									"new" => true,
									"dragdrop" => true,
									"sort" => true,
									"hide" => true,
									"delete" => true,
									"localize" => true,
								),
							),
							"behaviour" => array(
								"localizationMode" => "select",
								"localizeChildrenAtParentLocalization" => TRUE,
							),
						),
					),
				),
			),
			"inlineStructure" => array(),
			"inlineFirstPid" => $this->getRecord()->getPid(),
			"inlineResolveExistingChildren" => true,
			"inlineCompileExistingChildren"=> true,
		);
		
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
		$tca["processedTca"]["columns"][$fieldName]["config"]["multiple"] = (int)$this->getField()->getConfig("multiple");

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

		//hideMoveIcons
		$tca["processedTca"]["columns"][$fieldName]["config"]["hideMoveIcons"] = (int)$this->getField()->getConfig("hideMoveIcons");

		//disable_controls
		if($disable_controls = $this->getField()->getConfig("disable_controls"))
			$tca["processedTca"]["columns"][$fieldName]["config"]["disable_controls"] = $disable_controls;

		parent::prepareTca($tca);
	}


}
