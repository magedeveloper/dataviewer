<?php
namespace MageDeveloper\Dataviewer\Controller;

use MageDeveloper\Dataviewer\Domain\Model\Field;
use MageDeveloper\Dataviewer\Domain\Model\Record;
use MageDeveloper\Dataviewer\Domain\Model\Variable;
use MageDeveloper\Dataviewer\Service\Settings\Plugin\ListSettingsService;
use MageDeveloper\Dataviewer\Service\Settings\Plugin\SearchSettingsService;
use MageDeveloper\Dataviewer\Utility\DebugUtility;
use MageDeveloper\Dataviewer\Utility\LocalizationUtility as Locale;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentNameException;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

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
class RecordController extends AbstractController
{
	/***************************************************************************
	 * This controller manages the display of records or record parts.
	 * It is influenced by different session settings like sorting and filtering
	 * or the search.
	 ***************************************************************************/

	/**
	 * Storage Pids
	 * 
	 * @var array
	 */
	protected $storagePids = [];

	/**
	 * Field Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\FieldRepository
	 * @inject
	 */
	protected $fieldRepository;

	/**
	 * Session Service Container
	 * 
	 * @var \MageDeveloper\Dataviewer\Service\Session\SessionServiceContainer
	 * @inject
	 */
	protected $sessionServiceContainer;

	/**
	 * List Settings Service
	 * 
	 * @var \MageDeveloper\Dataviewer\Service\Settings\Plugin\ListSettingsService
	 * @inject
	 */
	protected $listSettingsService;

	/**
	 * Standalone View
	 *
	 * @var \MageDeveloper\Dataviewer\Fluid\View\StandaloneView
	 */
	protected $standaloneView;

	/**
	 * List Action
	 * Displays a list of selected records
	 *
	 * @return void
	 */
	public function listAction()
	{
		$selectedRecords = $this->_getSelectedRecords();
		
		$selectedRecordIds = [];
		foreach($selectedRecords as $_sR)
			$selectedRecordIds[] = $_sR->getUid();

		// We set these records as currently active
		$this->sessionServiceContainer->getInjectorSessionService()->setActiveRecordIds($selectedRecordIds);

		if ($this->listSettingsService->hasTemplateOverride() && !$this->listSettingsService->isDebug())
			$this->view->setTemplatePathAndFilename($this->listSettingsService->getTemplateOverride());
		
		$this->view->assign($this->listSettingsService->getRecordsVarName(), $selectedRecords);
	}

	/**
	 * Detail Action
	 * Displays a selected record
	 *
	 * @return void
	 */
	public function detailAction()
	{
		$selectedRecordId 	= $this->listSettingsService->getSelectedRecordId();
		$record 			= $this->recordRepository->findByUid($selectedRecordId, false);

		if (!$record instanceof Record)
			$record = null;

		// We set this record as currently active
		// We set this record as currently active
		$activeRecordIds = [];
		if(!is_null($record))
			$activeRecordIds = [$record->getUid()];

		$this->sessionServiceContainer->getInjectorSessionService()->setActiveRecordIds($activeRecordIds);

		// Override by datatype template setting
		if ($record->getDatatype()->getTemplatefile() && !$this->listSettingsService->isDebug())
			$this->view->setTemplatePathAndFilename($record->getDatatype()->getTemplatefile());
			
		// Template Override by plugin setting
		if ($this->listSettingsService->hasTemplateOverride() && !$this->listSettingsService->isDebug())
			$this->view->setTemplatePathAndFilename($this->listSettingsService->getTemplateOverride());

		$this->view->assign($this->listSettingsService->getRecordVarName(), $record);
	}

