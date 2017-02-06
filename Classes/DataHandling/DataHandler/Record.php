<?php
namespace MageDeveloper\Dataviewer\DataHandling\DataHandler;

use MageDeveloper\Dataviewer\Utility\LocalizationUtility as Locale;
use MageDeveloper\Dataviewer\Domain\Model\Datatype as DatatypeModel;
use MageDeveloper\Dataviewer\Domain\Model\Record as RecordModel;
use MageDeveloper\Dataviewer\Domain\Model\RecordValue as RecordValueModel;
use MageDeveloper\Dataviewer\Domain\Model\Field as FieldModel;
use MageDeveloper\Dataviewer\Domain\Model\FieldValue as FieldValueModel;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Html\RteHtmlParser;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * MageDeveloper Dataviewer Extension
 * -----------------------------------
 *
 * @category    TYPO3 Extension
 * @package     MageDeveloper\Dataviewer
 * @author		Bastian Zagar
 * @copyright   Magento Developers / magedeveloper.de <kontakt@magedeveloper.de>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL .0)
 */
class Record extends AbstractDataHandler implements DataHandlerInterface
{
	/**
	 * RecordValue Session Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Session\RecordValueSessionService
	 * @inject
	 */
	protected $recordValueSessionService;

	/**
	 * Field Validation
	 *
	 * @var \MageDeveloper\Dataviewer\Validation\FieldValidation
	 * @inject
	 */
	protected $fieldValidation;

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
	 * Record Value Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\RecordValueRepository
	 * @inject
	 */
	protected $recordValueRepository;

	/**
	 * Field Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\FieldRepository
	 * @inject
	 */
	protected $fieldRepository;

