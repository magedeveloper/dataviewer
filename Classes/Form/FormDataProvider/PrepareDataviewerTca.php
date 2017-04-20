<?php
namespace MageDeveloper\Dataviewer\Form\FormDataProvider;

use MageDeveloper\Dataviewer\Domain\Model\Datatype;
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
	 * Datatype Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\DatatypeRepository
	 * @inject
	 */
	protected $datatypeRepository;

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
		$this->datatypeRepository		= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\DatatypeRepository::class);
		$this->fieldtypeSettingsService = $this->objectManager->get(\MageDeveloper\Dataviewer\Service\Settings\FieldtypeSettingsService::class);
	}

	/**
	 * Prepares the temporary tca for the dataviewer stuff
	 *
	 * @param array $result
	 * @return array
	 */
	public function addData(array $result)
	{
		if($result["tableName"] == "tx_dataviewer_domain_model_record")
		{
			/**************************************************
			 * We need to prepare the databaseRow Information
			 * ----------------------------------------------
			 * The row information is not existant because
			 * our row is virtually created, so we just add
			 * the uid from the form post, to get everything
			 * right here
			 **************************************************/

			// If we have a posted value of the row uid, we use it here
			if($uid = GeneralUtility::_POST("databaseRowUid"))
				$result["databaseRow"]["uid"] = $uid;
			else if (!isset($result["databaseRow"]["uid"]) && GeneralUtility::_GET("uid"))
				$result["databaseRow"]["uid"] = GeneralUtility::_GET("uid");

			/**************************************************
			 * The row will be filled with the record values
			 * to completely fill the field information
			 * for the databaseRow
			 **************************************************/
			$record = null;
			if(is_numeric($result["databaseRow"]["uid"]) && $result["databaseRow"]["uid"] > 0)
				$record = $this->recordRepository->findByUid($result["databaseRow"]["uid"], false);

			if(!$record instanceof Record)
				$record = $this->objectManager->get(Record::class);

			$recordValues = $record->getRecordValues();
			if($recordValues->count())
			{
				foreach($recordValues as $_recordValue)
				{
					/* @var RecordValue $_recordValue */
					if($_recordValue->getField() instanceof Field)
						$result["databaseRow"][$_recordValue->getField()->getUid()] = $_recordValue->getValueContent();
				}
			}

			if($record->getDatatype() instanceof Datatype)
			{
				$datatype = $record->getDatatype();
			}
			else
			{
				$datatypeUid = null;

				if($result["databaseRow"]["datatype"] > 0)
				{
					// We extract the datatype from the databaseRow
					if(is_array($result["databaseRow"]["datatype"]))
						$datatypeUid  = reset($result["databaseRow"]["datatype"]);
					else
						$datatypeUid  = (int)$result["databaseRow"]["datatype"];
				}
				else
				{
					// We try to find the datatype uid in the url parameters
					$datatypeUid = (int)GeneralUtility::_GET("datatype");
				}

				/* @var Datatype $datatype */
				$datatype = $this->datatypeRepository->findByUid($datatypeUid, false);
			}

			// We can prepare the tca if record and datatype exists
			if($datatype instanceof Datatype && $record instanceof Record)
				$this->prepareProcessedTca($result, $datatype, $record);

		}

		return $result;
	}

	/**
	 * Prepares the tca for a given datatype
	 *
	 * @param array $result
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Datatype $datatype
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Record $record
	 * @return void
	 */
	public function prepareProcessedTca(&$result, Datatype $datatype, Record $record)
	{
		$fields = $datatype->getFields();

		foreach ($fields as $_field)
		{
			/* @var Field $_field */
			$fieldId = $_field->getUid();

			// We retrieve the fieldtype configuration
			$fieldtypeConfig = $this->fieldtypeSettingsService->getFieldtypeConfiguration($_field->getType());
			$fieldClass      = $fieldtypeConfig->getFieldClass();

			if ($this->objectManager->isRegistered($fieldClass))
			{
				/* @var \MageDeveloper\Dataviewer\Form\Fieldtype\FieldtypeInterface $fieldtypeModel */
				$fieldtypeModel = $this->objectManager->get($fieldClass);
				$fieldtypeModel->setField($_field);
				$fieldtypeModel->setRecord($record);

				$tca = $fieldtypeModel->buildTca();
				$result["processedTca"]["columns"][$fieldId] = $tca["processedTca"]["columns"][$fieldId];
			}
		}

		return;
	}

}
