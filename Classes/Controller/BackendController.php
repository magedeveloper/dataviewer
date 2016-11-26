<?php
namespace MageDeveloper\Dataviewer\Controller;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Core\Messaging\FlashMessage;

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
class BackendController extends AbstractController
{
	/**
	 * Main Default Action to start with
	 *
	 * @var string
	 */
	protected $defaultAction = "createRecord";

	/**
	 * Main Default Controller to start with
	 * 
	 * @var string
	 */
	protected $defaultContoller = "BackendModuleController";

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
		$this->view->assign("recordsName", $this->pluginSettingsService->getRecordsVarName());
		$this->view->assign("recordName", $this->pluginSettingsService->getRecordVarName());
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
	 * Makes controller name from the controller class name.
	 *
	 * @return string
	 */
	protected function getControllerName() 
	{
		return (string)preg_replace('/^.*\\\([^\\\]+)Controller$/', '\1', get_class($this));
	}

	/**
	 * Adds a flash message to the backend
	 *
	 * @param FlashMessage $message
	 * @throws \TYPO3\CMS\Core\Exception
	 */
	public function addBackendFlashMessage($message, $title = '', $severity = FlashMessage::ERROR)
	{
		$flashMessage = $this->getFlashMessage($message, $title, $severity);
		$this->addMessageToQueue($flashMessage);
	}

	/**
	 * Adds an message to the message queue
	 *
	 * @param \TYPO3\CMS\Core\Messaging\FlashMessage $message
	 * @throws \TYPO3\CMS\Core\Exception
	 */
	public function addMessageToQueue(FlashMessage $message)
	{
		/** @var $flashMessageService \TYPO3\CMS\Core\Messaging\FlashMessageService */
		$flashMessageService = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Messaging\FlashMessageService::class);
		$defaultFlashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
		$defaultFlashMessageQueue->enqueue($message);
	}

	/**
	 * Gets a new flash message
	 *
	 * @param string $message
	 * @param string $title
	 * @param int $severity
	 * @return FlashMessage
	 */
	public function getFlashMessage($message, $title = '', $severity = FlashMessage::ERROR)
	{
		return GeneralUtility::makeInstance(
			\TYPO3\CMS\Core\Messaging\FlashMessage::class,
			$message,
			$title,
			$severity,
			TRUE
		);
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

		BackendUtility::getModuleData(
			["controller" => ""],
			["controller" => $this->getControllerName()],
			"tx_dataviewer_web_dataviewerdataviewer"
		);
	}

	/**
	 * Gets the last used action in the backend module
	 * 
	 * @return string
	 */
	protected function _getLastAction()
	{
		$moduleData = BackendUtility::getModuleData(
			["action" => ""],
			[],
			"tx_dataviewer_web_dataviewerdataviewer"
		);

		if (!empty($moduleData))
		{
			if ($moduleData["action"] !== "")
			{
				return $moduleData["action"];
			}
		}

		return $this->defaultAction;
	}

	/**
	 * Gets the last used action in the backend module
	 *
	 * @return string
	 */
	protected function _getLastController()
	{
		$moduleData = BackendUtility::getModuleData(
			["controller" => ""],
			[],
			"tx_dataviewer_web_dataviewerdataviewer"
		);

		if (!empty($moduleData))
		{
			if ($moduleData["controller"] !== "" && $moduleData["controller"] !== $this->getControllerName())
			{
				return $moduleData["controller"];
			}
		}

		return $this->defaultController;
	}
}
