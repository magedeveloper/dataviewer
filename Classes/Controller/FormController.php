<?php
namespace MageDeveloper\Dataviewer\Controller;

use MageDeveloper\Dataviewer\Domain\Model\Field;
use MageDeveloper\Dataviewer\Domain\Model\Record;
use MageDeveloper\Dataviewer\Domain\Model\Variable;
use MageDeveloper\Dataviewer\Service\Settings\Plugin\ListSettingsService;
use MageDeveloper\Dataviewer\Service\Settings\Plugin\SearchSettingsService;
use MageDeveloper\Dataviewer\Service\Settings\Plugin\FormSettingsService;
use MageDeveloper\Dataviewer\Utility\DebugUtility;
use MageDeveloper\Dataviewer\Utility\LocalizationUtility as Locale;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentNameException;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

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
class FormController extends AbstractController
{
	/***************************************************************************
	 * This controller manages a form post with certain datatype contents
	 * that will automatically create a new record with the posted
	 * form data.
	 ***************************************************************************/

	/**
	 * Storage Pids
	 *
	 * @var array
	 */
	protected $storagePids = [];

	/**
	 * Resource Factory
	 * 
	 * @var \TYPO3\CMS\Core\Resource\ResourceFactory
	 * @inject
	 */
	protected $resourceFactory;

	/**
	 * Standalone View
	 * 
	 * @var \MageDeveloper\Dataviewer\Fluid\View\StandaloneView
	 * @inject
	 */
	protected $standaloneView;

	/**
	 * Record Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\RecordRepository
	 * @inject
	 */
	protected $recordRepository;

	/**
	 * Datatype Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\DatatypeRepository
	 * @inject
	 */
	protected $datatypeRepository;

	/**
	 * Form Settings Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Settings\Plugin\FormSettingsService
	 * @inject
	 */
	protected $formSettingsService;

	/**
	 * Record Data Handler
	 * 
	 * @var \MageDeveloper\Dataviewer\DataHandling\DataHandler\Record
	 * @inject
	 */
	protected $recordDataHandler;

	/**
	 * Gets a new record instance
	 * 
	 * @return \MageDeveloper\Dataviewer\Domain\Model\Record
	 */
	protected function _getNewRecord()
	{
		$datatype = $this->_getSelectedDatatype();
		
		/* @var Record $record */
		$record = $this->objectManager->get(Record::class);
		$record->setDatatype($datatype);
		
		return $record;
	}

	/**
	 * Action for displaying the form
	 * -
	 * This action is mainly for the output of the form.
	 * The form template is chosen in the backend plugin
	 * to add a customized form to this plugin
	 * 
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Record $record
	 * @return void
	 */
	public function indexAction(\MageDeveloper\Dataviewer\Domain\Model\Record $record = null)
	{
		$templateFile = $this->formSettingsService->getTemplateOverride();
		$template = GeneralUtility::getFileAbsFileName($templateFile);
		
		if(!$record instanceof Record)
			$record = $this->_getNewRecord();
		
		$this->view->assign("datatype", $record->getDatatype());
		$this->view->assign("template", $template);
		$this->view->assign("record", $record);
	}

