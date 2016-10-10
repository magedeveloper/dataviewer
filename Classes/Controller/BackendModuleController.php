<?php
namespace MageDeveloper\Dataviewer\Controller;

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
class BackendModuleController extends AbstractController
{
	/**
	 * Main Default Action to begin with
	 * 
	 * @var string
	 */
	protected $defaultAction = "records";

	/**
	 * The current selected page id
	 * 
	 * @var int
	 */
	protected $currentPageId;

	/**
	 * Excluded Arguments
	 * 
	 * @var array
	 */
	protected $excludedArgments = [];

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
	 * Default Index Action for deciding which
	 * last action was chosen
	 * 
	 * @return void
	 */
	public function indexAction()
	{
		$lastAction = $this->_getLastAction();
		$this->forward($lastAction);
	}

	/**
	 * Record Action for showing information about
	 * records on the selected page
	 * 
	 * @return void
	 */
	public function recordsAction()
	{
		$this->_storeLastAction();
	
		$searchString = null;
		if($this->request->hasArgument("searchString"))
			$searchString = $this->request->getArgument("searchString");
	
		$storagePids = [$this->currentPageId];
		$records = $this->recordRepository->findAll($storagePids, true);
		
		$datatypes = $this->datatypeRepository->findAllOnPid($this->currentPageId);
		
		$this->view->assign("records", $records);
		$this->view->assign("perPage", 30);
		$this->view->assign("searchString", $searchString);
		
		$this->view->assign("datatypes", $datatypes);
	}

	/**
	 * Datatypes Action for managing
	 * datatypes of the selected page
	 *
	 * @return void
	 */
	public function datatypesAction()
	{
		$this->_storeLastAction();

		$datatypes = $this->datatypeRepository->findAllOnPid($this->currentPageId);

		$this->view->assign("datatypes", $datatypes);
	}

	/**
	 * Datatypes Information Action for showing information
	 * about datatypes on the selected page
	 *
	 * @return void
	 */
	public function datatypesDetailsAction()
	{
		$this->_storeLastAction();
		$datatypes = $this->datatypeRepository->findAllOnPid($this->currentPageId);
		$this->view->assign("datatypes", $datatypes);
	}

	/**
	 * Records Information Action for showing information
	 * about records and their values on the selected page
	 *
	 * @return void
	 */
	public function recordsDetailsAction()
	{
		$this->_storeLastAction();
		$records = $this->recordRepository->findAll([$this->currentPageId], true, false);
		$datatypes = $this->datatypeRepository->findAllOnPid($this->currentPageId);
		
		$this->view->assign("records", $records);
		$this->view->assign("datatypes", $datatypes);
	}

	/**
	 * Initializes all actions.
	 *
	 * @return void
	 */
	protected function initializeAction() 
	{
		$this->currentPageId = (int)GeneralUtility::_GET("id");
		
		//$this->_storeActionName($actionName);
		parent::initializeAction();
	}

	/**
	 * Initializes the view before invoking an action method.
	 *
	 * Override this method to solve assign variables common for all actions
	 * or prepare the view in another way before the action is called.
	 *
	 * @param ViewInterface $view The view to be initialized
	 *
	 * @return void
	 * @api
	 */
	protected function initializeView(ViewInterface $view)
	{
		parent::initializeView($view);
		$this->view->assign("currentPageId", $this->currentPageId);
	}

	/**
	 * Makes action name from the current action method name.
	 *
	 * @return string
	 */
	protected function getActionName() 
	{
		return substr($this->actionMethodName, 0, -6);
	}

	/**
	 * Stores the last chosen action to the module data
	 * 
	 * @return void
	 */
	protected function _storeLastAction()
	{
		BackendUtility::getModuleData(
			["action" => ""],
			["action" => $this->getActionName()],
			"tx_dataviewer_web_dataviewerdataviewer"
		);
	}
	
	protected function _getLastAction()
	{
		$moduleData = BackendUtility::getModuleData(
			["action" => ""],
			[],
			"tx_dataviewer_web_dataviewerdataviewer"
		);
		
		if (!empty($moduleData)) 
		{
			if ($moduleData["action"] !== "" && $moduleData["action"] !== $this->getActionName()) 
			{
				return $moduleData["action"];
			}
		}
		
		return $this->defaultAction;
	}
}
