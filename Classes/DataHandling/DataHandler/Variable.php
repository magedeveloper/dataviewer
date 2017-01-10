<?php
namespace MageDeveloper\Dataviewer\DataHandling\DataHandler;

use MageDeveloper\Dataviewer\Domain\Model\Variable as VariableModel;
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
class Variable extends AbstractDataHandler implements DataHandlerInterface
{
	/**
	 * Field Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\VariableRepository
	 * @inject
	 */
	protected $variableRepository;

	/**
	 * Constructor
	 *
	 * @return Variable
	 */
	public function __construct()
	{
		parent::__construct();
		$this->variableRepository = $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\VariableRepository::class);
	}

	/**
	 * Get an variable by a given id
	 *
	 * @param int $id
	 * @return VariableModel|bool
	 */
	public function getVariableById($id)
	{
		/* @var FieldModel $field */
		$variable = $this->variableRepository->findByUid($id, false);

		if ($variable instanceof VariableModel && $variable->getUid() == $id)
			return $variable;

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
		if ($table != "tx_dataviewer_domain_model_variable") return;
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
		if ($table != "tx_dataviewer_domain_model_variable") return;

		$message = Locale::translate("variable_was_successfully_deleted", $id);
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
		if ($table != "tx_dataviewer_domain_model_variable") return;
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
		if ($table != "tx_dataviewer_domain_model_variable") return;

		if(isset($parentObj->substNEWwithIDs[$id]))
			$id = $parentObj->substNEWwithIDs[$id];
			
		if(isset($parentObj->substNEWwithIDs[$id]))
			$id = $parentObj->substNEWwithIDs[$id];
			
		$name = '-';	
		if(isset($fieldArray["variable_name"]))
		{
			$name = $fieldArray["variable_name"];
		}
		else
		{
			$variable = $this->getVariableById($id);
			$name = $variable->getVariableName();
		}		
		
		$message  = Locale::translate("variable_was_successfully_saved", [$name, $id]);
		$this->addBackendFlashMessage($message, '', FlashMessage::OK);

		// Save processed data
		$this->persistenceManager->persistAll();
	}
}
