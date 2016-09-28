<?php
namespace MageDeveloper\Dataviewer\Controller;

use MageDeveloper\Dataviewer\Service\Session\SearchSessionService;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use MageDeveloper\Dataviewer\Utility\LocalizationUtility as Locale;

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
class SearchController extends AbstractController
{
	/***************************************************************************
	 * This controller manages the search form and adds the entered value
	 * to the session
	 ***************************************************************************/
	
	/**
	 * Search Setting Service
	 * 
	 * @var \MageDeveloper\Dataviewer\Service\Settings\Plugin\SearchSettingsService
	 * @inject
	 */
	protected $searchSettingsService;

	/**
	 * Search Session Service
	 * 
	 * @var \MageDeveloper\Dataviewer\Service\Session\SearchSessionService
	 * @inject
	 */
	protected $searchSessionService;

	/**
	 * Search Index to display the search form
	 * 
	 * @param string $searchString
	 * @return void
	 */
	public function indexAction($searchString = '')
	{
		if($this->searchSessionService->getSearchString() && $this->searchSettingsService->getClearOnPageLoad() && $searchString == "")
			$this->searchSessionService->reset();
	
		$searchString 		= ($searchString != "")?$searchString:$this->searchSessionService->getSearchString();

		$min 				= $this->searchSettingsService->getMinimumChars();
		$searchStringLen 	= strlen($searchString);

		if ($searchStringLen == 0)
		{
			$this->searchSessionService->reset();
		}
		elseif ($searchStringLen > 0 && $searchStringLen < $min)
		{
			$this->addFlashMessage(Locale::translate("search_requires_characters", $min));
			$this->searchSessionService->reset();
			$searchString = '';
		}
		
		$this->view->assign("searchString", $searchString);
		$this->view->assign("targetUid", $this->searchSettingsService->getTargetContentUid());
	}

	/**
	 * Action for putting the searchstring to the session
	 * and redirecting back to index to fulfill a new
	 * page load
	 * 
	 * @param string $searchString
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
	 */
	public function searchAction($searchString = '')
	{
		if(!$this->_checkTargetUid())
			$this->redirect("index");
	
		$searchType 	= $this->searchSettingsService->getSearchType();
		$searchFields	= $this->searchSettingsService->getSearchFields();

		//////////////////////////////////////////////////////
		// Signal-Slot for processing the search parameters //
		//////////////////////////////////////////////////////
		$this->signalSlotDispatcher->dispatch(
			__CLASS__,
			"searchPostProcess",
			[
				&$searchFields,
				&$searchType,
				&$searchString,
				&$this,
			]
		);

		$this->searchSessionService->setSearchFields($searchFields);
		$this->searchSessionService->setSearchString($searchString);
		$this->searchSessionService->setSearchType($searchType);
		
		if($this->searchSettingsService->getClearOnPageLoad())
			$this->redirect("index", null, null, ["searchString" => $searchString]);
		else
			$this->redirect("index");
			
		exit();
	}

	/**
	 * Action for resetting the search
	 * 
	 * @return void
	 */
	public function resetAction()
	{
		if(!$this->_checkTargetUid())
			$this->redirect("index");
	
		$this->searchSessionService->reset();
		$this->redirect("index");
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
		$uid = $this->searchSettingsService->getTargetContentUid();
		$sessionKey = SearchSessionService::SESSION_PREFIX_KEY;
		$this->searchSessionService->setPrefixKey("{$sessionKey}-{$uid}");

		// Parent
		parent::initializeView($view);
	}
}
