<?php
namespace MageDeveloper\Dataviewer\DataHandling;

use MageDeveloper\Dataviewer\Utility\ArrayUtility;
use MageDeveloper\Dataviewer\Utility\LocalizationUtility as Locale;
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
class DataHandlingHook
{
	/**
	 * Object Manager
	 * 
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;
	
	/**
	 * Datatype Handling Model
	 *
	 * @var \MageDeveloper\Dataviewer\DataHandling\DataHandler\Datatype
	 */
	protected $datatypeHandling;
	
	/**
	 * Field Data Handling Model
	 * 
	 * @var \MageDeveloper\Dataviewer\DataHandling\DataHandler\Field
	 * @inject
	 */
	protected $fieldHandling;

	/**
	 * Record Data Handling Model
	 *
	 * @var \MageDeveloper\Dataviewer\DataHandling\DataHandler\Record
	 * @inject
	 */
	protected $recordHandling;

	/**
	 * Constructor
	 *
	 * @return DataHandlingHook
	 */
	public function __construct()
	{
		$this->objectManager 		= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
	
		$this->datatypeHandling		= $this->objectManager->get(\MageDeveloper\Dataviewer\DataHandling\DataHandler\Datatype::class);
		$this->fieldHandling		= $this->objectManager->get(\MageDeveloper\Dataviewer\DataHandling\DataHandler\Field::class);
		$this->recordHandling		= $this->objectManager->get(\MageDeveloper\Dataviewer\DataHandling\DataHandler\Record::class);
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
		$this->datatypeHandling->processCmdmap_deleteAction($table, $id, $recordToDelete, $recordWasDeleted, $parentObj);
		$this->fieldHandling->processCmdmap_deleteAction($table, $id, $recordToDelete, $recordWasDeleted, $parentObj);
		$this->recordHandling->processCmdmap_deleteAction($table, $id, $recordToDelete, $recordWasDeleted, $parentObj);
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
		$this->datatypeHandling->processDatamap_afterDatabaseOperations($status, $table, $id, $fieldArray, $parentObj);
		$this->fieldHandling->processDatamap_afterDatabaseOperations($status, $table, $id, $fieldArray, $parentObj);
		$this->recordHandling->processDatamap_afterDatabaseOperations($status, $table, $id, $fieldArray, $parentObj);
	}

	/**
	 * Prevent saving of a news record if the editor doesn't have access to all categories of the news record
	 *
	 * @param array $incomingFieldArray
	 * @param string $table
	 * @param int $id
	 * @param \TYPO3\CMS\Core\DataHandling\DataHandler $parentObj
	 */
	public function processDatamap_preProcessFieldArray(&$incomingFieldArray, $table, $id, &$parentObj)
	{
		$this->datatypeHandling->processDatamap_preProcessFieldArray($incomingFieldArray, $table, $id, $parentObj);
		$this->fieldHandling->processDatamap_preProcessFieldArray($incomingFieldArray, $table, $id, $parentObj);
		$this->recordHandling->processDatamap_preProcessFieldArray($incomingFieldArray, $table, $id, $parentObj);
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
		$this->datatypeHandling->processCmdmap($command, $table, $id, $value, $commandIsProcessed, $parentObj, $pasteUpdate);
		$this->fieldHandling->processCmdmap($command, $table, $id, $value, $commandIsProcessed, $parentObj, $pasteUpdate);
		$this->recordHandling->processCmdmap($command, $table, $id, $value, $commandIsProcessed, $parentObj, $pasteUpdate);
	}

}
