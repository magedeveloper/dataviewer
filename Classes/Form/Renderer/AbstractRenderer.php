<?php
namespace MageDeveloper\Dataviewer\Form\Renderer;

use MageDeveloper\Dataviewer\Utility\LocalizationUtility as Locale;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;

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
abstract class AbstractRenderer
{
	/**
	 * The record id,
	 * either NEW<hash> or an INT-Id
	 * 
	 * @var string|int
	 */
	protected $recordId;

	/**
	 * FlexForm Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\FlexFormService
	 * @inject
	 */
	protected $flexFormService;

	/**
	 * Plugin Settings Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Settings\Plugin\PluginSettingsService
	 * @inject
	 */
	protected $pluginSettingsService;

	/**
	 * Datatype Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\DatatypeRepository
	 * @inject
	 */
	protected $datatypeRepository;

	/**
	 * Field Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\FieldRepository
	 * @inject
	 */
	protected $fieldRepository;

	/**
	 * Record Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\RecordRepository
	 * @inject
	 */
	protected $recordRepository;

	/**
	 * Object Manager
	 *
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

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
	 * @return AbstractRenderer
	 */
	public function __construct()
	{
		$this->objectManager 	= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);

		$this->pluginSettingsService	= $this->objectManager->get(\MageDeveloper\Dataviewer\Service\Settings\Plugin\PluginSettingsService::class);
		$this->flexFormService  		= $this->objectManager->get(\MageDeveloper\Dataviewer\Service\FlexFormService::class);
		$this->datatypeRepository		= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\DatatypeRepository::class);
		$this->fieldRepository			= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\FieldRepository::class);
		$this->recordRepository		 	= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\RecordRepository::class);
		$this->iconFactory					= $this->objectManager->get(\TYPO3\CMS\Core\Imaging\IconFactory::class);
		
		/*
		$backend = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\BackendInterface::class);
		//$dataMapRecord = $backend->getDataMapper()->getDataMap("MageDeveloper\\Dataviewer\\Domain\\Model\\Record");
		//$dataMapRecord->setTableName("tx_dataviewer_domain_model_record_external");
		$dataMapRecordValue = $backend->getDataMapper()->getDataMap("MageDeveloper\\Dataviewer\\Domain\\Model\\RecordValue");
		$dataMapRecordValue->setTableName("tx_dataviewer_domain_model_recordvalue_external");
		*/
	}

	/**
	 * Adds a flash message to the backend
	 *
	 * @param string $message
	 * @param string $title
	 * @param int $severity
	 * @throws \TYPO3\CMS\Core\Exception
	 */
	public function addBackendFlashMessage($message, $title = '', $severity = FlashMessage::ERROR)
	{
		/** @var $flashMessage FlashMessage */
		$flashMessage = GeneralUtility::makeInstance(
			\TYPO3\CMS\Core\Messaging\FlashMessage::class,
			$message,
			(string)$title,
			$severity,
			TRUE
		);

		/** @var $flashMessageService \TYPO3\CMS\Core\Messaging\FlashMessageService */
		$flashMessageService = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Messaging\FlashMessageService::class);
		$defaultFlashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
		$defaultFlashMessageQueue->enqueue($flashMessage);
	}

	/**
	 * Gets flash message html code
	 *
	 * @param string $message
	 * @param string $title
	 * @param int $severity
	 * @return string
	 */
	public function getMessageHtml($message, $title = null, $severity = FlashMessage::ERROR)
	{
		// Usage of the new infoboxes
		if ($this->objectManager->isRegistered(\TYPO3\CMS\Fluid\ViewHelpers\Be\InfoboxViewHelper::class))
		{
			// Obtain same states for flashMessage Type <=> InfoboxViewHelper State
			switch($severity)
			{
				case FlashMessage::ERROR:
					$severity = \TYPO3\CMS\Fluid\ViewHelpers\Be\InfoboxViewHelper::STATE_ERROR;
					break;
				case FlashMessage::INFO:
					$severity = \TYPO3\CMS\Fluid\ViewHelpers\Be\InfoboxViewHelper::STATE_INFO;
					break;
				case FlashMessage::NOTICE:
					$severity = \TYPO3\CMS\Fluid\ViewHelpers\Be\InfoboxViewHelper::STATE_NOTICE;
					break;
				case FlashMessage::OK:
					$severity = \TYPO3\CMS\Fluid\ViewHelpers\Be\InfoboxViewHelper::STATE_OK;
					break;
				case FlashMessage::WARNING:
					$severity = \TYPO3\CMS\Fluid\ViewHelpers\Be\InfoboxViewHelper::STATE_WARNING;
					break;
			}

			/* @var \TYPO3\CMS\Fluid\ViewHelpers\Be\InfoboxViewHelper $infoboxViewHelper */
			$infoboxViewHelper = $this->objectManager->get(\TYPO3\CMS\Fluid\ViewHelpers\Be\InfoboxViewHelper::class);
			$infoboxViewHelper->setRenderingContext( new RenderingContext() );

			$arguments = [
				"title" => $title,
				"message" => $message,
				"state" => $severity,
			];

			$html = \TYPO3\CMS\Fluid\ViewHelpers\Be\InfoboxViewHelper::renderStatic($arguments, function() use ($message){return $message;}, new RenderingContext());

			return $html;
		}

		$flashMessage = $this->objectManager->get(\TYPO3\CMS\Core\Messaging\FlashMessage::class, $message, $title, $severity);
		return $flashMessage->render();
	}

}	