	/**
	 * Part Action
	 * Displays a part from the selected record
	 *
	 * @return void
	 */
	public function partAction()
	{
		$selectedRecordId 	= $this->listSettingsService->getSelectedRecordId();
		$selectedFieldId	= $this->listSettingsService->getSelectedFieldId();
		
		// We set this record as currently active
		$this->sessionServiceContainer->getInjectorSessionService()->setActiveRecordIds(array($selectedRecordId));

		$record = $this->recordRepository->findByUid($selectedRecordId, false);
		$field  = $this->fieldRepository->findByUid($selectedFieldId, false);
		
		// Template Override by plugin setting
		if ($this->listSettingsService->hasTemplateOverride() && !$this->listSettingsService->isDebug())
			$this->view->setTemplatePathAndFilename($this->listSettingsService->getTemplateOverride());

		if ($record instanceof Record && $field instanceof Field)
		{
			$value = $record->getValueByField($field);
			$this->view->assign($this->listSettingsService->getPartVarName(), $value);
		}
		
	}

	/**
	 * Dynamic Detail Action
	 * Displays record details on dynamic parameters
	 *
	 * @param int $record
	 * @return void
	 */
	public function dynamicDetailAction($record = null)
	{
		/* @var Record $recordObj */
		$recordObj = $this->recordRepository->findByUid($record, false);

		if (!$recordObj instanceof Record)
			$recordObj = null;
		else	
			if ($recordObj->getDatatype()->getTemplatefile() && !$this->listSettingsService->isDebug())
				$this->view->setTemplatePathAndFilename($record->getDatatype()->getTemplatefile());

		// Template Override by plugin setting
		if ($this->listSettingsService->hasTemplateOverride() && !$this->listSettingsService->isDebug())
			$this->view->setTemplatePathAndFilename($this->listSettingsService->getTemplateOverride());

		// We set this record as currently active
		$activeRecordIds = [];
		if(!is_null($recordObj))
			$activeRecordIds = [$recordObj->getUid()];

		$this->sessionServiceContainer->getInjectorSessionService()->setActiveRecordIds($activeRecordIds);


		////////////////////////////////////////////////////
		// We need to obtain the selected records for
		// getting information about which records are
		// allowed for this dynamic detail action
		////////////////////////////////////////////////////
		$selectedRecords = $this->_getSelectedRecords();
		$ids = [];
		foreach($selectedRecords as $_s)
			$ids[] = $_s->getUid();

		if (!in_array($record, $ids) && $this->listSettingsService->getRecordSelectionType())
			return;

		// Get selected records and check if the record is allowed
		$this->view->assign($this->listSettingsService->getRecordVarName(), $recordObj);
	}

	/**
	 * Gets the content uid
	 *
	 * @return int
	 */
	protected function _getContentUid()
	{
		$uid = 0;
		$contentObj = $this->configurationManager->getContentObject();

		if ($contentObj)
			$uid = $contentObj->data["uid"];

		return (int)$uid;
	}

