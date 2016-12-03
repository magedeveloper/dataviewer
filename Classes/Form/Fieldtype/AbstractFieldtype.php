<?php
namespace MageDeveloper\Dataviewer\Form\Fieldtype;

use MageDeveloper\Dataviewer\Utility\FieldtypeConfigurationUtility;
use MageDeveloper\Dataviewer\Utility\LocalizationUtility as Locale;
use TYPO3\CMS\Backend\Form\Container\SingleFieldContainer;
use MageDeveloper\Dataviewer\Domain\Model\Record as RecordModel;
use MageDeveloper\Dataviewer\Domain\Model\Field as FieldModel;
use MageDeveloper\Dataviewer\Domain\Model\FieldValue as FieldValueModel;
use MageDeveloper\Dataviewer\Domain\Model\RecordValue as RecordValueModel;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement as FormElement;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\InvalidClassException;

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
abstract class AbstractFieldtype
{
	/**
	 * The record id,
	 * either NEW<hash> or an INT-Id
	 *
	 * @var string|int
	 */
	protected $recordId;

	/**
	 * According Pid for 
	 * record storage
	 * 
	 * @var int
	 */
	protected $pid;

	/**
	 * Object Manager
	 *
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * RecordValue Session Service
	 * 
	 * @var \MageDeveloper\Dataviewer\Service\Session\RecordValueSessionService
	 * @inject
	 */
	protected $recordValueSessionService;

	/**
	 * Field Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\FieldRepository
	 * @inject
	 */
	protected $fieldRepository;

	/**
	 * Record Value Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\RecordValueRepository
	 * @inject
	 */
	protected $recordValueRepository;

	/**
	 * TypoScript Utility
	 *
	 * @var \MageDeveloper\Dataviewer\Utility\TypoScriptUtility
	 * @inject
	 */
	protected $typoScriptUtility;

	/**
	 * Node Factory
	 *
	 * @var \TYPO3\CMS\Backend\Form\NodeFactory
	 * @inject
	 */
	protected $nodeFactory;

	/**
	 * Record
	 *
	 * @var RecordModel
	 */
	protected $record;

	/**
	 * Field
	 *
	 * @var FieldModel
	 */
	protected $field;

	/**
	 * Table Name
	 *
	 * @var string
	 */
	protected $tableName;

	/**
	 * Form Data Providers
	 *
	 * @var array
	 */
	protected $formDataProviders = [];

