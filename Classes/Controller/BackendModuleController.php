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
class BackendModuleController extends BackendController
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
		$lastController = $this->_getLastController();
		
		$this->forward($lastAction, $lastController);
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
     * Overview of all datatype for creating records
     *
     * @return void
     */
	public function createRecordAction()
    {
        $this->_storeLastAction();
        $datatypes = $this->datatypeRepository->findAll(false);
        $this->view->assign("datatypes", $datatypes);
    }

}
