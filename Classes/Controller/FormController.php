<?php
namespace MageDeveloper\Dataviewer\Controller;

use MageDeveloper\Dataviewer\Domain\Model\Field;
use MageDeveloper\Dataviewer\Domain\Model\Record;
use MageDeveloper\Dataviewer\Domain\Model\Variable;
use MageDeveloper\Dataviewer\Service\Settings\Plugin\ListSettingsService;
use MageDeveloper\Dataviewer\Service\Settings\Plugin\SearchSettingsService;
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
	protected $storagePids = array();

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
		$fieldArray = $this->traversePost();
		if(!$record instanceof Record)
			$record = $this->_getNewRecord();
				
		/////////////////////////////////////////
		// Signal-Slot 'postPrepareFieldArray' //
		/////////////////////////////////////////
		$this->signalSlotDispatcher->dispatch(__CLASS__,"postPrepareFieldArray",array(&$fieldArray, &$this));
		
		$validationErrors = $this->recordDataHandler->validateFieldArray($fieldArray);
		
		/////////////////////////////////////////////////
		// Signal-Slot 'postAfterFieldArrayValidation' //
		/////////////////////////////////////////////////
		$this->signalSlotDispatcher->dispatch(__CLASS__,"postAfterFieldArrayValidation",array(&$fieldArray, &$validationErrors, &$this));
		
		if(!empty($validationErrors))
		{
			foreach($validationErrors as $_title=>$_errors)
				foreach($_errors as $_error)
					$this->addFlashMessage($_error->getMessage(), $_title, AbstractMessage::ERROR);
		
			$this->forward("index", null, null, array("record" => $record));
		}
		
		////////////////////////////////////
		// Signal-Slot 'preProcessRecord' //
		////////////////////////////////////
		$this->signalSlotDispatcher->dispatch(__CLASS__,"preProcessRecord",array(&$fieldArray, &$record, &$this));

		// We need to save the record to obtain a uid
		if($record->getUid())
			$this->recordRepository->update($record);
		else
			$this->recordRepository->add($record);

		$this->persistenceManager->persistAll();

		$result = $this->recordDataHandler->processRecord($fieldArray, $record);
		
		////////////////////////////////////
		// Signal-Slot 'postProcessRecord' //
		////////////////////////////////////
		$this->signalSlotDispatcher->dispatch(__CLASS__,"postProcessRecord",array(&$record, &$this));
		
		$this->persistenceManager->persistAll();
		
		
		$redirect = "index";
		$controllerName = null;
		$extensionName = null;
		$arguments = array("record" => $record);
		$pageUid = null;
		/////////////////////////////////////
		// Signal-Slot 'postFinalRedirect' //
		/////////////////////////////////////
		$this->signalSlotDispatcher->dispatch(__CLASS__,"postFinalRedirect",array(&$redirect, 
																				  &$controllerName,
																				  &$extensionName,
																				  &$arguments, 
																				  &$pageUid,
																				  &$this));
		
		// Validation was passed, final redirect now
		$this->redirect($redirect, $controllerName, $extensionName, $arguments, $pageUid);
		exit();
	}

	/**
	 * Traverses the post and combines the values with
	 * the correct field ids
	 * 
	 * @return array
	 */
	public function traversePost()
	{
		$post = $_POST;
		$datatype = $this->_getSelectedDatatype();
		
		if($datatype)
		{
			foreach($post as $_postVar=>$_value)
			{
				/* @var \MageDeveloper\Dataviewer\Domain\Model\Field $_field */
				foreach($datatype->getFields() as $_field)
				{
					if($_field->getCode() == $_postVar)
					{
						$post[$_field->getUid()] = $_value;
						unset($post[$_postVar]);
					}	
				}
			}
		}
		
		return $post;
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