	/**
	 * Constructor
	 *
	 * @return AbstractFieldtype
	 */
	public function __construct()
	{
		$this->objectManager 				= GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->recordValueSessionService 	= $this->objectManager->get(\MageDeveloper\Dataviewer\Service\Session\RecordValueSessionService::class);
		$this->fieldRepository				= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\FieldRepository::class);
		$this->recordValueRepository 		= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\RecordValueRepository::class);
		$this->nodeFactory 					= $this->objectManager->get(\TYPO3\CMS\Backend\Form\NodeFactory::class);
		$this->typoScriptUtility			= $this->objectManager->get(\MageDeveloper\Dataviewer\Utility\TypoScriptUtility::class);

		$this->initializeFormDataProviders();
	}

	/**
	 * Gets the record id
	 * 
	 * @return int|string
	 */
	public function getRecordId()
	{
		return $this->recordId;
	}

	/**
	 * Sets the record id
	 * 
	 * @param int|string $recordId
	 * @return void
	 */
	public function setRecordId($recordId)
	{
		$this->recordId = $recordId;
	}

	/**
	 * Gets the storage pid
	 * 
	 * @return int
	 */
	public function getPid()
	{
		return $this->pid;
	}

	/**
	 * Sets the storage pid
	 * 
	 * @param int $pid
	 * @return void
	 */
	public function setPid($pid)
	{
		$this->pid = (int)$pid;
	}
	
	/**
	 * Gets the record
	 *
	 * @return RecordModel
	 */
	public function getRecord()
	{
		return $this->record;
	}

	/**
	 * Sets the record
	 *
	 * @param RecordModel $record
	 * @return void
	 */
	public function setRecord(RecordModel $record)
	{
		$this->record = $record;
	}

	/**
	 * Gets the field
	 *
	 * @return Field
	 */
	public function getField()
	{
		return $this->field;
	}

	/**
	 * Sets the field
	 *
	 * @param FieldModel $field
	 * @return void
	 */
	public function setField(FieldModel $field)
	{
		$this->field = $field;
	}

	/**
	 * Initializes all form data providers to
	 * $this->formDataProviders
	 *
	 * Will be executed in order of the added providers!
	 *
	 * @return void
	 */
	public function initializeFormDataProviders()
	{
		$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\EvaluateDisplayConditions::class;

		foreach($this->formDataProviders as $key=>$_fdp)
		{
			if (is_string($_fdp))
				$this->formDataProviders[$key] = $this->objectManager->get($_fdp);
		}

	}

	/**
	 * Prepares the TCA Array with
	 * the form data providers
	 *
	 * @param array $tca
	 */
	public function prepareTca(array &$tca)
	{
		$fieldName = $tca["fieldName"];

		// requestUpdate
		if($requestUpdate = $this->getField()->getRequestUpdate())
			$tca["processedTca"]["ctrl"]["requestUpdate"] = $fieldName;

		//displayCond
		if($displayCond = $this->getField()->getDisplayCond())
			$tca["processedTca"]["columns"][$fieldName]["displayCond"] = $displayCond;

		foreach($this->formDataProviders as $fdp)
			$tca = $fdp->addData($tca);

	}

	/**
	 * Determines the value of the field
	 *
	 * @param FieldValueModel $fieldValue
	 * @param int $position Position of the value
	 * @param bool $returnFull Return full content
	 * @return mixed
	 */
	protected function _getDefaultValue(FieldValueModel $fieldValue, $position = 0, $returnFull = false)
	{
		// If it pretends to be empty, we return null
		if($fieldValue->getPretendsEmpty())
			return;
			
		$defaultValue = null;

		if ($fieldValue->isDefault())
		{
			switch($fieldValue->getType())
			{
				case FieldValueModel::TYPE_TYPOSCRIPT:
					$typoscript = $fieldValue->getValueContent();
					$tsValue = $this->typoScriptUtility->getTypoScriptValue($typoscript);
					$defaultValue = $tsValue;
					break;
				case FieldValueModel::TYPE_DATABASE:
					$values = $this->fieldRepository->findEntriesForFieldValue($fieldValue);
					$valueArr = [];
					foreach($values as $_value)
						$valueArr[] = implode(", ", array_values($_value));

					$defaultValue = $valueArr;

					break;
				default:
					$defaultValue = [$fieldValue->getValueContent()];
					break;
			}
		}

		if($returnFull)
			return $defaultValue;

		if (is_array($defaultValue) && !is_null($position))
			return $defaultValue[$position];
		else if(is_array($defaultValue) && is_null($position))
			return reset($valueContent);
		else if(is_string($defaultValue) && strlen($defaultValue))
			return $defaultValue;

		return;
	}

	/**
	 * Gets flexform default content for a field
	 *
	 * @param string $flex
	 * @return string
	 */
	protected function _getFlexDefault($flex)
	{
		$extKey = \MageDeveloper\Dataviewer\Configuration\ExtensionConfiguration::EXTENSION_KEY;
		$extPath = ExtensionManagementUtility::extRelPath($extKey);

		$flexFile = $extPath . "Configuration/FlexForms/Defaults/".$flex.".xml";

		if (file_exists($flexFile))
		{
			$contents = GeneralUtility::getUrl($flexFile);
			return GeneralUtility::xml2array($contents);
		}

		return;
	}

	/**
	 * Gets an array with items from fieldvalues
	 * even when they are from database
	 * So this will contain all possible records
	 *
	 * @return array
	 */
	public function getFieldItems()
	{
		$fieldValues = $this->getField()->getFieldValues();
		$items = [];
		foreach($fieldValues as $_fieldValue)
		{
			$itemsFromFieldValue = $this->getFieldValueItems($_fieldValue);
			foreach($itemsFromFieldValue as $_it)
				$items[] = $_it;
		}

		return $items;
	}

	/**
	 * Processes the field items and returns a clean array
	 * for select boxes
	 * 
	 * @return array
	 */
	public function getItems()
	{
		$fieldItemsArray = $this->getFieldItems();
		$items = [];
		foreach($fieldItemsArray as $_fi)
		{
			$label = reset($_fi);
			$value = end($_fi);
			$items[$value] = $label;
		}
		return $items;
	}

	/**
	 * Gets all items that come with a fieldValue
	 *
	 * @param FieldValueModel $fieldValueModel
	 * @return array
	 */
	public function getFieldValueItems(FieldValueModel $fieldValueModel)
	{
		$items = [];

		/* @var FieldValue $_fieldValue */
		switch($fieldValueModel->getType())
		{
			case FieldValueModel::TYPE_TYPOSCRIPT:
				$typoScript = $fieldValueModel->getValueContent();
				$value = $this->typoScriptUtility->getTypoScriptValue($typoScript);
				$label = $this->typoScriptUtility->getTypoScriptValue($typoScript);

				if($fieldValueModel->getPretendsEmpty())
					$value = null;

				$items[] = [$label, $value];
				break;
			case FieldValueModel::TYPE_FIXED_VALUE:
				$translation = Locale::translate("value.{$fieldValueModel->getValueContent()}");
				if (!$translation) $translation = $fieldValueModel->getValueContent();

				$value = $fieldValueModel->getValueContent();
				$label = $translation;

				if($fieldValueModel->getPretendsEmpty())
					$value = null;

				$items[] = [$label, $value];
				break;
			case FieldValueModel::TYPE_DATABASE:
				$values = $this->fieldRepository->findEntriesForFieldValue($fieldValueModel);
				foreach($values as $i=>$_value)
				{
					$value = $_value[$fieldValueModel->getColumnName()];
					$translation = Locale::translate("value.{$fieldValueModel->getValueContent()}");
					if (!$translation) $translation = $value;

					if($fieldValueModel->getPretendsEmpty())
						$value = null;

					$items[] = [$translation, $value];
				}
			case FieldValueModel::TYPE_FIELDVALUES:
				$field = $fieldValueModel->getFieldContent();
				if($field instanceof FieldModel)
				{
					$fieldItems = $this->getAllFieldItems($field);
					if(!empty($fieldItems))
					{
						foreach($fieldItems as $value)
							$items[] = [$value, $value];
					}
				}
				break;
			default:
				break;
		}

		return $items;
	}

	/**
	 * Processes a field type and obtains all possible recordValues
	 * from the database with a finalization of these values
	 * into a straight array for usage
	 * 
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Field $field
	 * @return array
	 */
	public function getAllFieldItems(FieldModel $field)
	{
		$items = [];
		
		$fieldtypeConfiguration = FieldtypeConfigurationUtility::getFieldtypeConfiguration($field->getType());
		$valueClass = $fieldtypeConfiguration->getValueClass();

		if (!$this->objectManager->isRegistered($valueClass))
			$valueClass = \MageDeveloper\Dataviewer\Form\Fieldvalue\General::class;

		/* @var \MageDeveloper\Dataviewer\Form\Fieldvalue\FieldvalueInterface $fieldValue */
		$fieldvalue = $this->objectManager->get($valueClass);

		if($fieldvalue instanceof \MageDeveloper\Dataviewer\Form\Fieldvalue\FieldvalueInterface)
		{
			$recordValues = $this->recordValueRepository->findByField($field);

			/* @var \MageDeveloper\Dataviewer\Domain\Model\RecordValue $_recordValue */
			foreach($recordValues as $_recordValue)
			{
				// We need to initialize the fieldvalue with the plain value
				$fieldvalue->init($field, $_recordValue->getValueContent());

				// We retrieve our needed data from the fieldvalue
				$valueArray = $fieldvalue->getValueArray();

                foreach($valueArray as $_item)
                    $items[] = $_item;

                // Unique Values
                $items = array_unique($items);

			}
		}
		
		return $items;
	}

	/**
	 * Gets an related record value for a field value
	 * If there is no related record value, we return
	 * a new one
	 *
	 * @param FieldValue $fieldValue
	 * @return RecordValue
	 */
	public function getRelatedRecordValueForFieldValue(FieldValue $fieldValue)
	{
		// Searches the record for a equivalent field value
		$recordValues = $this->getRecord()->getRecordValues();

		if (count($recordValues))
		{
			foreach($recordValues as $_recordValue)
			{
				/* @var RecordValue $_recordValue */
				if ($_recordValue->getFieldValue() instanceof FieldValueModel)
				{
					if ($_recordValue->getField() instanceof FieldModel)
					{
						if ($_recordValue->getFieldValue()->getUid() == $fieldValue->getUid() && // this would also be enough to check ;)
							$_recordValue->getField()->getUid() == $fieldValue->getField()->getUid()
						) {
							return $_recordValue;
						}
					}
				}
			}
		}

		/* @var RecordValueModel $newRecordValueObj */
		$newRecordValueObj = $this->objectManager->get(RecordValueModel::class);
		$newRecordValueObj->setRecord($this->getRecord());

		return $newRecordValueObj;
	}

	/**
	 * Gets a plain array with record
	 * values and information
	 *
	 * @return array
	 */
	public function getDatabaseRow()
	{
		$databaseRow = [];

		$databaseRow["uid"] = $this->getRecordId();
		$databaseRow["pid"] = $this->getPid();
		$recordValues = $this->getRecord()->getRecordValues();

		foreach($recordValues as $_rV)
		{
			/* @var RecordValue $_rV */

			if ($_rV->getField() instanceof FieldModel)
				$databaseRow[$_rV->getField()->getUid()] = $_rV->getValueContent();
		}

		return $databaseRow;
	}

	/**
	 * Gets the tca value for the field
	 *
	 * @param bool $default Return only default value
	 * @return mixed
	 */
	public function getValue($default = false, $noSessionValue = false)
	{
		if(!$default && !$noSessionValue)
		{
			// We get a session value if it exists and if this method shall return the record value content
			$sessionValue = $this->recordValueSessionService->getStoredValueForRecordIdAndFieldId($this->getRecordId(), $this->getField()->getUid());
			
			if(!is_null($sessionValue))
				return $sessionValue;
		}
		
		$value = "";

		// Get all according record values for the field
		// If no record values are found, we need all default values (field values with default setting) for this field
		// Implode the content of the default values and return them as a string
		$recordValue = $this->getRecord()->getRecordValueByField($this->getField());

		if ($recordValue instanceof RecordValueModel && $default === false)
		{
			/* @var RecordValueModel $_recordValue */
			$value = $recordValue->getValueContent();
		}
		else
		{
			// We need to retrieve the default value content for the field
			$fieldValues = $this->getField()->getFieldValues();
			$values = [];
			foreach($fieldValues as $_fieldValue)
			{
				$default = $this->_getDefaultValue($_fieldValue, 0, true);
				if (is_array($default))
					$values = array_merge($values, $default);
				else
					$values[] = $default;

			}
			
			$value = implode(", ", $values);
		}

		// Each value is combined with a comma to fit in the input field
		return $value;
	}

	/**
	 * Renders a field
	 *
	 * @return array
	 */
	public function render()
	{
		$tca                  = $this->buildTca();
		
		// We can inject our session values here
		
		$singleFieldContainer = $this->objectManager->get(SingleFieldContainer::class, $this->nodeFactory, $tca);
		$resultArray          = $singleFieldContainer->render($tca);
		return $resultArray;
	}

	/**
	 * Builds the tca
	 * In this case, we do nothing
	 *
	 * @return array
	 */
	public function buildTca()
	{
		$this->tca = [];
		return $this->tca;
	}

	/**
	 * Gets the field tca configuration
	 *
	 * @return array
	 */
	public function getFieldTca()
	{
		$tca = $this->buildTca();

		if(isset($tca["processedTca"]["columns"][$this->getField()->getUid()]["config"]))
			return $tca["processedTca"]["columns"][$this->getField()->getUid()]["config"];

		return [];
	}

	/**
	 * Gets a tca error array with
	 * calling a userfunc for displaying
	 * an alert error message with given
	 * string
	 * 
	 * @param string $message
	 * @return array
	 */
	public function getErrorTca($message)
	{
		$fieldName = $this->getField()->getUid();
		return [
			"fieldName" => $fieldName,
			"processedTca" => [
				"columns" => [
					$fieldName => [
						"exclude" => 1,
						"label" => $this->getField()->getFrontendLabel(),
						"config" => [
							"type" => "user",
							"userFunc" => 'MageDeveloper\Dataviewer\UserFunc\Text->displayErrorText',
							"parameters" => [
								"message" => $message,
							],
						],
					],
				],
			],
			"inlineStructure" => [],
		];
	}
}
