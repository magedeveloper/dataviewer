<?php
namespace MageDeveloper\Dataviewer\Controller;

use MageDeveloper\Dataviewer\Domain\Model\Field;
use MageDeveloper\Dataviewer\Domain\Model\Record;
use MageDeveloper\Dataviewer\Domain\Model\Variable;
use MageDeveloper\Dataviewer\Service\Settings\Plugin\ListSettingsService;
use MageDeveloper\Dataviewer\Service\Settings\Plugin\SearchSettingsService;
use MageDeveloper\Dataviewer\Utility\DebugUtility;
use MageDeveloper\Dataviewer\Utility\LocalizationUtility as Locale;
use TYPO3\CMS\Backend\Utility\BackendUtility;
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
	 * Gets the session service container
	 * 
	 * @return \MageDeveloper\Dataviewer\Service\Session\SessionServiceContainer
	 */
	public function getSessionServiceContainer()
	{
		return $this->sessionServiceContainer;
	}

	/**
	 * List Action
	 * Displays a list of selected records
	 *
	 * @return void
	 */
	public function listAction()
	{
		if($this->listSettingsService->isCustomFluidCode())
			$this->forward("renderFluid");
			
		$selectedRecords = $this->_getSelectedRecords();
		
		$templateSwitch = $this->getTemplateSwitch();
		if($templateSwitch)
			$this->view->setTemplatePathAndFilename($templateSwitch);
		
		$this->view->assign($this->listSettingsService->getRecordsVarName(), $selectedRecords);

		// Custom Headers
		$customHeaders = $this->getCustomHeaders();
		$this->performCustomHeaders($customHeaders);
		
		if($this->listSettingsService->renderOnlyTemplate() && !$this->listSettingsService->isDebug())
		{
			echo $this->view->render();
			exit();
		}	
	}

	/**
	 * This action directly renders the entered fluid code
	 * 
	 * @return string
	 */
	public function renderFluidAction()
	{
		$selectedRecords = $this->_getSelectedRecords();
		$view = $this->getStandaloneView(false);
		$selectedVariableIds = $this->listSettingsService->getSelectedVariableIds();
		$variables = $this->prepareVariables($selectedVariableIds);
		$templateSource = $this->listSettingsService->getFluidCode();

		// Assign variables to the view
		$view->assign($this->listSettingsService->getRecordsVarName(), $selectedRecords);
		$view->assignMultiple($variables);
		
		$templateSwitch = $this->getTemplateSwitch();
		if($templateSwitch)
			$view->setTemplatePathAndFilename($templateSwitch);
		else
			$view->setTemplateSource($templateSource);

		// Custom Headers
		$customHeaders = $this->getCustomHeaders();
		$this->performCustomHeaders($customHeaders);
		
		if($this->listSettingsService->renderOnlyTemplate() && !$this->listSettingsService->isDebug())
		{
			echo $view->render();
			exit();
		}

		if($this->listSettingsService->isDebug())
		{
			$templateSource = "<f:debug>{_all}</f:debug>".$templateSource;
			$view->setTemplateSource($templateSource);
		}

		return $view->render();
	}

	/**
	 * Adds custom headers to the response object
	 * 
	 * @param array $customHeaders
	 * @return bool
	 */
	public function performCustomHeaders($customHeaders)
	{
		if(!empty($customHeaders))
		{
			// Setting custom headers
			foreach($customHeaders as $_headerName=>$_headerValue)
				$this->response->setHeader($_headerName, $_headerValue, true);

			$this->response->sendHeaders();
			return true;
		}
		
		return false;
	}

	/**
	 * Gets all custom headers that are valid for
	 * the current view
	 * 
	 * @return array
	 */
	public function getCustomHeaders()
	{
		// Custom Headers Configuration
		$customHeadersConfiguration = $this->listSettingsService->getCustomHeaders();

		// Get a view with all injected variables
		$view = $this->getStandaloneView(true);

		$customHeaders = [];
		foreach($customHeadersConfiguration as $_header)
		{
			$conditionStr = $_header["headers"]["condition"];
			$headerName = $_header["headers"]["name"];
			$headerValue = $_header["headers"]["value"];
			$headerValue = $this->_replaceMarkersInString($headerValue);

			if($conditionStr == "")
			{
				// Header is always valid
				$isValid = true;
			}
			else
			{
				// Since we yet do not know how to render the nodes separately, we
				// just render a simple full fluid condition here
				$conditionText = "<f:if condition=\"{$conditionStr}\">1</f:if>";
				$isValid = (bool)$view->renderSource($conditionText);
			}

			if($isValid)
				$customHeaders[$headerName] = $headerValue;
		}
		
		return $customHeaders;
	}

	/**
	 * Evaluations the conditions for a template switch
	 * and returns the evaluated template path that
	 * can be used
	 * 
	 * @return string
	 */
	public function getTemplateSwitch()
	{
		// Evaluation the template switch conditions
		$conditions = $this->listSettingsService->getTemplateSwitchConditions();
		
		// Get a view with all injected variables
		$view = $this->getStandaloneView(true);
	
		foreach($conditions as $_condition)
		{
			$conditionStr = $_condition["switches"]["condition"];
			$templateId = $_condition["switches"]["template_selection"];

			// Since we yet do not know how to render the nodes separately, we
			// just render a simple full fluid condition here
			$conditionText = "<f:if condition=\"{$conditionStr}\">1</f:if>";
			$isValid = (bool)$view->renderSource($conditionText);
			
			if($isValid)
				return $this->listSettingsService->getPredefinedTemplateById($templateId);
			
		}

		if ($this->listSettingsService->hasTemplate() && !$this->listSettingsService->isDebug())
			return $this->listSettingsService->getTemplate();
		
		return;
	}

	/**
	 * Detail Action
	 * Displays a selected record
	 *
	 * @return void
	 */
	public function detailAction()
	{
		if($this->listSettingsService->isCustomFluidCode())
			$this->forward("renderFluid");
	
		$selectedRecordId 	= $this->listSettingsService->getSelectedRecordId();
		$record 			= $this->recordRepository->findByUid($selectedRecordId, false);

		if (!$record instanceof Record)
			$record = null;

		// We set this record as currently active
		$activeRecordIds = [];
		if(!is_null($record))
			$activeRecordIds = [$record->getUid()];

		$this->sessionServiceContainer->getInjectorSessionService()->setActiveRecordIds($activeRecordIds);

		// Override by datatype template setting
		if ($record->getDatatype()->getTemplatefile() && !$this->listSettingsService->isDebug())
			$this->view->setTemplatePathAndFilename($record->getDatatype()->getTemplatefile());

		$templateSwitch = $this->getTemplateSwitch();
		if($templateSwitch)
			$this->view->setTemplatePathAndFilename($templateSwitch);

		$this->view->assign($this->listSettingsService->getRecordVarName(), $record);

		// Custom Headers
		$customHeaders = $this->getCustomHeaders();
		$this->performCustomHeaders($customHeaders);

		if($this->listSettingsService->renderOnlyTemplate() && !$this->listSettingsService->isDebug())
		{
			echo $this->view->render();
			exit();
		}
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
		$templateSwitch = $this->getTemplateSwitch();
		if($templateSwitch)
			$this->view->setTemplatePathAndFilename($templateSwitch);

		if ($record instanceof Record && $field instanceof Field)
		{
			$value = $record->getValueByField($field);
			$this->view->assign($this->listSettingsService->getPartVarName(), $value);
		}

		// Custom Headers
		$customHeaders = $this->getCustomHeaders();
		$this->performCustomHeaders($customHeaders);

		if($this->listSettingsService->renderOnlyTemplate() && !$this->listSettingsService->isDebug())
		{
			echo $this->view->render();
			exit();
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
				$this->view->setTemplatePathAndFilename($recordObj->getDatatype()->getTemplatefile());

		// Template Override by plugin setting
		$templateSwitch = $this->getTemplateSwitch();
		if($templateSwitch)
			$this->view->setTemplatePathAndFilename($templateSwitch);

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

		// Custom Headers
		$customHeaders = $this->getCustomHeaders();
		$this->performCustomHeaders($customHeaders);

		if($this->listSettingsService->renderOnlyTemplate() && !$this->listSettingsService->isDebug())
		{
			echo $this->view->render();
			exit();
		}
	}

	/**
	 * Ajax Request Action
	 * This action is the main entry for the ajax request handling.
	 * It initially shows the configured template and is then
	 * ready for the ajax call.
	 * 
	 * @return string
	 */
	public function ajaxRequestAction()
	{
		$contentUid = $this->_getContentUid();
		$selectedRecords = $this->_getSelectedRecords();

		$templateSwitch = $this->getTemplateSwitch();
		if($templateSwitch)
			$this->view->setTemplatePathAndFilename($templateSwitch);

		$this->view->assign($this->listSettingsService->getRecordsVarName(), $selectedRecords);
		$this->view->assign("ajax", false);
		$rendered = $this->view->render();

		return "<div id=\"dataviewer-ajax-{$contentUid}\">".$rendered."</div>";
	}

	/**
	 * Ajax Response Action
	 * This action is for handling ajax requests that
	 * are done with the dataviewer extension.
	 *
	 * It can handle different type of requests, given as
	 * arguments in this action
	 *
	 * @return string
	 */
	public function ajaxResponseAction()
	{
		// TODO: hookable method for evaluating records that will be
		// injected to the selected templates as chosen in ajaxRequestAction.
		// --------------------------------------------------------------------
		// We need a ViewHelper that can run the Ajax Request by
		// click, change, keyUp (all configurable) and adds parameters to
		// the call
		if($this->request->hasArgument("uid"))
		{
			/* @var \MageDeveloper\Dataviewer\Service\FlexFormService $flexFormService */
			$flexFormService = $this->objectManager->get(\MageDeveloper\Dataviewer\Service\FlexFormService::class);
			$uid = $this->request->getArgument("uid");
			$cObj = BackendUtility::getRecord("tt_content", $uid);
			
			// Storage Page Ids
			$this->storagePids = GeneralUtility::trimExplode(",", $cObj["pages"]);
		
			// Settings Array
			$flexArr = $flexFormService->convertFlexFormContentToArray($cObj["pi_flexform"]);
			$this->settings = $flexArr["settings"];
			$this->listSettingsService->setSettings($this->settings);
			
			// Session Container Connection
			$this->sessionServiceContainer->setTargetUid($uid);

			$parameters = [];
			if($this->request->hasArgument("parameters"))
				$parameters = $this->request->getArgument("parameters");

			$view = $this->getStandaloneView(true);
			$additionalVariables = [];

			/////////////////////////////////////////////
			// Signal-Slot for hooking the ajax return //
			/////////////////////////////////////////////
			$this->signalSlotDispatcher->dispatch(
				__CLASS__,
				"ajaxResponsePreRecords",
				[
					&$parameters,
					&$uid,
					&$additionalVariables,
					&$this,
				]
			);

			$records = $this->_getSelectedRecords();

			$templateSwitch = $this->getTemplateSwitch();
			if($templateSwitch)
				$view->setTemplatePathAndFilename($templateSwitch);

			/////////////////////////////////////////////
			// Signal-Slot for hooking the ajax return //
			/////////////////////////////////////////////
			$this->signalSlotDispatcher->dispatch(
				__CLASS__,
				"ajaxResponsePostRecords",
				[
					&$records,
					&$parameters,
					&$uid,
					&$additionalVariables,
					&$this,
				]
			);
			
			$view->assign("records", $records);
			$view->assign("ajax", 1);
			$view->assign("parameters", $parameters);
			
			if(!empty($additionalVariables))
				$view->assignMultiple($additionalVariables);
			
			return $view->render();
		}
		
		return "";
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
	 * Gets merged filters
	 * 
	 * @return array
	 */
	protected function _getFilters()
	{
		/////////////////////////////////////////////////////////////////////////////////////////
		// Record Selection Type Filters
		/////////////////////////////////////////////////////////////////////////////////////////
		$selectionType 	= $this->listSettingsService->getRecordSelectionType();
		$selectFilters	= $this->_getAdditionalFiltersBySelectionType($selectionType);

		/////////////////////////////////////////////////////////////////////////////////////////
		// Field/Value Filter Settings
		/////////////////////////////////////////////////////////////////////////////////////////
		$fieldFilter	= $this->listSettingsService->getFieldValueFilters();

		/////////////////////////////////////////////////////////////////////////////////////////
		// Search Filters
		/////////////////////////////////////////////////////////////////////////////////////////
		$searchFields	= $this->sessionServiceContainer->getSearchSessionService()->getSearchFields();
		$searchString	= $this->sessionServiceContainer->getSearchSessionService()->getSearchString();
		$searchType		= $this->sessionServiceContainer->getSearchSessionService()->getSearchType();
		$searchFilters	= $this->_getAdditionalFiltersBySearch($searchType, $searchString, $searchFields);
		
		/////////////////////////////////////////////////////////////////////////////////////////
		// Filter Plugin Filters
		/////////////////////////////////////////////////////////////////////////////////////////
		$filter			= $this->sessionServiceContainer->getFilterSessionService()->getCleanSelectedOptions();

		/////////////////////////////////////////////////////////////////////////////////////////
		// Selection Plugin Filters
		/////////////////////////////////////////////////////////////////////////////////////////
		$selection		= $this->sessionServiceContainer->getSelectSessionService()->getSelectedRecords();
		$selectionFilters = $this->_getAdditionalFiltersByRecordSelection($selection);

		/////////////////////////////////////////////////////////////////////////////////////////
		// Letter Selection Plugin Filters
		/////////////////////////////////////////////////////////////////////////////////////////
		$letter			= $this->sessionServiceContainer->getLetterSessionService()->getSelectedLetter();
		$letterField	= $this->sessionServiceContainer->getLetterSessionService()->getLetterSelectionField();
		$letterFilters	= $this->_getAdditionalFiltersByLetterSelection($letter, $letterField);

		// Merging all Filters
		$filters	= array_merge($fieldFilter, $filter, $selectionFilters, $selectFilters, $searchFilters, $letterFilters);

		return $filters;
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
		// Limit
		$limit			= $this->listSettingsService->getLimitation();

		// Pager
		$perPage		= $this->sessionServiceContainer->getPagerSessionService()->getPerPage();
		$selectedPage	= $this->sessionServiceContainer->getPagerSessionService()->getSelectedPage();
		$page			= ($selectedPage*$perPage) - $perPage;
		
		// If nothing was set before, we use the per page setting from our records plugin
		if(is_null($perPage)) $perPage = $this->listSettingsService->getPerPage();
		
		if($perPage && $selectedPage > 0)
			$limit = "$page,{$perPage}";
			
		// Sorting
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
		
		// Retrieving all filters from different sources
		$filters = $this->_getFilters();
		
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
		[filter_field] => search|value_content

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
		$selectedRecordIds = [];
		if($records) 
		{
			foreach ($records as $_record) 
			{
				$_record->initializeValues();
				$selectedRecordIds[] = $_record->getUid();
			}
		}
		else
		{
			$records = [];
		}

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

		// We set these records as currently active
		$this->sessionServiceContainer->getInjectorSessionService()->setActiveRecordIds($selectedRecordIds);

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
				"filter_field" => "search",
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
				"filter_field" => "search",
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
					"filter_field" => "search",
				];
				break;
			case SearchSettingsService::SEARCH_RECORD_TITLE_FIELDS:
				$additionalFilters[] = [
					"field_id" => "RECORD.title",
					"filter_condition" => "like",
					"field_value" => "%{$searchString}%",
					"filter_combination" => "AND",
					"filter_field" => "search",
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
					"filter_field" => "search",
				];
				break;
			case ListSettingsService::SELECTION_TYPE_RECORDS:
				$selectedRecordIds = $this->listSettingsService->getSelectedRecordIds();
				$additionalFilters[] = [
					"field_id" => "RECORD.uid",
					"filter_condition" => "in",
					"field_value" => $selectedRecordIds,
					"filter_combination" => "AND",
					"filter_field" => "search",
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
					"filter_field" => "search",
				];
				
				$additionalFilters[] = [
					"field_id" => "RECORD.crdate",
					"filter_condition" => "lte",
					"field_value" => $dateTo->getTimestamp(),
					"filter_combination" => "AND",
					"filter_field" => "search",
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
					"filter_field" => "search",
				];

				$additionalFilters[] = [
					"field_id" => "RECORD.tstamp",
					"filter_condition" => "lte",
					"field_value" => $dateTo->getTimestamp(),
					"filter_combination" => "AND",
					"filter_field" => "search",
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
		return $this->getStandaloneView(true)->renderSource($string);
	}

	/**
	 * Gets a standalone view instance
	 * 
	 * @return \MageDeveloper\Dataviewer\Fluid\View\StandaloneView
	 */
	protected function getStandaloneView($includeVariables = false)
	{
		$view = $this->objectManager->get(\MageDeveloper\Dataviewer\Fluid\View\StandaloneView::class);

		if($includeVariables === true)
		{
			$variables = $this->variableRepository->findByStoragePids($this->storagePids);
			$ids = [];

			foreach($variables as $_v)
				$ids[] = $_v->getUid();

			$variables = $this->prepareVariables($ids);
			$view->assignMultiple($variables);
		}
		
		return $view;	
	}
}
