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
	 * @return Record
	 */
	public function create(array $fieldArray, Datatype $datatype = null)
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
		
		// We need to initially save the record
		$this->recordRepository->add($record);
		$this->persistenceManager->persistAll();

		
		// Traverse the data into the relevant fieldId=>value information
		$traversedFieldArray = $this->traverseFieldArray($fieldArray, $datatype);
		
		// Check for validation errors
		$this->validationErrors = $this->recordDataHandler->validateFieldArray($traversedFieldArray, $datatype);
		$result = $this->recordDataHandler->processRecord($traversedFieldArray, $record);
	
		if($result === true) 
		{
			$this->recordRepository->update($record);
			$this->persistenceManager->persistAll();

			return $record;
		}
		
		return false;
	}

	/**
	 * Updates a record with new given values
	 * 
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Record $record
	 * @param array $updateFieldArray
	 * @return bool
	 */
	public function update(Record $record, array $updateFieldArray)
	{
		// The record data has to contain a datatype
		if(!$record->getDatatype() instanceof Datatype)
		{
			throw new \MageDeveloper\Dataviewer\Exception\NoDatatypeException(
				"Missing Datatype for Record Factory -> update", 1477057477
			);
		}

		// TODO: implementation
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
