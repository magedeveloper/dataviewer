<?php
namespace MageDeveloper\Dataviewer\Controller;

use MageDeveloper\Dataviewer\Service\Session\PagerSessionService;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

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
class PagerController extends RecordController
{
	/***************************************************************************
	 * This controller adds a pager to the site for paging records of
	 * a selected records plugin
	 ***************************************************************************/

	/**
	 * Pager Session Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Session\PagerSessionService
	 * @inject
	 */
	protected $pagerSessionService;

	/**
	 * Pager Plugin Settings Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Settings\Plugin\PagerSettingsService
	 * @inject
	 */
	protected $pagerSettingsService;

	/**
	 * Flexform Service
	 * 
	 * @var \MageDeveloper\Dataviewer\Service\FlexFormService
	 * @inject
	 */
	protected $flexformService;

	/**
	 * Target Plugin Settings
	 * 
	 * @var array
	 */
	protected $targetSettings = [];

	/**
	 * Index Action
	 * Displays the pager
	 *
	 * @return void
	 */
	public function indexAction()
	{
		$perPage 		= $this->pagerSessionService->getPerPage();
		$selectedPage	= $this->pagerSessionService->getSelectedPage();
		
		// We clear the pager if we reload the page manually
		if($this->pagerSettingsService->getClearOnPageLoad() && 
		   empty($this->request->getArguments()))
		{
			$this->forward("reset");
		}
	
		if(!$selectedPage) $selectedPage = 1;
		
		if(!is_int($perPage))
			$perPage = $this->_getTargetSetting("per_page");
			
		// Adding the evaluated value back to the session for record plugin handling
		$this->pagerSessionService->setPerPage($perPage);

		// Get the overall records count of the selected
		// records plugin
		$recordCount = $this->_getRecordsCount();
		
		$pagesCount = 1;
		if($perPage != 0)
			$pagesCount = ceil($recordCount / $perPage);
	
		$pages = range(1, $pagesCount);
		
		if($selectedPage < $pagesCount)
			$nextPage = $selectedPage+1;
			
		if($selectedPage > 1)
			$previousPage = $selectedPage-1;

		$endRecordNumber = $selectedPage*$perPage;
		$startRecordNumber = ($endRecordNumber-$perPage)+1;
		
		if($startRecordNumber > $recordCount)
			$startRecordNumber = 1;
		
		if($endRecordNumber > $recordCount)
			$endRecordNumber = $recordCount;
			
		if($endRecordNumber == 0 && $perPage == 0)
			$endRecordNumber = $recordCount;
			
			
		$this->view->assign("selectedPage", $selectedPage);
		$this->view->assign("nextPage", $nextPage);
		$this->view->assign("previousPage", $previousPage);
		
		$this->view->assign("startRecordNumber", $startRecordNumber);
		$this->view->assign("endRecordNumber", $endRecordNumber);
		
		$this->view->assign("perPage", $perPage);
		$this->view->assign("perPageOptions", $this->pagerSettingsService->getPerPageFields());
		
		$this->view->assign("recordCount", $recordCount);
		$this->view->assign("pagesCount", $pagesCount);
		$this->view->assign("pages", $pages);

		$this->view->assign("targetUid", $this->pagerSettingsService->getTargetContentUid());
		$this->view->assign("showViewAll", $this->pagerSettingsService->showViewAll());
	}

	/**
	 * Action for selecting a page
	 *
	 * @param int $perPage
	 * @param int $page
	 * @return void
	 */
	public function pageAction($perPage = null, $page = null)
	{
		if(!$this->_checkTargetUid())
			$this->redirect("index");

		if(!is_null($page))
		{
			$this->pagerSessionService->setSelectedPage((int)$page);
		}
		
		if(!is_null($perPage))
		{
			$perPageOptions = $this->pagerSettingsService->getPerPageFields();

			/********************
			 * Validate Per Page
			 ********************/
			if (!in_array($perPage, $perPageOptions) && $perPage > 0)
				$perPage = reset($perPageOptions);
				
			$this->pagerSessionService->setPerPage((int)$perPage);
			$this->pagerSessionService->setSelectedPage(1);
		}

		$this->redirect("index");

		exit();
	}

	/**
	 * Action for resetting the pager
	 * 
	 * @return void
	 */
	public function resetAction()
	{
		$this->pagerSessionService->reset();
		$this->redirect("index");
	}

	/**
	 * Gets the overall records count
	 * 
	 * @return int
	 */
	protected function _getRecordsCount()
	{
		$targetUid = $this->pagerSettingsService->getTargetContentUid();
		$record = BackendUtility::getRecord("tt_content", $targetUid);

		if(is_array($record) && isset($record["uid"]))
		{
			// Setting the storage pids of the target plugin
			$storagePids = GeneralUtility::trimExplode(",", $record["pages"]);
			$this->storagePids = $storagePids;
			
			// Retrieving the settings of the target plugin
			$targetSettings = $this->_getSettingsOfTargetPlugin();
			
			// We need to create the List Settings Service here and inject the Target Plugin Settings
			// to receive the correct settings
			$this->listSettingsService->setSettings($targetSettings);
			$this->sessionServiceContainer->setTargetUid($targetUid);
			
			// Retrieve filters from different sources like in the normal records plugin
			$filters = $this->_getFilters();

			// Replace markers in the filters
			$this->_replaceMarkersInFilters($filters);

			// We do nearly the same request to observe the records count but with a COUNT()
			// for faster access
			$count = $this->recordRepository->countByConditions($filters, $this->storagePids);
			
			return (int)$count;
		}

		return 0;
	}

	/**
	 * Gets a setting from the target plugin settings
	 * 
	 * @param string $setting
	 * @return mixed|null
	 */
	protected function _getTargetSetting($setting)
	{
		$settings = $this->_getSettingsOfTargetPlugin();
		
		if(isset($settings[$setting]))
			return $settings[$setting];
			
		return null;	
	}

	/**
	 * Gets the settings of the target plugin
	 * 
	 * @return array
	 */
	protected function _getSettingsOfTargetPlugin()
	{
		$targetUid = $this->pagerSettingsService->getTargetContentUid();
		$record = BackendUtility::getRecord("tt_content", $targetUid);
		
		if(is_array($record) && isset($record["uid"]))
		{
			$flexformArr = $this->flexformService->convertFlexFormContentToArray($record["pi_flexform"]);
			if(is_array($flexformArr) && isset($flexformArr["settings"]))
				$this->targetSettings = $flexformArr["settings"];		
		}
	
		return $this->targetSettings;
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
		$uid = $this->pagerSettingsService->getTargetContentUid();
		$sessionKey = PagerSessionService::SESSION_PREFIX_KEY;
		$this->pagerSessionService->setPrefixKey("{$sessionKey}-{$uid}");

		// Parent
		parent::initializeView($view);
	}

}