	/**
	 * Gets the selected records with the use of all
	 * session services to apply the following cicumstances
	 * 
	 * - Filtering
	 * - Sorting
	 * - Searching
	 * - Letter Selection
	 * 
	 * @return array|QueryInterface
	 */
	protected function _getSelectedRecords()
	{
		$selectionType 	= $this->listSettingsService->getRecordSelectionType();
		
		// Record Selection Type Filters
		$selectFilters	= $this->_getAdditionalFiltersBySelectionType($selectionType);
		
		// Limit
		$limit			= $this->listSettingsService->getLimitation();
		
		// Field/Value Filter Settings
		$fieldFilter	= $this->listSettingsService->getFieldValueFilters();
		
		// Search
		$searchFields	= $this->sessionServiceContainer->getSearchSessionService()->getSearchFields();
		$searchString	= $this->sessionServiceContainer->getSearchSessionService()->getSearchString();
		$searchType		= $this->sessionServiceContainer->getSearchSessionService()->getSearchType();
		$searchFilters	= $this->_getAdditionalFiltersBySearch($searchType, $searchString, $searchFields);
		
		// Filter
		$filter			= $this->sessionServiceContainer->getFilterSessionService()->getCleanSelectedOptions();
		
		// Selection
		$selection		= $this->sessionServiceContainer->getSelectSessionService()->getSelectedRecords();
		$selectionFilters = $this->_getAdditionalFiltersByRecordSelection($selection);
		
		// Sort
		if(!$this->sessionServiceContainer->getSortSessionService()->hasOrderings() || !$this->_hasTargetSortPlugin())
		{
			// We initally set orderings from our plugin settings and will use
			// information from the sort plugin later, once it was used
			$configSortField	= $this->listSettingsService->getSortField();
			$configSortOrder	= $this->listSettingsService->getSortOrder();
			$configPerPage		= $this->listSettingsService->getPerPage();
			
			$this->sessionServiceContainer->getSortSessionService()->setSortField($configSortField);
			$this->sessionServiceContainer->getSortSessionService()->setSortOrder($configSortOrder);
			$this->sessionServiceContainer->getSortSessionService()->setPerPage($configPerPage);
		}
		
		$sortField		= $this->sessionServiceContainer->getSortSessionService()->getSortField();
		$sortOrder		= $this->sessionServiceContainer->getSortSessionService()->getSortOrder();
		$perPage		= $this->sessionServiceContainer->getSortSessionService()->getPerPage();
		
		// Letter Selection
		$letter			= $this->sessionServiceContainer->getLetterSessionService()->getSelectedLetter();
		$letterField	= $this->sessionServiceContainer->getLetterSessionService()->getLetterSelectionField();
		$letterFilters	= $this->_getAdditionalFiltersByLetterSelection($letter, $letterField);
		
		// Filters Merged
		$filters	= array_merge($fieldFilter, $filter, $selectionFilters, $selectFilters, $searchFilters, $letterFilters);
		
		////////////////////////////////////////////////////////////////////////////////
		// Signal-Slot for manipulating the complete filters for the record selection //
		////////////////////////////////////////////////////////////////////////////////
		$this->signalSlotDispatcher->dispatch(
			__CLASS__,
			"prepareFilters",
			[
				&$filters,
				&$this,
			]
		);

		// Replace markers in the filters
		$this->_replaceMarkersInFilters($filters);
		
		/***************************************************************************************************
		Adding a filter:
		--------------------------------------
		[field_id] => 5|title
		[filter_condition] => like
		[field_value] => filterval
		[filter_combination] => AND|OR

		---------------------------------------------------------------------------------------------------------
		eq			=			'{$var}'					->equals
		neq			!=			'{$var}'					->logicalNot->equals
		like		LIKE		'%{$var}%'					->like
		nlike		NOT LIKE	'%{$var}%'					->logicalNot->like
		in			IN			([trimExplode]{$var})		->in
		nin			NOT IN		([trimExplode]{$var})		->logicalNot->in
		gt			>			{(int)$var}					->greaterThan
		lt			<			{(int)$var}					->lessThan
		gte			>=			{(int)$var}					->greaterThanOrEqual
		lte			<=			{(int)$var}					->lessThanOrEqual
		****************************************************************************************************/
		if($this->settings["debug"] == 1)
		{
			$statement = $this->recordRepository->getStatementByAdvancedConditions($filters, $sortField, $sortOrder, $limit, $this->storagePids);
			$this->view->assign("statement", $statement);
		}
		
		$validRecords = $this->recordRepository->findByAdvancedConditions($filters, $sortField, $sortOrder, $limit, $this->storagePids);
		$records = null;
		$validRecordIds = array_column($validRecords, "uid");
		
		if(!empty($validRecordIds))
			$records = $this->recordRepository->findByRecordIds($validRecordIds, $this->storagePids);

		/* @var \MageDeveloper\Dataviewer\Domain\Model\Record $_record */
		if($records)
			foreach($records as $_record)
				$_record->initializeValues();
		else
			$records = [];

		//////////////////////////////////////////////////////
		// Signal-Slot for post-processing selected records //
		//////////////////////////////////////////////////////
		$this->signalSlotDispatcher->dispatch(
			__CLASS__,
			"postProcessSelectedRecords",
			[
				&$records,
				&$this,
			]
		);

		return $records;
	}

