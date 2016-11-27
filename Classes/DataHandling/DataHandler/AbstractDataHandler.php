<?php
namespace MageDeveloper\Dataviewer\DataHandling\DataHandler;

use MageDeveloper\Dataviewer\Utility\ArrayUtility;
use MageDeveloper\Dataviewer\Utility\LocalizationUtility as Locale;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * MageDeveloper Dataviewer Extension
 * -----------------------------------
 *
 * @category    TYPO3 Extension
 * @package     MageDeveloper\Dataviewer
 * @author		Bastian Zagar

 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AbstractDataHandler
{
	/**
	 * Object Manager
	 *
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Persistence Managaer
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
	 * @inject
	 */
	protected $persistenceManager;

	/**
	 * Data Handler
	 *
	 * @var \MageDeveloper\Dataviewer\DataHandling\DataHandler
	 * @inject
	 */
	protected $dataHandler;
	
	/**
	 * Flexform Tools
	 *
	 * @var \TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools
	 * @inject
	 */
	protected $flexTools;

	/**
	 * Fieldtype Settings Service
	 * 
	 * @var \MageDeveloper\Dataviewer\Service\Settings\FieldtypeSettingsService
	 * @inject
	 */
	protected $fieldtypeSettingsService;

	/**
	 * Subsitute Array NEW-IDs with IDs
	 *
	 * @var array
	 */
	protected $substNEWwithIDs = [];

	/**
	 * Save Data
	 * 
	 * @var array
	 */
	protected $saveData = [];

	/**
	 * Constructor
	 *
	 * @return AbstractDataHandler
	 */
	public function __construct()
	{
		$this->objectManager 			= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->persistenceManager 		= $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager::class);
		$this->dataHandler				= $this->objectManager->get(\MageDeveloper\Dataviewer\DataHandling\DataHandler::class);
		$this->flexTools 				= $this->objectManager->get(\TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools::class);
		$this->fieldtypeSettingsService	= $this->objectManager->get(\MageDeveloper\Dataviewer\Service\Settings\FieldtypeSettingsService::class);
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
	 * Redirects to a record edit page
	 *
	 * @param int $id
	 * @return void
	 */
	protected function _redirectRecord($id)
	{
		$url = BackendUtility::getModuleUrl(
			'record_edit',
			[
				'edit[tx_dataviewer_domain_model_record][' . $id . ']' => 'edit',
				'returnUrl' => GeneralUtility::getIndpEnv('REQUEST_URI')
			]
		);
	
		\TYPO3\CMS\Core\Utility\HttpUtility::redirect($url);
		exit();
	}

	/**
	 * Redirects to the current url
	 *
	 * @return void
	 */
	protected function _redirectCurrentUrl()
	{
		$link = GeneralUtility::linkThisScript(GeneralUtility::_GET());
		\TYPO3\CMS\Core\Utility\HttpUtility::redirect( GeneralUtility::sanitizeLocalUrl($link) );
		exit();
	}

	/**
	 * Substitutes all NEW-Elements with their according ids
	 *
	 * @param array $arrayToSubstitute
	 * @return array
	 */
	protected function _substituteArrayNEWwithIds($arrayToSubstitute = [])
	{
		if (!is_array($arrayToSubstitute)) return [];

		$substituted = [];
		foreach($arrayToSubstitute as $_key=>$_value)
		{
			$sKey = $this->_substituteStringNEWwithIds($_key);

			if (is_array($_value))
				$sValue = $this->_substituteArrayNEWwithIds($_value);
			else
				$sValue = $this->_substituteStringNEWwithIds($_value);

			$substituted[$sKey] = $sValue;
		}

		return $substituted;
	}

	/**
	 * Substitutes a string with the known ids
	 *
	 * @param string $string
	 * @return string
	 */
	protected function _substituteStringNEWwithIds($string)
	{
		if(isset($this->substNEWwithIDs[$string]))
			return $this->substNEWwithIDs[$string];

		return $string;
	}

	/**
	 * Processes uploads
	 * 
	 * @param array $files
	 */
	protected function _processUploads($files)
	{
		$this->dataHandler->process_uploads($files);
	}
}