	/**
	 * Action for form posts
	 * -
	 * The posted data is validated and then stored
	 * as a normal record is created.
	 * 
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Record $record
	 * @return void
	 */
	public function postAction(\MageDeveloper\Dataviewer\Domain\Model\Record $record = null)
	{
		// Initialize language
		\TYPO3\CMS\Frontend\Utility\EidUtility::initLanguage();
	
		$redirectPid = null;
		if(!$record instanceof Record)
		{
			// We need to check here, if the 'new'-Action is allowed, so we can create a new record
			if(!$this->formSettingsService->isAllowedAction(FormSettingsService::ACTION_NEW))
				$this->_handleRestrictedAction(FormSettingsService::ACTION_NEW, $record);
			
			$record = $this->_getNewRecord();
			$redirectPid = $this->formSettingsService->getRedirectAfterSuccessfulCreation();
		}
		else
		{
			// We check here, if the 'edit'-Action is allowed
			if(!$this->formSettingsService->isAllowedAction(FormSettingsService::ACTION_EDIT))
				$this->_handleRestrictedAction(FormSettingsService::ACTION_EDIT, $record);

			$redirectPid = $this->formSettingsService->getRedirectAfterSuccessfulEditing();
		}

		// We perform the normal post action for the new or existing record
		$post = $_POST;
		$fieldArray = $this->traverseFieldArray($post);

		/////////////////////////
		// Handle File Uploads //
		/////////////////////////
		$fileInfoArray = $this->handleFileUploads();
		$fileInfoArrayTraversed = $this->traverseFieldArray($fileInfoArray);

		// Merge FieldArray with the File Upload Information
		$fieldArray = \TYPO3\CMS\Extbase\Utility\ArrayUtility::arrayMergeRecursiveOverrule($fieldArray, $fileInfoArrayTraversed);

		/////////////////////////////////////////
		// Signal-Slot 'postPrepareFieldArray' //
		/////////////////////////////////////////
		$this->signalSlotDispatcher->dispatch(__CLASS__,"postPrepareFieldArray",[&$fieldArray, &$this]);
		
		$datatype = $this->_getSelectedDatatype();
		$validationErrors = $this->recordDataHandler->validateFieldArray($fieldArray, $datatype);
		
		/////////////////////////////////////////////////
		// Signal-Slot 'postAfterFieldArrayValidation' //
		/////////////////////////////////////////////////
		$this->signalSlotDispatcher->dispatch(__CLASS__,"postAfterFieldArrayValidation",[&$fieldArray, &$validationErrors, &$this]);
		
		if(!empty($validationErrors))
		{
			foreach($validationErrors as $_title=>$_errors)
				foreach($_errors as $_error)
					$this->addFlashMessage($_error->getMessage(), $_title, AbstractMessage::ERROR);
		
			$this->forward("index", null, null, ["record" => $record]);
		}
		
		////////////////////////////////////
		// Signal-Slot 'preProcessRecord' //
		////////////////////////////////////
		$this->signalSlotDispatcher->dispatch(__CLASS__,"preProcessRecord",[&$fieldArray, &$record, &$this]);

		// We need to save the record to obtain a uid
		if($record->getUid())
			$this->recordRepository->update($record);
		else
			$this->recordRepository->add($record);

		$this->persistenceManager->persistAll();

		$result = $this->recordDataHandler->processRecord($fieldArray, $record);
		
		if($result === true)
		{
			$message = Locale::translate("record_was_successfully_saved", array($record->getTitle(), $record->getUid()));
			$this->addFlashMessage($message, null, AbstractMessage::OK);
		}
		else
		{
			$message = Locale::translate("record_not_saved");
			$this->addFlashMessage($message, null, AbstractMessage::ERROR);
		}
		
		////////////////////////////////////
		// Signal-Slot 'postProcessRecord' //
		////////////////////////////////////
		$this->signalSlotDispatcher->dispatch(__CLASS__,"postProcessRecord", [&$record, &$this]);
		
		$this->persistenceManager->persistAll();

		$actionName = "index";
		$controllerName = (is_null($redirectPid))?null:"Record";
		$extensionName = null;
		$arguments = ["record" => $record];
		
		/////////////////////////////////////
		// Signal-Slot 'postFinalRedirect' //
		/////////////////////////////////////
		$this->signalSlotDispatcher->dispatch(__CLASS__,"postFinalRedirect",[	  &$actionName, 
																				  &$controllerName,
																				  &$extensionName,
																				  &$arguments, 
																				  &$redirectPid,
																				  &$this]);
		
		// Validation was passed, final redirect now
		$this->redirect($actionName, $controllerName, $extensionName, $arguments, $redirectPid);
		exit();
	}

	/**
	 * Action for displaying the form
	 * -
	 * This action is mainly for the output of the form.
	 * The form template is chosen in the backend plugin
	 * to add a customized form to this plugin
	 *
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Record $record
	 * @return void
	 */
	public function deleteAction(\MageDeveloper\Dataviewer\Domain\Model\Record $record = null)
	{
		$redirectPid = $this->formSettingsService->getRedirectAfterSuccessfulDeletion();
	
		if(!$record instanceof Record) 
		{
			$message = Locale::translate("could_not_delete_record", "?");
			$this->addBackendFlashMessage($message, '', FlashMessage::OK);
			$this->redirect("error");
		}

		// We check here, if the 'edit'-Action is allowed
		if(!$this->formSettingsService->isAllowedAction(FormSettingsService::ACTION_DELETE))
			$this->_handleRestrictedAction(FormSettingsService::ACTION_DELETE, $record);

		if ($record->getRecordValues() && $record->getRecordValues()->count())
		{
			// Remove each record value
			/* @var RecordValue $_recordValue */
			foreach ( $record->getRecordValues() as $_recordValue )
				$_recordValue->setDeleted(true);

		}

		$record->setDeleted(true);
		$this->recordRepository->update($record);

		// Process changes to the database
		$this->persistenceManager->persistAll();

		$message = Locale::translate("record_was_successfully_deleted", array($record->getUid()));
		$this->addFlashMessage($message, null, AbstractMessage::ERROR);

		$actionName = "index";
		$controllerName = (is_null($redirectPid))?null:"Record";
		$extensionName = null;
		$arguments = ["record" => $record];
		
		/////////////////////////////////////
		// Signal-Slot 'deleteFinalRedirect' //
		/////////////////////////////////////
		$this->signalSlotDispatcher->dispatch(__CLASS__,"deleteFinalRedirect",[	  	&$actionName,
																					&$controllerName,
																					&$extensionName,
																					&$arguments,
																					&$redirectPid,
																					&$this]);

		// Validation was passed, final redirect now
		$this->redirect($actionName, $controllerName, $extensionName, $arguments, $redirectPid);
	}
	
