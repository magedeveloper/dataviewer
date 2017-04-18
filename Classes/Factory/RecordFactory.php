<?php
namespace MageDeveloper\Dataviewer\Factory;

use MageDeveloper\Dataviewer\Domain\Model\Record;
use MageDeveloper\Dataviewer\Domain\Model\Datatype;
use MageDeveloper\Dataviewer\Domain\Model\RecordValue;
use MageDeveloper\Dataviewer\Domain\Model\Field;
use MageDeveloper\Dataviewer\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
class RecordFactory
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

		// Traverse the data into the relevant fieldId=>value information
		if ($traverse)
			$traversedFieldArray = $this->traverseFieldArray($updateFieldArray, $record->getDatatype());
		else
			$traversedFieldArray = $updateFieldArray;

		// Check for validation errors
		$this->validationErrors = $this->recordDataHandler->validateFieldArray($traversedFieldArray, $record->getDatatype());

		// We hide the record on any error
		if(!empty($this->validationErrors))
			$record->setHidden(true);

		$result = $this->recordDataHandler->processRecord($traversedFieldArray, $record);

		return $record;
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
		$uploadFolder = str_replace("fileadmin/", "", $uploadFolder);

		/* @var \TYPO3\CMS\Core\Resource\ResourceFactory $resourceFactory */
		/* @var \TYPO3\CMS\Core\Resource\StorageRepository $defaultStorage */
		/* @var \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler */
		$resourceFactory = $this->objectManager->get(\TYPO3\CMS\Core\Resource\ResourceFactory::class);
		$defaultStorage = $resourceFactory->getDefaultStorage();
		$dataHandler = $this->objectManager->get(\TYPO3\CMS\Core\DataHandling\DataHandler::class);
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
			"uid_foreign" 	=> $record->getUid,
			"fieldname" 	=> $field->getUid(),
			"pid" 			=> $record->getPid(),
		);

		$data["tx_dataviewer_domain_model_record"][$record->getUid()] = [
			"tx_dataviewer_domain_model_record" => $newId,
		];

		$dataHandler->start($data, []);
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
