<?php
namespace MageDeveloper\Dataviewer\Factory;

use MageDeveloper\Dataviewer\Domain\Model\Record;
use MageDeveloper\Dataviewer\Domain\Model\Datatype;
use MageDeveloper\Dataviewer\Domain\Model\RecordValue;
use MageDeveloper\Dataviewer\Domain\Model\Field;
use MageDeveloper\Dataviewer\Utility\ArrayUtility;
use TYPO3\CMS\Core\Resource\DuplicationBehavior;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\File\BasicFileUtility;

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
class RecordFactory implements SingletonInterface
{
	/**
	 * Object Manager
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
	 * Signal/Slot Dispatcher
	 *
	 * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
	 * @inject
	 */
	protected $signalSlotDispatcher;

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
	 * Record DataHandler
	 * 
	 * @var \MageDeveloper\Dataviewer\DataHandling\DataHandler\Record
	 * @inject
	 */
	protected $recordDataHandler;

	/**
	 * Validation Errors
	 * 
	 * @var array
	 */
	protected $validationErrors = array();

	/**
	 * Gets the validation errors
	 * 
	 * @return array
	 */
	public function getValidationErrors()
	{
		return $this->validationErrors;
	}

	/**
	 * Creates a record with given values
	 * and returns the output
	 *
	 * @param array $fieldArray
	 * @param Datatype $datatype
	 * @param bool $traverse
	 * @param bool $forceCreation
	 * @return \MageDeveloper\Dataviewer\Domain\Model\Record
	 * @throws \MageDeveloper\Dataviewer\Exception\NoDatatypeException
	 */
	public function create(array $fieldArray, Datatype $datatype = null, $traverse = true, $forceCreation = false)
	{
		// The record data has to contain a datatype
		if(is_null($datatype) && !isset($fieldArray["datatype"]))
		{
			throw new \MageDeveloper\Dataviewer\Exception\NoDatatypeException(
				"Missing Datatype for Record Factory -> create", 1477051980
			);
		}

		// Initiate a new record model
		$record = $this->objectManager->get(Record::class);
		
		if(!$datatype instanceof Datatype)
			$datatype = $this->datatypeRepository->findByUid($fieldArray["datatype"], false);

		// Setting the datatype to the record
		$record->setDatatype($datatype);

		
		if(isset($fieldArray["pid"]) && is_numeric($fieldArray["pid"]))
		{
			$record->setPid($fieldArray["pid"]);
			unset($fieldArray["pid"]);		
		}
		else
		{
			// Fallback for pid of the datatype pid
			$record->setPid($datatype->getPid());
		}
		
		$this->recordDataHandler->setDontProcessTransformations(true);

		/////////////////////////////////////////////////
		// Signal-Slot 'createPreProcess'              //
		/////////////////////////////////////////////////
		$this->signalSlotDispatcher->dispatch(__CLASS__,"createPreProcess",[&$fieldArray, &$this]);

		// Traverse the data into the relevant fieldId=>value information
		if ($traverse)
			$traversedFieldArray = $this->traverseFieldArray($fieldArray, $datatype);
		else
			$traversedFieldArray = $fieldArray;

		// Check for validation errors
		$this->validationErrors = $this->recordDataHandler->validateFieldArray($traversedFieldArray, $datatype);

		// We hide the record on any error
		if(!empty($this->validationErrors))
			$record->setHidden(true);

		if(empty($this->validationErrors) || $forceCreation)
			$result = $this->recordDataHandler->processRecord($traversedFieldArray, $record);

		/////////////////////////////////////////////////
		// Signal-Slot 'createPostProcess'             //
		/////////////////////////////////////////////////
		$this->signalSlotDispatcher->dispatch(__CLASS__,"createPostProcess",[&$record, &$fieldArray, &$this]);
		
		return $record;
	}

	/**
	 * Updates a record with new given values
	 *
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Record $record
	 * @param array $updateFieldArray
	 * @param bool $traverse
	 * @return bool
	 * @throws \MageDeveloper\Dataviewer\Exception\NoDatatypeException
	 */
	public function update(Record $record, array $updateFieldArray, $traverse = true)
	{
		// The record data has to contain a datatype
		if(!$record->getDatatype() instanceof Datatype)
		{
			throw new \MageDeveloper\Dataviewer\Exception\NoDatatypeException(
				"Missing Datatype for Record Factory -> update", 1477057477
			);
		}

		$this->recordDataHandler->setDontProcessTransformations(true);

		/////////////////////////////////////////////////
		// Signal-Slot 'updatePreProcess'              //
		/////////////////////////////////////////////////
		$this->signalSlotDispatcher->dispatch(__CLASS__,"updatePreProcess",[&$updateFieldArray, &$this]);

		// Traverse the data into the relevant fieldId=>value information
		if ($traverse)
			$traversedFieldArray = $this->traverseFieldArray($updateFieldArray, $record->getDatatype());
		else
			$traversedFieldArray = $updateFieldArray;

		$recordValues = $record->getRecordValues();
		
		$originalRecordFieldArray = [];
		foreach($recordValues as $_recordValue) {
		    /* @var RecordValue $_recordValue */
		    if($_recordValue->getField() instanceof Field)
                $originalRecordFieldArray[$_recordValue->getField()->getUid()] = $_recordValue->getValueContent();
		}

		// Adding array values as an appended value
        foreach($traversedFieldArray as $_fieldId=>$_value)
        {
            if(is_array($_value) && is_array($originalRecordFieldArray) && array_key_exists($_fieldId, $originalRecordFieldArray))
            {
                $parts = GeneralUtility::trimExplode(",", $originalRecordFieldArray[$_fieldId]);
                $parts = array_merge($parts, $_value);
                $traversedFieldArray[$_fieldId] = implode(",", $parts);
            }
        }

		$fieldArray = array_replace($originalRecordFieldArray, $traversedFieldArray);

		// Check for validation errors
		$this->validationErrors = $this->recordDataHandler->validateFieldArray($fieldArray, $record->getDatatype());

		// We hide the record on any error
		if(!empty($this->validationErrors))
			$record->setHidden(true);

		$result = $this->recordDataHandler->processRecord($fieldArray, $record);

		/////////////////////////////////////////////////
		// Signal-Slot 'updatePostProcess'             //
		/////////////////////////////////////////////////
		$this->signalSlotDispatcher->dispatch(__CLASS__,"updatePostProcess",[&$record, &$fieldArray, &$this]);

		return $record;
	}
    