	/**
	 * Gets additional filters computed
	 * by a letter and the according field
	 *
	 * @param array $recordSelection
	 * @return array
	 */
	protected function _getAdditionalFiltersByRecordSelection(array $recordSelection = [])
	{
		$additionalFilters = [];

		if(!empty($recordSelection))
		{
			$selectedRecordIds = implode(",", $recordSelection);
			$additionalFilters[] = [
				"field_id" => "RECORD.uid",
				"filter_condition" => "in",
				"field_value" => $selectedRecordIds,
				"filter_combination" => "AND",
			];
		}

		return $additionalFilters;
	}

	/**
	 * Gets additional filters computed
	 * by a letter and the according field
	 * 
	 * @param string $letter
	 * @param int|string $letterField
	 * @return array
	 */
	protected function _getAdditionalFiltersByLetterSelection($letter, $letterField)
	{
		$additionalFilters = [];
		
		if(strlen($letter) === 1)
		{
			if(!is_numeric($letterField))
				$letterField = "RECORD.{$letterField}";
		
			$additionalFilters[] = [
				"field_id" => $letterField,
				"filter_condition" => "like",
				"field_value" => "{$letter}%",
				"filter_combination" => "AND",
			];
		}
		
		return $additionalFilters;
	}

	/**
	 * Gets additional filters computed
	 * by search
	 * 
	 * @param string $searchType
	 * @param string $searchString
	 * @param array $searchFields
	 * @return array
	 */
	protected function _getAdditionalFiltersBySearch($searchType, $searchString, array $searchFields)
	{
		$additionalFilters = [];

		foreach($searchFields as $i=>$_sF)
			$searchFields[$i]["field_value"] = $searchString;
	
		switch($searchType)
		{
			case SearchSettingsService::SEARCH_RECORD_TITLE:
				$additionalFilters[] = [
					"field_id" => "RECORD.title",
					"filter_condition" => "like",
					"field_value" => "%{$searchString}%",
					"filter_combination" => "AND",
				];
				break;
			case SearchSettingsService::SEARCH_RECORD_TITLE_FIELDS:
				$additionalFilters[] = [
					"field_id" => "RECORD.title",
					"filter_condition" => "like",
					"field_value" => "%{$searchString}%",
					"filter_combination" => "AND",
				];
				$additionalFilters = array_merge($additionalFilters, $searchFields);
				break;
			case SearchSettingsService::SEARCH_FIELDS:
				$additionalFilters = array_merge($additionalFilters, $searchFields);
				break;
		}

		return $additionalFilters;
	}

	/**
	 * Gets additional filters computed
	 * by a selection type
	 * 
	 * @param string $selectionType
	 * @return array
	 */
	protected function _getAdditionalFiltersBySelectionType($selectionType)
	{
		$additionalFilters = [];
	
		switch($selectionType)
		{
			case ListSettingsService::SELECTION_TYPE_DATATYPES:
				$selectedDatatypes = $this->listSettingsService->getSelectedDatatypeIds();
			
				$additionalFilters[] = [
					"field_id" => "RECORD.datatype",
					"filter_condition" => "in",
					"field_value" => $selectedDatatypes,
					"filter_combination" => "AND",
				];
				break;
			case ListSettingsService::SELECTION_TYPE_RECORDS:
				$selectedRecordIds = $this->listSettingsService->getSelectedRecordIds();
				$additionalFilters[] = [
					"field_id" => "RECORD.uid",
					"filter_condition" => "in",
					"field_value" => $selectedRecordIds,
					"filter_combination" => "AND",
				];
				break;
			case ListSettingsService::SELECTION_TYPE_CREATION_DATE:
				// Date From
				$dateFrom = $this->listSettingsService->getDateFrom();
				// Date To
				$dateTo = $this->listSettingsService->getDateTo();
				
				$additionalFilters[] = [
					"field_id" => "RECORD.crdate",
					"filter_condition" => "gte",
					"field_value" => $dateFrom->getTimestamp(),
					"filter_combination" => "AND",
				];
				
				$additionalFilters[] = [
					"field_id" => "RECORD.crdate",
					"filter_condition" => "lte",
					"field_value" => $dateTo->getTimestamp(),
					"filter_combination" => "AND",
				];
				break;
			case ListSettingsService::SELECTION_TYPE_CHANGE_DATE:
				// Date From
				$dateFrom = $this->listSettingsService->getDateFrom();
				// Date To
				$dateTo = $this->listSettingsService->getDateTo();

				$additionalFilters[] = [
					"field_id" => "RECORD.tstamp",
					"filter_condition" => "gte",
					"field_value" => $dateFrom->getTimestamp(),
					"filter_combination" => "AND",
				];

				$additionalFilters[] = [
					"field_id" => "RECORD.tstamp",
					"filter_condition" => "lte",
					"field_value" => $dateTo->getTimestamp(),
					"filter_combination" => "AND",
				];
				break;
			case ListSettingsService::SELECTION_TYPE_FIELD_VALUE_FILTER:
				break;
			case ListSettingsService::SELECTION_TYPE_ALL_RECORDS:
				break;
		}
		
		return $additionalFilters;
	}

