<?php
namespace MageDeveloper\Dataviewer\DataHandling\DataHandler;

use MageDeveloper\Dataviewer\Domain\Model\Datatype as DatatypeModel;
use MageDeveloper\Dataviewer\Utility\LocalizationUtility as Locale;
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
class Datatype extends AbstractDataHandler implements DataHandlerInterface
{
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
	 * Constructor
	 * 
	 * @return Datatype
	 */
	public function __construct()
	{
		parent::__construct();
		$this->datatypeRepository 	= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\DatatypeRepository::class);
		$this->recordRepository		= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\RecordRepository::class);
	}

	/**
	 * Get an datatype by a given id
	 *
	 * @param int $id
	 * @return DatatypeModel|bool
	 */
	public function getDatatypeById($id)
	{
		/* @var DatatypeModel $field */
		$datatype = $this->datatypeRepository->findByUid($id, false);

		if ($datatype instanceof DatatypeModel && $datatype->getUid() == $id)
			return $datatype;

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
		if ($table != "tx_dataviewer_domain_model_datatype") return;
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
		if ($table != "tx_dataviewer_domain_model_datatype") return;
		
		// If we think about it, we should delete the following
		// - All according fields
		// - All according records
		// - All according recordValues
		// TODO: Delete connected entries
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
		if ($table != "tx_dataviewer_domain_model_datatype") return;
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
		if ($table != "tx_dataviewer_domain_model_datatype") return;
		
		$datatype = $this->getDatatypeById($id);

		if ($datatype)
		{
			// Update all according record icons with (new) datatype icon
            $this->_updateRecordIcons($datatype, $datatype->getIcon());

			$message  = Locale::translate("datatype_was_successfully_saved", [$datatype->getName(), $id]);
			$this->addBackendFlashMessage($message, '', FlashMessage::OK);
		}
		else
		{
			if ($id > 0) 
			{
				$message = Locale::translate("datatype_not_saved");
				$this->addBackendFlashMessage($message, '', FlashMessage::ERROR);
			}
		}

		// Save processed data
		$this->persistenceManager->persistAll();
	}

	/**
	 * Updates record icons when the datatype icon is changed
	 *
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Datatype $datatype
	 * @param string $newIcon
	 */
	protected function _updateRecordIcons(\MageDeveloper\Dataviewer\Domain\Model\Datatype $datatype, $newIcon)
	{
		/* @var \MageDeveloper\Dataviewer\Domain\Model\Record $_record */
		$records = $this->recordRepository->findByDatatype($datatype);

		foreach($records as $_record)
		{
			$_record->setIcon($newIcon);
			$this->recordRepository->update($_record);
		}

	}
}