	/**
	 * Regenerates dynamic values from an existing record
	 * 
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Record $record
	 * @return bool
	 */
	public function regenerateDynamicValues(Record $record)
	{
		if($record->getUid() > 0)
		{
			$recordValues = $record->getRecordValues();

			if(count($recordValues) > 0) {
				$originalRecordFieldArray = [];

				foreach($recordValues as $_recordValue) {

					/* @var RecordValue $_recordValue */
					$originalRecordFieldArray[$_recordValue->getField()->getUid()] = $_recordValue->getValueContent();
				}

				try {
					$result = $this->recordDataHandler->processRecord($originalRecordFieldArray, $record);
					$this->recordRepository->update($record);
					$this->persistenceManager->persistAll();
				} catch (\Exception $e)	{ 
					return false;
				}
				
				return true;
			}

		}
		
		return false;
	}

	/**
	 * Uploads a file
	 *
	 * @param array $fileInfo
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Field $field
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Record $record
	 * @return array|int
	 */
	public function uploadFile(array $fileInfo, \MageDeveloper\Dataviewer\Domain\Model\Field $field, \MageDeveloper\Dataviewer\Domain\Model\Record $record)
	{
		$uploadFolder = $field->getConfig("uploadfolder");

		// Upload folder fallback
        if($uploadFolder == "") {
            $uploadFolder = "fileadmin/user_upload/";
        }

		$uploadFolder = str_replace("fileadmin/", "", $uploadFolder);

		/* @var \TYPO3\CMS\Core\Resource\ResourceFactory $resourceFactory */
		/* @var \TYPO3\CMS\Core\Resource\StorageRepository $defaultStorage */
		/* @var \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler */
		$resourceFactory = $this->objectManager->get(\TYPO3\CMS\Core\Resource\ResourceFactory::class);
		$defaultStorage = $resourceFactory->getDefaultStorage();
		$dataHandler = $this->objectManager->get(\TYPO3\CMS\Core\DataHandling\DataHandler::class);
		$dataHandler->fileFunc = GeneralUtility::makeInstance(BasicFileUtility::class);
		/* @var \TYPO3\CMS\Core\Resource\Folder $targetFolder */
		$targetFolder = $defaultStorage->getFolder($uploadFolder);
		$newFileName = $fileInfo["name"];

		/* @var \TYPO3\CMS\Core\Resource\File $file */
		$file = $targetFolder->addUploadedFile($fileInfo, DuplicationBehavior::RENAME);

		$newId = "NEW1234";
		$data = [];
		$data["sys_file_reference"][$newId] = array(
			"table_local" 	=> "sys_file",
			"uid_local" 	=> $file->getUid(),
			"tablenames" 	=> "tx_dataviewer_domain_model_record",
			"uid_foreign" 	=> $record->getUid(),
			"fieldname" 	=> $field->getUid(),
			"cruser_id"		=> 0,
			"pid" 			=> 0,
		);

		$data["tx_dataviewer_domain_model_record"][$record->getUid()] = [
			"tx_dataviewer_domain_model_record" => $newId,
		];

		$allowed = $field->getConfig("allowed");
		$disallowed = $field->getConfig("disallowed");

		$dataHandler->fileFunc->f_ext['webspace']['allow'] = $allowed;
		$dataHandler->fileFunc->f_ext['webspace']['deny'] = $disallowed;

		$dataHandler->fileFunc->f_ext['ftpspace']['allow'] = $allowed;
		$dataHandler->fileFunc->f_ext['ftpspace']['deny'] = $disallowed;
		
		$dataHandler->bypassFileHandling = true;
		$dataHandler->start($data, []);
		$dataHandler->admin = true;
		$dataHandler->userid = 0;
		$dataHandler->process_datamap();

		$id = $dataHandler->substNEWwithIDs[$newId];

		if(is_numeric($id) && $id > 0)
		{
			return [
				"uid" => $id,
				"filename" => $file->getName(),
			];
		}

		return null;
	}

	/**
	 * Traverses a given fieldarray and combines the values with
	 * the correct field ids
	 *
	 * @param array $fieldArray
	 * @param Datatype $datatype
	 * @return array
	 */
	public function traverseFieldArray(array $fieldArray = array(), Datatype $datatype)
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

		return $fieldArray;
	}
}
