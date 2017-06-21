<?php
namespace MageDeveloper\Dataviewer\Hooks;

use MageDeveloper\Dataviewer\Utility\LocalizationUtility as Locale;
use TYPO3\CMS\Backend\RecordList\RecordListGetTableHookInterface;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Recordlist\RecordList\RecordListHookInterface;

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
class RecordListHeader implements RecordListHookInterface
{
	/**
	 * Object Manager
	 *
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Datatype Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\DatatypeRepository
	 * @inject
	 */
	protected $datatypeRepository;

	/**
	 * Record Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\RecordRepository
	 * @inject
	 */
	protected $recordRepository;

	/**
	 * Field Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\FieldRepository
	 * @inject
	 */
	protected $fieldRepository;

	/**
	 * Backend Session Service
	 * 
	 * @var \MageDeveloper\Dataviewer\Service\Session\BackendSessionService
	 * @inject
	 */
	protected $backendSessionService;

	/**
	 * Icon Factory
	 *
	 * @var \TYPO3\CMS\Core\Imaging\IconFactory
	 * @inject
	 */
	protected $iconFactory;

	/**
	 * Constructor
	 *
	 * @return RecordListHeader
	 */
	public function __construct()
	{
		$this->objectManager    		= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->datatypeRepository 		= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\DatatypeRepository::class);
		$this->recordRepository 		= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\RecordRepository::class);
		$this->fieldRepository	 		= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\FieldRepository::class);
		$this->backendSessionService 	= $this->objectManager->get(\MageDeveloper\Dataviewer\Service\Session\BackendSessionService::class);
		$this->iconFactory 				= $this->objectManager->get(\TYPO3\CMS\Core\Imaging\IconFactory::class);
	}

	/**
	 * Modifies Web>List clip icons (copy, cut, paste, etc.) of a displayed row
	 *
	 * @param string $table The current database table
	 * @param array $row The current record row
	 * @param array $cells The default clip-icons to get modified
	 * @param object $parentObject Instance of calling object
	 * @return array The modified clip-icons
	 */
	public function makeClip($table, $row, $cells, &$parentObject)
	{
		return $cells;
	}

	/**
	 * Modifies Web>List control icons of a displayed row
	 *
	 * @param string $table The current database table
	 * @param array $row The current record row
	 * @param array $cells The default control-icons to get modified
	 * @param object $parentObject Instance of calling object
	 * @return array The modified control-icons
	 */
	public function makeControl($table, $row, $cells, &$parentObject)
	{
		return $cells;
	}

	/**
	 * Modifies Web>List header row columns/cells
	 *
	 * @param string $table The current database table
	 * @param array $currentIdList Array of the currently displayed uids of the table
	 * @param array $headerColumns An array of rendered cells/columns
	 * @param object $parentObject Instance of calling (parent) object
	 * @return array Array of modified cells/columns
	 */
	public function renderListHeader($table, $currentIdList, $headerColumns, &$parentObject)
	{
		if($table != "tx_dataviewer_domain_model_record" || $parentObject->searchString != "") return $headerColumns;

		$pid = $parentObject->id;
		
		// We set a pid here, so we can store/get only the information of the selected page
		$this->backendSessionService->setAccordingPid($pid);

		$scriptUrl = $parentObject->thisScript."&id=".$pid;
		
		$sortByOptions = [
			"sorting" 	=> "sorting",
			"uid" 		=> "uid",
			"title" 	=> "title",
			"tstamp" 	=> "tstamp",
			"crdate" 	=> "crdate",
		];

		$pid = $parentObject->pageRow["uid"];

		if($pid > 0)
		{
			// We get all datatypes from records on this page and
			// use their fields to fill the sort by options
			$datatypes = $this->datatypeRepository->findAllOfRecordsOnPid([$pid]);

			foreach($datatypes as $_datatype)
			{
				/* @var \MageDeveloper\Dataviewer\Domain\Model\Datatype $_datatype */
				$fields = $_datatype->getFields();

				// Adding the fields to the sortBy Options
				if(count($fields))
				{
					foreach($fields as $_field)
					{
						$label = "[{$_field->getPid()}] " . strtoupper($_field->getType()) . ": " . $_field->getFrontendLabel();
						$sortByOptions[$_field->getUid()] = $label;
					}
				}
			}
		}
		
		$sortBy 	= $this->backendSessionService->getSortBy();
		$sortOrder 	= $this->backendSessionService->getSortOrder();
		$addInfo 	= (bool)$this->backendSessionService->getAddInfo();
		
		if(isset($_POST["sort"]) && in_array($_POST["sort"], array_keys($sortByOptions)))
		{	
			$sortOrder = ($_POST["sortOrder"] == "asc")?"asc":"desc";
			$this->backendSessionService->setSortOrder($sortOrder);
			$this->backendSessionService->setSortBy($_POST["sort"]);
			$this->backendSessionService->setSearch($_POST["search"]);
			
			if($_POST["addInfo"] == "1")
				$this->backendSessionService->setAddInfo((bool)!$addInfo);
	
			$this->_redirect($scriptUrl);
			exit();
		}
		
		$html = "";
		
		// Creating the form element html code that will be attached to the panel title field
		$html .= "<div class=\"input-group\" style=\"margin-top:-18px;\">";
		$html .= "<span class=\"input-group-addon\">";
		$html .= Locale::translate("flexform.sorting");
		$html .= "</span>";
		
		// Selector Box for the Sort By Options
		$html .= "<select id=\"sort\" name=\"sort\" class=\"form-control form-control-adapt\" style=\"height:33px;\" onchange=\"this.form.submit()\">";
		foreach($sortByOptions as $_optionValue=>$_optionLabel)
		{
			$selected = ($sortBy == $_optionValue)?"selected":""; 
			$html .= "<option value=\"{$_optionValue}\" {$selected}>{$_optionLabel}</option>";
		}
		$html .= "</select>";

		// Selector Box for the Sort Order Direction
		$html .= "<span class=\"input-group-btn\" style=\"display:table-cell;\">";

		$iconInfo = ($addInfo === false)?"actions-edit-localize-status-high":"actions-edit-localize-status-low";
		$html .= "<button type=\"submit\" title=\"".Locale::translate("EXT:lang/Resources/Private/Language/locallang_mod_web_list.xlf:largeControl")."\" value=\"1\" name=\"addInfo\" class=\"btn btn-default\" style=\"height: 33px; background-color: rgb(245, 245, 245); \">";
		$html .= $this->iconFactory->getIcon($iconInfo, Icon::SIZE_SMALL)->render();;
		$html .= "</button>";

		$value = ($sortOrder == "asc")?"desc":"asc";
		
		$iconSorting = $this->iconFactory->getIcon('actions-view-go-down', Icon::SIZE_SMALL)->render();
		if($value == "desc")
			$iconSorting = $this->iconFactory->getIcon('actions-view-go-up', Icon::SIZE_SMALL)->render();
		$html .= "<button title=\"".Locale::translate("sort.{$value}")."\" type=\"submit\" name=\"sortOrder\" value=\"{$value}\" class=\"btn btn-default\" style=\"height: 33px; background-color: rgb(245, 245, 245); border: 1px solid #c0c0c0;\">";
		$html .= $iconSorting;
		$html .= "</button>";

		$html .= "</span>";
		
		$html .= "</div>";

		// Attaching the sorting form to the control area
		$headerColumns["_CONTROL_"] .= $html;

		$search = $this->backendSessionService->getSearch();
		$htmlSearch = "";
		$htmlSearch .= "<div class=\"input-group\">";
		$htmlSearch .= "<span class=\"input-group-addon\">";
		$htmlSearch .= Locale::translate("record_title");
		$htmlSearch .= "</span>";
		$htmlSearch .= "<input id=\"search\" value=\"{$search}\" name=\"search\" class=\"form-control form-control-adapt\" style=\"height:33px; width:90% !important;\">";
        $htmlSearch .= "<button type=\"submit\" name=\"sortOrder\" value=\"{$value}\" class=\"btn btn-default\" style=\"height: 33px; background-color: rgb(245, 245, 245); border-left: 0;\">";
		$htmlSearch .= $this->iconFactory->getIcon('actions-search', Icon::SIZE_SMALL)->render();;
		$htmlSearch .= "</button>";

		$htmlSearch .= "</div>";
		$headerColumns["title"] = $htmlSearch;


		if(GeneralUtility::_GET("M") !== "web_list")
		{
			// We are not in web list, so we need to include the form tag in order
			// to restore functionality of a form post
			$formTag = "<form method=\"post\" action=\"{$scriptUrl}\">";
			$headerColumns["title"] 	= $formTag.$headerColumns["title"];
			$headerColumns["_CONTROL_"] = $formTag.$headerColumns["_CONTROL_"]."</form>";
		}
		
		
		return $headerColumns;
	}

	/**
	 * Modifies Web>List header row clipboard/action icons
	 *
	 * @param string $table The current database table
	 * @param array $currentIdList Array of the currently displayed uids of the table
	 * @param array $cells An array of the current clipboard/action icons
	 * @param object $parentObject Instance of calling (parent) object
	 * @return array Array of modified clipboard/action icons
	 */
	public function renderListHeaderActions($table, $currentIdList, $cells, &$parentObject)
	{
		return $cells;
	}

	/**
	 * Redirects to a url
	 * 
	 * @param string $url
	 * @return void
	 */
	protected function _redirect($url)
	{
		\TYPO3\CMS\Core\Utility\HttpUtility::redirect( $url );
	}
}
