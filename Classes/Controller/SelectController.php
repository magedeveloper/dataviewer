<?php
namespace MageDeveloper\Dataviewer\Controller;

use MageDeveloper\Dataviewer\Service\Session\SelectSessionService;
use MageDeveloper\Dataviewer\Utility\LocalizationUtility;
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
class SelectController extends RecordController
{
	/***************************************************************************
	 * This controller manages the selection of records and storing
	 * the selection in the session for usage in the record plugin
	 ***************************************************************************/

	/**
	 * Select Setting Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Settings\Plugin\SelectSettingsService
	 * @inject
	 */
	protected $selectSettingsService;

	/**
	 * Search Session Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Session\SelectSessionService
	 * @inject
	 */
	protected $selectSessionService;

	/**
	 * Index Action to display the record selection form
	 *
	 * @return void
	 */
	public function indexAction()
	{
		$selectedRecords 	= $this->_getSelectedRecords();
		$selectedRecordIds 	= $this->selectSessionService->getSelectedRecords();

		$records = [];
		if(!$this->selectSessionService->isSetSelectedRecords()) 
		{
			$selectedRecordIds = $this->selectSettingsService->getPreselectedRecords();
			
			if(empty($selectedRecordIds))
				$selectedRecordIds = [0=>0];
			
			$this->selectSessionService->setSelectedRecords($selectedRecordIds);
			
			// We redirect to the current page in order to reload the selection and records
			// with the current values (this is mainly for the possibility, that the
			// display-records plugin is above the selection plugin
			$this->_redirectToPid();
		}
		
		foreach($selectedRecords as $_record)
		{
			/* @var \MageDeveloper\Dataviewer\Domain\Model\Record $_record */
			$label = $_record->getTitle();

			$records[] = [
				"label" 	=> $label,
				"record"	=> $_record,
				"selected"	=> (in_array($_record->getUid(), $selectedRecordIds)),
			];
		}
		
		$this->view->assign("records", $records);
		$this->view->assign("targetUid", $this->selectSettingsService->getTargetContentUid());
		$this->view->assign("selectionLimit", $this->selectSettingsService->getSelectionLimit());
		$this->view->assign("autoSubmit", $this->selectSettingsService->isAutoSubmit());
	}

	/**
	 * Action for handling the selection
	 * 
	 * @return void
	 */
	public function selectAction()
	{
		$selection = $this->request->getArgument("selection");
		
		if(!is_array($selection))
			$selection = [0];
		
		$validationErrors = $this->_validateSelection($selection);
		
		if(!empty($validationErrors))
		{
			foreach($validationErrors as $_error)
				$this->addFlashMessage($_error, "", \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
				
			$this->forward("index");	
		}
	
		// Save selection to the session
		$this->selectSessionService->setSelectedRecords($selection);
		
		$this->forward("index");
	}

	/**
	 * Validates the selection
	 * 
	 * @param array $selection
	 * @return bool
	 */
	protected function _validateSelection($selection)
	{
		$errors = [];
	
		$selectionLimit = $this->selectSettingsService->getSelectionLimit();
		
		if(is_array($selection) && (count($selection) > $selectionLimit))
			$errors[] = LocalizationUtility::translate("please_regard_selection_limit", [$selectionLimit]);
		
		return $errors;
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
		$uid = $this->selectSettingsService->getTargetContentUid();
		$sessionKey = SelectSessionService::SESSION_PREFIX_KEY;
		$this->selectSessionService->setPrefixKey("{$sessionKey}-{$uid}");

		// Parent
		parent::initializeView($view);
	}
}
