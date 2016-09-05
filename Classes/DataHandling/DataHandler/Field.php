<?php
namespace MageDeveloper\Dataviewer\DataHandling\DataHandler;

use MageDeveloper\Dataviewer\Domain\Model\Field as FieldModel;
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
class Field extends AbstractDataHandler implements DataHandlerInterface
{
	/**
	 * Field Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\FieldRepository
	 * @inject
	 */
	protected $fieldRepository;

	/**
	 * RecordValue Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\RecordValueRepository
	 * @inject
	 */
	protected $recordValueRepository;

	/**
	 * Constructor
	 *
	 * @return Field
	 */
	public function __construct()
	{
		parent::__construct();
		$this->fieldRepository			= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\FieldRepository::class);
		$this->recordValueRepository 	= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\RecordValueRepository::class); 
	}

	/**
	 * Get an field by a given id
	 *
	 * @param int $id
	 * @return FieldModel|bool
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
		if ($table != "tx_dataviewer_domain_model_field") return;
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
		if ($table != "tx_dataviewer_domain_model_field") return;
	
		// We need to delete all recordValues that are related to this field
		$recordValues = $this->recordValueRepository->findByFieldId($id);

		if($recordValues && $recordValues->count())
		{
			foreach($recordValues as $_recordValue)
			{
				$_recordValue->setDeleted(true);
				$this->recordValueRepository->update($_recordValue);
			}

			$this->persistenceManager->persistAll();
		}

		$message = Locale::translate("field_was_successfully_deleted", $id);
		$this->addBackendFlashMessage($message, '', FlashMessage::OK);
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
		if ($table != "tx_dataviewer_domain_model_field") return;
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
		if ($table != "tx_dataviewer_domain_model_field") return;
		
		$field = $this->getFieldById($id);

		if ($field)
		{
			$message  = Locale::translate("field_was_successfully_saved", array($id));
			$this->addBackendFlashMessage($message, '', FlashMessage::OK);
		}

		// Save processed data
		$this->persistenceManager->persistAll();
	}
}
