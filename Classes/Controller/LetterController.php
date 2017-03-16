<?php
namespace MageDeveloper\Dataviewer\Controller;

use MageDeveloper\Dataviewer\Service\Session\LetterSessionService;
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
class LetterController extends AbstractController
{
	/***************************************************************************
	 * This controller manages the letter selection and stores the selected
	 * value in the session
	 ***************************************************************************/
	
	/**
	 * Letter Selection Session Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Session\LetterSessionService
	 * @inject
	 */
	protected $letterSessionService;

	/**
	 * Letter Selection Plugin Settings Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Settings\Plugin\LetterSettingsService
	 * @inject
	 */
	protected $letterSettingsService;

	/**
	 * Index Action
	 * Displays the letter selection
	 *
	 * @return void
	 */
	public function indexAction()
	{
        $active = $this->_getSelectedLetter();
        $preselectedLetter = $this->letterSettingsService->getPreselectedLetter();

        if($this->letterSettingsService->getClearOnPageLoad() &&
            !$this->_hasDataviewerArguments() &&
            ($active != $preselectedLetter)
        )
        {
            $this->letterSessionService->setSelectedLetter($preselectedLetter);
            $this->_redirectToPid();
        }
	
		// Get all letters
		$letters = $this->letterSettingsService->getLetters();
		
		// Add letter selection field to the session
		$field = $this->letterSettingsService->getLetterSelectionField();

		// Set active letter selection field
		$this->letterSessionService->setLetterSelectionField($field);
		$this->letterSessionService->setSelectedLetter($active);

		///////////////////////////////////////////////////////////////////////////////
		// Signal-Slot for the handling of the letters and the current active letter //
		///////////////////////////////////////////////////////////////////////////////
		$this->signalSlotDispatcher->dispatch(
			__CLASS__,
			"prepareLetters",
			[
				&$letters,
				&$active,
				&$this,
			]
		);
		
		$this->view->assign("active", $active);
		$this->view->assign("letters", $letters);
		$this->view->assign("targetUid", $this->letterSettingsService->getTargetContentUid());
	}

	/**
	 * Gets the selected letter
	 * 
	 * @return string
	 */
	protected function _getSelectedLetter()
	{
		// Retrieve preselected letter
		$preselectedLetter = $this->letterSettingsService->getPreselectedLetter();

		$active = null;
		if ($this->request->hasArgument("letter") && $this->_checkTargetUid())
		{
			if (strlen($this->request->getArgument("letter")) == 1)
				$active = $this->request->getArgument("letter");
		}
		else
		{
			if (!is_null($preselectedLetter) && $preselectedLetter != "0")
				$active = $preselectedLetter;

			// We assign the selected letter, if the session contains one
			if($sessionSelectedLetter = $this->letterSessionService->getSelectedLetter())
				$active = $sessionSelectedLetter;
		}
		
		return $active;
	}

	/**
	 * Action for selecting a letter
	 * 
	 * @return void
	 */
	public function letterAction()
	{
        if(!$this->_checkTargetUid())
            $this->redirect("index");
	
		$active = $this->_getSelectedLetter();
		
		// Add letter selection field to the session
		$field = $this->letterSettingsService->getLetterSelectionField();

		// Set active letter selection field
		$this->letterSessionService->setLetterSelectionField($field);
		$this->letterSessionService->setSelectedLetter($active);

		$this->forward("index");		
		exit();		
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
		$uid = $this->letterSettingsService->getTargetContentUid();
		$sessionKey = LetterSessionService::SESSION_PREFIX_KEY;
		$this->letterSessionService->setPrefixKey("{$sessionKey}-{$uid}");

		// Parent
		parent::initializeView($view);
	}
	 
}