	/**
	 * Checks if there is a sort plugin, that is 
	 * targeting to this element
	 * 
	 * @return bool
	 */
	protected function _hasTargetSortPlugin()
	{
		if($this->listSettingsService->isForcedSorting())
			return false;
	
		$cObj = $this->configurationManager->getContentObject();
		$data = $cObj->data;
		$uid = (int)$data["uid"];
	
		$res = $GLOBALS["TYPO3_DB"]->exec_SELECTgetRows(
			"uid",				// SELECT
			"tt_content",		// FROM
			"list_type = 'dataviewer_sort' AND pi_flexform RLIKE '<field index=\"settings.target_plugin\">.*<value index=\"vDEF\">{$uid}</value>.*</field>'"	// WHERE
		);
	
		return (count($res)>0);
	}

	/**
	 * initializeView
	 * Initializes the view
	 *
	 * Adds some variables to view that could always
	 * be useful
	 *
	 * @param ViewInterface $view
	 * @return void
	 */
	protected function initializeView(ViewInterface $view)
	{
		// Individual session key
		$uid = $this->_getContentUid();
		$this->sessionServiceContainer->setTargetUid($uid);

		$cObj = $this->configurationManager->getContentObject();
		if ($cObj instanceof \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer)
			$this->view->assign("cObj", $cObj->data);

		// Allowed Storage Pids
		$pageData = $cObj->data;
		$this->storagePids = GeneralUtility::trimExplode(",", $pageData["pages"], true);

		$ids = $this->listSettingsService->getSelectedVariableIds();
		$variables = $this->prepareVariables($ids);
		$this->view->assignMultiple($variables);
		
		// Parent
		parent::initializeView($view);
	}

	/**
	 * Replaces all markers in filters
	 *
	 * @param array $filters
	 * @return void
	 */
	protected function _replaceMarkersInFilters(array &$filters)
	{
		foreach($filters as $i=>$_filter)
			$filters[$i]["field_value"] = $this->_replaceMarkersInString($_filter["field_value"]);
			
	}

	/**
	 * Replaces all markers in a given string
	 *
	 * @param string $string
	 * @return void
	 */
	protected function _replaceMarkersInString($string)
	{
		return $this->getStandaloneView()->renderSource($string);
	}

	/**
	 * Gets the standalone view instance
	 * 
	 * @return \MageDeveloper\Dataviewer\Fluid\View\StandaloneView
	 */
	protected function getStandaloneView()
	{
		if(!$this->standaloneView) 
		{
			$this->standaloneView = $this->objectManager->get(\MageDeveloper\Dataviewer\Fluid\View\StandaloneView::class);
			
			$variables = $this->variableRepository->findByStoragePids($this->storagePids);
			$ids = [];
			
			foreach($variables as $_v)
				$ids[] = $_v->getUid();
			
			$variables = $this->prepareVariables($ids);
			$this->standaloneView->assignMultiple($variables);
		}	
					
		return $this->standaloneView;	
	}
}