	/**
	 * Handles a restriction action
	 * 
	 * @param string $restrictedAction
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Record $record
	 * @return void
	 */
	protected function _handleRestrictedAction($restrictedAction, \MageDeveloper\Dataviewer\Domain\Model\Record $record = null)
	{
		$message = null;
		switch($restrictedAction)
		{
			case FormSettingsService::ACTION_NEW:
				$message = Locale::translate("message.creating_not_allowed");
				break;
			case FormSettingsService::ACTION_DELETE:
				$message = Locale::translate("message.deleting_not_allowed");
				break;
			case FormSettingsService::ACTION_EDIT:
			default:
				$message = Locale::translate("message.editing_not_allowed");
				break;
		}

		$this->addFlashMessage($message, null, AbstractMessage::ERROR);
		$this->redirect("Error");
	}

	/**
	 * Error Action to display errors
	 * without any other stuff
	 * 
	 * @return void
	 */
	public function errorAction()
	{
		
	}

	/**
	 * Traverses a given fieldarray and combines the values with
	 * the correct field ids
	 * 
	 * @return array
	 */
	public function traverseFieldArray(array $fieldArray = array())
	{
		$datatype = $this->_getSelectedDatatype();
		
		if($datatype)
		{
			foreach($fieldArray as $_fieldVar=>$_value)
			{
				/* @var \MageDeveloper\Dataviewer\Domain\Model\Field $_field */
				foreach($datatype->getFields() as $_field)
				{
					if($_field->getCode() == $_fieldVar)
					{
						$fieldArray[$_field->getUid()] = $_value;
						unset($fieldArray[$_fieldVar]);
					}	
				}
			}
		}
		
		return $fieldArray;
	}

	/**
	 * Handles file uploads
	 * and returns the finalized array
	 * 
	 * @return array
	 */
	public function handleFileUploads()
	{
		$fileUploadPath = $this->formSettingsService->getFileUploadFolder();
		$defaultStorage	= $this->resourceFactory->getDefaultStorage();
		$fileInfoArray = [];

		if($defaultStorage->hasFolder($fileUploadPath))
			$folder = $defaultStorage->getFolder($fileUploadPath);
		else
			$folder = $defaultStorage->createFolder($fileUploadPath);
		
		if($folder instanceof \TYPO3\CMS\Core\Resource\Folder)
		{
			$files = $_FILES;
			
			foreach($files as $_key=>$_fileInfo)
			{
				// This is the current behaviour, we still need to investigate this
				if(strlen($_fileInfo["name"]) <= 0) continue;
				
				$duplicationBehaviour = \TYPO3\CMS\Core\Resource\DuplicationBehavior::RENAME;
			
				// The destination folder exists, so we create the uploaded file here
				$file = $defaultStorage->addUploadedFile($_fileInfo, $folder, null, $duplicationBehaviour);
				$fileInfoArray[$_key] = $file->getPublicUrl();
			}
			
		}
		
		return $fileInfoArray;
	}

	/**
	 * Gets the according datatype model instance
	 * 
	 * @return \MageDeveloper\Dataviewer\Domain\Model\Datatype|null
	 */
	protected function _getSelectedDatatype()
	{
		$datatypeId = $this->formSettingsService->getSelectedDatatypeIds();
		$datatype = $this->datatypeRepository->findByUid($datatypeId, true);
		
		if($datatype instanceof \MageDeveloper\Dataviewer\Domain\Model\Datatype)
			return $datatype;
		
		return null;
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
		$cObj = $this->configurationManager->getContentObject();
		if ($cObj instanceof \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer)
			$this->view->assign("cObj", $cObj->data);

		// Allowed Storage Pids
		$ids = $this->formSettingsService->getSelectedVariableIds();
		$variables = $this->prepareVariables($ids);
		$this->view->assignMultiple($variables);

		// Parent
		parent::initializeView($view);
	}
}
