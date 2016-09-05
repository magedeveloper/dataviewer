<?php
namespace MageDeveloper\Dataviewer\Form\FormDataProvider;

use MageDeveloper\Dataviewer\Domain\Model\Record;
use MageDeveloper\Dataviewer\Domain\Repository\FieldRepository;
use MageDeveloper\Dataviewer\Utility\LocalizationUtility as Locale;
use TYPO3\CMS\Backend\Form\Container\SingleFieldContainer;
use MageDeveloper\Dataviewer\Domain\Model\Field as Field;
use MageDeveloper\Dataviewer\Domain\Model\FieldValue as FieldValue;
use MageDeveloper\Dataviewer\Domain\Model\RecordValue as RecordValue;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement as FormElement;
use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\InvalidClassException;
use TYPO3\CMS\Extbase\Object\ObjectManager;

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
class PrepareDataviewerTca implements FormDataProviderInterface
{
	/**
	 * Object Manager
	 *
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Field Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\FieldRepository
	 * @inject
	 */
	protected $fieldRepository;

	/**
	 * Record Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\RecordRepository
	 * @inject
	 */
	protected $recordRepository;

	/**
	 * Fieldtype Settings Service
	 * 
	 * @var \MageDeveloper\Dataviewer\Service\Settings\FieldtypeSettingsService
	 * @inject
	 */
	protected $fieldtypeSettingsService;

	/**
	 * Constructor
	 *
	 * @return PrepareDataviewerTca
	 */
	public function __construct()
	{
		$this->objectManager 			= GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->fieldRepository			= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\FieldRepository::class);
		$this->recordRepository			= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\RecordRepository::class);
		$this->fieldtypeSettingsService = $this->objectManager->get(\MageDeveloper\Dataviewer\Service\Settings\FieldtypeSettingsService::class);
	}

	/**
	 * Inline parent TCA may override some TCA of children.
	 *
	 * @param array $result
	 * @return array
	 */
	public function addData(array $result)
	{
		if($result["tableName"] == "tx_dataviewer_domain_model_record") {
			$row              = $result["databaseRow"];
			$columnsToProcess = $result["columnsToProcess"];
			$record           = null;
			$uid              = (isset($row["uid"]))? $row["uid"] : null;

			if (!is_null($uid)) {
				/* @var Record $record */
				$record = $this->recordRepository->findByUid($uid, false);
			}
			
			// We instantiate a new record model, so we can provide at least an empty model
			if(!$record instanceof Record)
				$record = $this->objectManager->get(Record::class);

			foreach ($columnsToProcess as $_fieldId) {
				/* @var Field $field */
				$field = $this->fieldRepository->findByUid($_fieldId, false);
				if ($field instanceof Field) {
					// We retrieve the fieldtype configuration
					$fieldtypeConfiguration = $this->fieldtypeSettingsService->getFieldtypeConfiguration($field->getType());
					$fieldClass             = $fieldtypeConfiguration->getFieldClass();

					if ($this->objectManager->isRegistered($fieldClass)) {
						/* @var \MageDeveloper\Dataviewer\Form\Fieldtype\FieldtypeInterface $fieldtypeModel */
						$fieldtypeModel = $this->objectManager->get($fieldClass);
						$fieldtypeModel->setField($field);
						$fieldtypeModel->setRecord($record);
						
						$tca                                          = $fieldtypeModel->buildTca();
						$result['processedTca']['columns'][$_fieldId] = $tca["processedTca"]["columns"][$_fieldId];
					}
				}

			}
		}
		
		return $result;
	}
	
}