	/**
	 * Constructor
	 *
	 * @return Record
	 */
	public function __construct()
	{
		parent::__construct();
		$this->fieldValueSessionService = $this->objectManager->get(\MageDeveloper\Dataviewer\Service\Session\FieldValueSessionService::class);
		$this->recordRepository			= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\RecordRepository::class);
		$this->recordValueRepository	= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\RecordValueRepository::class);
		$this->fieldRepository			= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\FieldRepository::class);
		$this->fieldValidation			= $this->objectManager->get(\MageDeveloper\Dataviewer\Validation\FieldValidation::class);

		/*
		$backend = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\BackendInterface::class);
		//$dataMapRecord = $backend->getDataMapper()->getDataMap("MageDeveloper\\Dataviewer\\Domain\\Model\\Record");
		//$dataMapRecord->setTableName("tx_dataviewer_domain_model_record_external");
		$dataMapRecordValue = $backend->getDataMapper()->getDataMap("MageDeveloper\\Dataviewer\\Domain\\Model\\RecordValue");
		$dataMapRecordValue->setTableName("tx_dataviewer_domain_model_recordvalue_external");
		*/
	}

	/**
	 * Get an record by a given id
	 *
	 * @param int $id
	 * @return RecordModel|bool
	 */
	public function getRecordById($id)
	{
		if($id <= 0) return false;

		/* @var RecordModel $record */
		$record = $this->recordRepository->findByUid($id, false);

		if ($record instanceof RecordModel)
			return $record;

		return false;
	}

	/**
	 * Get an datatype by a given id
	 *
	 * @param int $id
	 * @return DatatypeModel|bool
	 */
	public function getDatatypeById($id)
	{
		if($id <= 0) return false;

		/* @var DatatypeModel $record */
		$datatype = $this->datatypeRepository->findByUid($id, false);

		if ($datatype instanceof DatatypeModel)
			return $datatype;

		return false;
	}

	/**
	 * Get an recordValue by a given id
	 *
	 * @param int $id
	 * @return RecordValueModel|bool
	 */
	public function getRecordValueById($id)
	{
		/* @var RecordValueModel $record */
		$recordValue = $this->recordValueRepository->findByUid($id, false);

		if ($recordValue instanceof RecordValueModel && $recordValue->getUid() == $id)
			return $recordValue;

		return false;
	}

	/**
	 * Get an field by a given id
	 *
	 * @param int $id
	 * @return Field|bool
	 */
	public function getFieldById($id)
	{
		/* @var FieldModel $field */
		$field = $this->fieldRepository->findByUid($id, false);

		if ($field instanceof FieldModel && $field->getUid() == $id)
			return $field;

		return false;
	}

	/**
	 * processCmdmap
	 *
	 * @param string $command
	 * @param string $table
	 * @param mixed $value
	 * @param bool $commandIsProcessed
	 * @param \TYPO3\CMS\Core\DataHandling\DataHandler $parentObj
	 * @param bool $pasteUpdate
	 * @return void
	 */
	public function processCmdmap($command, $table, $id, $value, &$commandIsProcessed, $parentObj, $pasteUpdate)
	{
		if ($table != "tx_dataviewer_domain_model_record") return;

		if ($command == "copy")
		{
			/* @var RecordModel $parentRecord */
			/* @var RecordModel $newRecord */
			$parentRecord = $this->getRecordById($id);
			$newRecordId = $parentObj->copyRecord($table, $id, $value, false, [], "record_values");
			$newRecord = $this->getRecordById($newRecordId);

			// Original Record Values that need to be copied
			$recordValues = $parentRecord->getRecordValues();

			foreach($recordValues as $_recordValue)
			{
				/* @var RecordValue $_recordValue */
				/* @var RecordValue $newRecordValue */
				$newRecordValueId = $parentObj->copyRecord("tx_dataviewer_domain_model_recordvalue", $_recordValue->getUid(), $value, false, [], "field,field_value");
				$newRecordValue = $this->getRecordValueById($newRecordValueId, false);

				if ($newRecordValue)
				{
					$newRecordValue->setRecord($newRecord);
					$newRecordValue->setField($_recordValue->getField());
					$this->recordValueRepository->update($newRecordValue);
				}

			}

			$this->persistenceManager->persistAll();

			// We disable the normal processing of the command
			$commandIsProcessed = true;
		}

	}

	/**
	 * @param string $table
	 * @param int $id
	 * @param array $recordToDelete
	 * @param bool $recordWasDeleted
	 * @param \TYPO3\CMS\Core\DataHandling\DataHandler $parentObj
	 */
	public function processCmdmap_deleteAction($table, $id, $recordToDelete, &$recordWasDeleted, &$parentObj)
	{
		if ($table != "tx_dataviewer_domain_model_record") return;

		/* @var Record $record */
		$record = $this->getRecordById($id);

		if (!$record instanceof RecordModel)
		{
			$message = Locale::translate("could_not_delete_record", $id);
			$this->addBackendFlashMessage($message, '', FlashMessage::OK);
			return;
		}

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

		// Hook
		$recordWasDeleted = true;

		$message = Locale::translate("record_was_successfully_deleted", $id);
		$this->addBackendFlashMessage($message, '', FlashMessage::OK);
	}

	/**
	 * Prevent saving of a news record if the editor doesn't have access to all categories of the news record
	 *
	 * @param array $incomingFieldArray
	 * @param string $table
	 * @param int $id
	 * @param \TYPO3\CMS\Core\DataHandling\DataHandler $parentObj
	 * @return bool
	 */
	public function processDatamap_preProcessFieldArray(&$incomingFieldArray, $table, $id, &$parentObj)
	{
		if ($table != "tx_dataviewer_domain_model_record") return;
		
		// Storing the fieldArray to the session to prefill form values for easier modifying
		$this->recordValueSessionService->store($id, $incomingFieldArray);
		
		$record = $this->getRecordById($id);
		
		$datatype = null;
		if ($record)
			$datatype = $record->getDatatype();

		if(!$datatype)
			$datatype = $this->getDatatypeById($incomingFieldArray["datatype"]);

        if(!$datatype)
            return;

		$datatypeId = $datatype->getUid();
		if(GeneralUtility::_POST()["datatype"]) {
			// This is for the single datatype selection on creating a new
			// record in the New-Record-Assistant of TYPO3
			$datatypeId = (int)GeneralUtility::_POST("datatype");
			$this->_redirectCurrentUrl(["datatype" => $datatypeId]);
		}
		
		$validationErrors = [];
	
		// Validate the POST data
		$validationErrors = $this->validateFieldArray($incomingFieldArray, $datatype);

		if (!empty($validationErrors))
		{
			// Record save data is invalid. We showed the messages before, now we need to reload the
			// form with the entered data
			foreach($validationErrors as $field=>$_errors)
				foreach($_errors as $_error)
					$this->addBackendFlashMessage($_error, $field);

			$incomingFieldArray["icon"] = $datatype->getIcon();
			$incomingFieldArray["hidden"] = true;

			// Storing the fieldArray to the session to prefill form values for easier modifying
			//$this->recordValueSessionService->store("NEW", $incomingFieldArray);
			$this->_redirectCurrentUrl(["datatype" => $datatypeId]);
			return;
		}

		// Records save data is stored for later usage to
		// correctly store NEW<hash>-Records
		$this->saveData[$id] = [
			$incomingFieldArray,
		];

		// We need to remove all elements from the array where the key is an integer,
		// so we can remove our custom fields in order to let the save procedure
		// in combination with the added GLOBALS (for the suggest wizard) removed
		foreach($incomingFieldArray as $_k=>$_v)
			if(is_numeric($_k))
				unset($incomingFieldArray[$_k]);
	}

	/**
	 * @param string $status
	 * @param string $table
	 * @param int $id
	 * @param array $fieldArray
	 * @param \TYPO3\CMS\Core\DataHandling\DataHandler $parentObj
	 */
	public function processDatamap_afterDatabaseOperations($status, $table, $id, $fieldArray, &$parentObj)
	{
		// Assign substNEWIds for later usage if the element is in our target
		$this->substNEWwithIDs = array_merge($this->substNEWwithIDs, $parentObj->substNEWwithIDs);
		$this->_substituteRecordValues();

		if ($table != "tx_dataviewer_domain_model_record") return;

		// Disable Versioning
		//$parentObj->bypassWorkspaceRestrictions = true;
		//$parentObj->updateModeL10NdiffDataClear = true;
		//$parentObj->updateModeL10NdiffData = false;

		// Substitute the NEW to Id on all record values and maybe in this array
		// We need to transform the saved values (NEW<hash>) to already saved ids)
		$this->_substituteRecordValues();

		// Retrieve clean id
		$recordId = $this->_getPossibleSubstitutedId($id);
		$record   = $this->getRecordById($recordId);

		if(!$record instanceof RecordModel)
		{
			$message  = Locale::translate("record_not_found", $id);
			$this->addBackendFlashMessage($message);
			return;
		}

		if (isset($this->saveData[$id]) && is_array($this->saveData[$id]))
		{
			$recordSaveData = reset($this->saveData[$id]);
			
			$result 		= $this->processRecord($recordSaveData, $record);

			$message  = Locale::translate("record_not_saved");
			$severity = FlashMessage::ERROR;
			if ($result)
			{
				// Save processed data
				$this->persistenceManager->persistAll();

				$message  = Locale::translate("record_was_successfully_saved", [$record->getTitle(), $recordId]);
				$severity = FlashMessage::OK;

				// We clear the according session data
				$this->recordValueSessionService->resetForRecordId($id);
			}
			else
			{
				if($record)
				{
					$record->setHidden(true);
					$this->recordRepository->update($record);
					$this->persistenceManager->persistAll();
				}
			}

			// We only deliver a message, when 
			if(!$record->getDatatype()->getHideRecords() || $severity != FlashMessage::OK)
				$this->addBackendFlashMessage($message, '', $severity);

			return;
		}

	}
	
	/**
	 * Validates an field array that came with the
	 * form post on the record editing
	 *
	 * @param array $fieldArray
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Datatype $datatype
	 * @return array
	 */
	public function validateFieldArray(array $fieldArray, \MageDeveloper\Dataviewer\Domain\Model\Datatype $datatype)
	{
		// We need to check if all contents of the fieldArray are fields from the TCA
		// If not, we need to validate the other fields
		// We check the tca configuration, and retrieve all fields for the table that are general
		if(isset($GLOBALS["TCA"]["tx_dataviewer_domain_model_record"]["columns"]))
		{
			$validColumns = $GLOBALS["TCA"]["tx_dataviewer_domain_model_record"]["columns"];
			$diff = array_diff_key($fieldArray, $validColumns);
		
			if(empty($diff))
				return [];
		}
	
		$fieldValidationErrors = [];

		foreach($datatype->getFields() as $_field)
		{
			/* @var \MageDeveloper\Dataviewer\Domain\Model\Field $_field */
			$this->fieldValidation->setField($_field);

			$value = null;
			if(isset($fieldArray[$_field->getUid()]))
				$value = $fieldArray[$_field->getUid()];

			$fieldValueValidationErrors = $this->fieldValidation->validate($value);

			if(!empty($fieldValueValidationErrors))
			{
				foreach($fieldValueValidationErrors as $_error)
					$fieldValidationErrors[$_field->getFrontendLabel()][] = $_error;
			}
		}

		return $fieldValidationErrors;
	}
	
	

	/**
	 * Transforms the NEW-ID into the
	 * correct ID if found in Substitute Id Array
	 *
	 * @param string|int $id
	 * @return string|int
	 */
	protected function _getPossibleSubstitutedId($id)
	{
		if(isset($this->substNEWwithIDs[$id]))
			return $this->substNEWwithIDs[$id];

		return $id;
	}

	/**
	 * Substitutes all record values with the now known ids
	 *
	 * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
	 * @return void
	 */
	protected function _substituteRecordValues()
	{
		if(!empty($this->substNEWwithIDs))
		{
			// This is dumb but we just replace the NEWhash.hash which is stored already in
			// the database into the uid that shows up here
			foreach($this->substNEWwithIDs as $_subNew=>$_subId)
			{
				$recordValues = $this->recordValueRepository->findByValueContent($_subNew);

				foreach($recordValues as $_recordValue)
				{
					$valueContent = $_recordValue->getValueContent();
					$newValueContent = str_replace($_subNew, $_subId, $valueContent);

					$_recordValue->setValueContent($newValueContent);
					$this->recordValueRepository->update($_recordValue);
				}

			}

			$this->persistenceManager->persistAll();
		}
	}

	/**
	 * Processes record information
	 *
	 * @param array $recordSaveData
	 * @param RecordModel $record
	 * @return bool
	 */
	public function processRecord(array $recordSaveData, RecordModel $record)
	{
		// Get datatype
		$datatype = $record->getDatatype();

		if(!$datatype)
		{
			// We try loading the datatype by the recordSaveData Information
			if(isset($recordSaveData["datatype"]))
				$datatype = $this->datatypeRepository->findByUid((int)$recordSaveData["datatype"], false);
		
		}
		
		if(!$datatype)
			return false;

		// Refresh record timestamp
		$record->setTstamp(time());

		// Add icon
		$record->setIcon($datatype->getIcon());
		
		//////////////////////
		// RECORD SAVE DATA //
		//////////////////////
		if(!is_array($recordSaveData))
			return false;

		$this->_processRecordSaveData($record, $recordSaveData);

		return true;
	}

	/**
	 * Processes record elements
	 *
	 * @param RecordModel $record
	 * @param array $recordSaveData
	 * @return void
	 */
	protected function _processRecordSaveData(RecordModel $record, array $recordSaveData = [])
	{
		$datatype = $record->getDatatype();

		if($record->hasTitleField())
			$record->setTitle("");
	
		if(isset($recordSaveData["title"]))
			$record->setTitle($recordSaveData["title"]);

		if(isset($recordSaveData["hidden"]))
			$record->setHidden((bool)$recordSaveData["hidden"]);

		///////////////////////////////////////////
		// process all uploads
		///////////////////////////////////////////
		$this->_processUploads($_FILES);

		///////////////////////////////////////////
		// We go through all fields of the datatype
		///////////////////////////////////////////
		$overallResult = null;

		foreach($recordSaveData as $_fieldId=>$_value)
		{
			$originalValue = $_value;
			//if($record->_hasProperty($_fieldId))
			//	$record->_setProperty($_fieldId, $_value);

			/* @var FieldModel $field */
			$field = $datatype->getFieldById($_fieldId);

			if (!$field instanceof FieldModel)
				continue;

			// Process Array (formerly Flexform Element) but don't process checkbox values
			if(is_array($_value))
			{
				// Panic substitute :)
				$_value = $this->_substituteArrayNEWwithIds($_value);
				$_value = $this->dataHandler->getFlexformValue($_value, $record, $field);
				$_value = $this->flexTools->flexArray2Xml($_value);
			}
			
			// We need to check the field
			// We get the tca from the fieldtype class
			// We check agains checkValue_SW in the dataHandler
			/* @var \MageDeveloper\Dataviewer\Domain\Model\Fieldtype $fieldtype */
			$fieldtypeConfiguration = $this->fieldtypeSettingsService->getFieldtypeConfiguration($field->getType());
			$class = $fieldtypeConfiguration->getFieldClass();

			if($this->objectManager->isRegistered($class))
			{
				$this->dataHandler->BE_USER		= $GLOBALS["BE_USER"];

				/* @var \MageDeveloper\Dataviewer\Form\Fieldtype\FieldtypeInterface $fieldtypeModel */
				$fieldtypeModel = $this->objectManager->get($class);

				$fieldtypeModel->setField($field);
				$fieldtypeModel->setRecord($record);

				$tca = $fieldtypeModel->getFieldTca();
				
				$res = [];
				$uploadedFiles = [];

				if(isset($this->dataHandler->uploadedFileArray["tx_dataviewer_domain_model_record"][$record->getUid()][$field->getUid()]))
					$uploadedFiles = $this->dataHandler->uploadedFileArray["tx_dataviewer_domain_model_record"][$record->getUid()][$field->getUid()];

				$val = $this->dataHandler->checkValue_SW(	
					$res,
					$_value,
					$tca,
					"tx_dataviewer_domain_model_record",
					$record->getUid(),
					$_value,
					"new",
					$record->getPid(),
					"[tx_dataviewer_domain_model_record:{$record->getUid()}:{$field->getUid()}]",
					$field->getUid(),
					$uploadedFiles,
					$record->getPid(),
					[]
				);

				// Select and Inline Value
				if(array_key_exists("value", $val) && $tca["type"] !== "select" && $tca["type"] !== "inline")
					$_value = $val["value"];

				if ($tca["type"] == "inline")
				{
					// We divorce the values and set them back together
					$values = GeneralUtility::trimExplode(",", $_value, true);
					$_value = [];
					foreach($values as $v)
						if ($v) $_value[] = $v;

					$_value = implode(",", $_value);
				}
				
				// Conversion of fieldvalue is wrong (dbType date/datetime is not good enough for us)
				if( ($field->getType() == "date" || $field->getType() == "datetime") )
					$_value = $originalValue;
				
				if($field->getType() == "rte")
				{
					// nl2br
					$_value = nl2br($_value);
					
					/* @var \MageDeveloper\Dataviewer\Form\Fieldtype\Rte $fieldtypeModel */
					$defaultExtras = $fieldtypeModel->getDefaultExtras();
					$specConf = BackendUtility::getSpecConfParts($defaultExtras);
					
					// Initialize transformation:
					/* @var RteHtmlParser $parseHTML */
					$parseHTML = GeneralUtility::makeInstance(RteHtmlParser::class);
					$parseHTML->init("tt_content" . ':' . "bodytext", $record->getPid()); // We imitate a tt_content bodytext field
					$parseHTML->setRelPath('');
					// Perform transformation:
					$_value = $parseHTML->RTE_transform($_value, $specConf, 'db', []);
				}
			}
			
			$result = $this->_saveRecordValue($record, $field, $_value);

			if (!$result)
				$overallResult = false;

		}
		
		if ($overallResult === false)
		{
			// We hide the record until it is ok
			$record->setHidden(true);

			// Persist valid fields
			//$this->persistenceManager->persistAll();

			// Redirect back to form
			$this->_redirectRecord($record->getUid());
		}

	}

	/**
	 * Saves record field value content
	 *
	 * @param RecordModel $record
	 * @param FieldModel $field
	 * @param mixed $value
	 * @return int
	 */
	protected function _saveRecordValue(RecordModel $record, FieldModel $field, $value)
	{
		$pid   = $record->getPid();

		/* @var RecordValueModel $recordValue */
		$recordValue = $this->recordValueRepository->findOneByRecordAndField($record, $field);

		if(!$recordValue instanceof RecordValueModel)
			$recordValue = $this->objectManager->get(RecordValueModel::class);

		$recordValue->setRecord($record);
		$recordValue->setField($field);
		$recordValue->setPid($pid);

		// Defaults
		$valueContent = $value;
		$search = $value;

		//////////////////////////////////////////////////////////////////////////////////
		// Specific Save Part
		// -------------------------------------------------------------------------------
		// The data is finalized by the according fieldValue 
		// It is splitted up in two parts:
		//
		// Value Content for the main database entry that is performed 
		// withing the SingleFieldContainer
		//
		// Search for the database search entry that will be used
		// in all search, sorting and filtering plugins
		//////////////////////////////////////////////////////////////////////////////////
		$fieldtypeConfiguration = $this->fieldtypeSettingsService->getFieldtypeConfiguration( $field->getType() );
		$valueClass = $fieldtypeConfiguration->getValueClass();

		if (!$this->objectManager->isRegistered($valueClass))
			$valueClass = \MageDeveloper\Dataviewer\Form\Fieldvalue\General::class;

		/* @var \MageDeveloper\Dataviewer\Form\Fieldvalue\FieldvalueInterface $fieldValue */
		$fieldvalue = $this->objectManager->get($valueClass);

		if($fieldvalue instanceof \MageDeveloper\Dataviewer\Form\Fieldvalue\FieldvalueInterface)
		{
			// We need to initialize the fieldvalue with the plain value
			$fieldvalue->init($field, $value);
			$fieldvalue->setRecordValue($recordValue);

			////////////////////////////////////////////////////////////////////////////////////////////////
			// This is the place where we will later pre-render the value through each fieldValue
			// so we can retrieve a TYPO3-valid save value for the database
			// TODO: render value through each formvalue
			////////////////////////////////////////////////////////////////////////////////////////////////

			// We retrieve our needed data from the fieldvalue
			$valueContent 	= $fieldvalue->getValueContent();
			$search 		= $fieldvalue->getSearch();
		}
		
		// Assign value to the recordValue
		$recordValue->setValueContent($valueContent);
		// Assign clean search string to the recordValue
		$recordValue->setSearch($search);

		// Add or update
		if($recordValue->getUid() > 0)
		{
			// Update
			$this->recordValueRepository->update($recordValue);
		}
		else
		{
			// Add
			$this->recordValueRepository->add($recordValue);
			$record->addRecordValue($recordValue);
		}

		// FieldType Text can overwrite the record title, so it can be inactive
		if ($field->getIsRecordTitle() && (strlen($value) < 250))
			$record->appendTitle($value);

		return true;
	}

	/**
	 * Builds default record contents from inline contents
	 * Our inline contents contain a 'value'-Key, that is unneeded but had
	 * to be in out Inline-Form-Field-Configuration. To bad :(
	 *
	 * @param array $inlineContents
	 * @return array
	 */
	protected function _buildRecordContentsFromInlineContents(array $inlineContents = [])
	{
		$recordContents = [];

		$parent = null;
		foreach($inlineContents as $_key=>$_value)
		{
			if ($_key == "value")
			{
				$recordContents = $inlineContents["value"];
			}
			else
			{
				if (is_array($_value))
					$recordContents[$_key] = $this->_buildRecordContentsFromInlineContents($_value);
				else
					$recordContents[$_key] = $_value;
			}

		}

		return $recordContents;
	}
}
