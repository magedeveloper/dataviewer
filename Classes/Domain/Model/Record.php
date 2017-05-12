<?php
namespace MageDeveloper\Dataviewer\Domain\Model;

use MageDeveloper\Dataviewer\Utility\DebugUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\UnknownClassException;

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
class Record extends AbstractModel
{
	/**
	 * Record - Datatype Relation
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Model\Datatype
	 */
	protected $datatype = NULL;

	/**
	 * Datatype Icon
	 *
	 * @var string
	 */
	protected $icon = '';

	/**
	 * Record Title
	 *
	 * @var string
	 */
	protected $title = '';

	/**
	 * Record Content
	 *
	 * @var string
	 */
	protected $recordContent = '';

	/**
	 * Record Values
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\MageDeveloper\Dataviewer\Domain\Model\RecordValue>
	 * @lazy
	 * @cascade remove
	 */
	protected $recordValues = NULL;

	/**
	 * Field is deleted
	 *
	 * @var boolean
	 */
	protected $deleted = FALSE;

	/**
	 * Record Timestamp
	 *
	 * @var int
	 */
	protected $tstamp;

	/**
	 * Record Sorting
	 *
	 * @var int
	 */
	protected $sorting;

	/**
	 * Field is hidden
	 *
	 * @var boolean
	 */
	protected $hidden = FALSE;

	/**
	 * _languageUid
	 * @var int
	 */
	protected $_languageUid;

	/**
	 * Finalized record values
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\ValueRepository
	 * @inject
	 */
	protected $values;

	/**
	 * Object Manager Instance
	 *
	 * @var ObjectManager
	 */
	protected $objectManager;

	/**
	 * __construct
	 */
	public function __construct()
	{
		//Do not remove the next line: It would break the functionality
		$this->initStorageObjects();
	}

	/**
	 * Gets the object manager
	 *
	 * @return \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	public function getObjectManager()
	{
		if(!$this->objectManager)
			$this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);

		return $this->objectManager;
	}

	/**
	 * Initializes all ObjectStorage properties
	 * Do not modify this method!
	 * It will be rewritten on each save in the extension builder
	 * You may modify the constructor of this class instead
	 *
	 * @return void
	 */
	protected function initStorageObjects()
	{
		$this->recordValues = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}

	/**
	 * Gets the values
	 *
	 * @return \MageDeveloper\Dataviewer\Domain\Repository\ValueRepository
	 */
	public function getValues()
	{
		if(!$this->values instanceof \MageDeveloper\Dataviewer\Domain\Repository\ValueRepository)
			$this->values = new \MageDeveloper\Dataviewer\Domain\Repository\ValueRepository();

		return $this->values;
	}

	/**
	 * Gets a value by a given field id
	 *
	 * @param int $fieldId
	 * @return \MageDeveloper\Dataviewer\Domain\Model\Value
	 */
	public function getValueByFieldId($fieldId)
	{
		$values = $this->getValues();
		return $values->getValueByFieldId($fieldId);
	}

	/**
	 * Gets a value by a given field
	 *
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Field $field
	 * @return \MageDeveloper\Dataviewer\Domain\Model\Value
	 */
	public function getValueByField(\MageDeveloper\Dataviewer\Domain\Model\Field $field)
	{
		$id = $field->getUid();
		return $this->getValueByFieldId($id);
	}

	/**
	 * Sets the values with an valueRepository
	 *
	 * @param \MageDeveloper\Dataviewer\Domain\Repository\ValueRepository $values
	 * @return void
	 */
	public function setValues(\MageDeveloper\Dataviewer\Domain\Repository\ValueRepository $values)
	{
		$this->values = $values;
	}

	/**
	 * Returns the datatype
	 *
	 * @return \MageDeveloper\Dataviewer\Domain\Model\Datatype $datatype
	 */
	public function getDatatype()
	{
		return $this->datatype;
	}

	/**
	 * Sets the datatype
	 *
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Datatype $datatype
	 * @return void
	 */
	public function setDatatype(\MageDeveloper\Dataviewer\Domain\Model\Datatype $datatype)
	{
		$this->datatype = $datatype;
	}

	/**
	 * Returns the icon
	 *
	 * @return string $icon
	 */
	public function getIcon()
	{
		return $this->icon;
	}

	/**
	 * Sets the icon
	 *
	 * @param string $icon
	 * @return void
	 */
	public function setIcon($icon)
	{
		$this->icon = $icon;
	}

	/**
	 * Gets the title
	 *
	 * @param bool $raw
	 * @return string
	 */
	public function getTitle($raw = false)
	{
		if (!$this->title && $this->getDatatype() && $raw === false)
			return \MageDeveloper\Dataviewer\Utility\LocalizationUtility::translate("entry", $this->getDatatype()->getName());

		return $this->title;
	}

	/**
	 * Sets the title
	 *
	 * @param string $title
	 * @return void
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * Appends Title Information
	 * Uses the title divider, that is configured
	 * in the datatype
	 *
	 * @param string $append Title to append
	 * @return void
	 */
	public function appendTitle($append)
	{
		$divider = $this->getDatatype()->getTitleDivider();
		$divider = str_replace("X", " ", $divider);

		$this->title .= $divider.$append;
		$this->title = trim($this->title, $divider);

	}

	/**
	 * Sets the recordContent
	 *
	 * @param string $recordContent
	 * @return void
	 */
	public function setRecordContent($recordContent)
	{
		$this->recordContent = $recordContent;
	}

	/**
	 * Returns the recordContent
	 *
	 * @return string $recordContent
	 */
	public function getRecordContent()
	{
		return $this->recordContent;
	}

	/**
	 * Adds a RecordValue
	 *
	 * @param \MageDeveloper\Dataviewer\Domain\Model\RecordValue $recordValue
	 * @return void
	 */
	public function addRecordValue(\MageDeveloper\Dataviewer\Domain\Model\RecordValue $recordValue)
	{
		$this->recordValues->attach($recordValue);
	}

	/**
	 * Removes a RecordValue
	 *
	 * @param \MageDeveloper\Dataviewer\Domain\Model\RecordValue $recordValueToRemove The RecordValue to be removed
	 * @return void
	 */
	public function removeRecordValue(\MageDeveloper\Dataviewer\Domain\Model\RecordValue $recordValueToRemove)
	{
		$this->recordValues->detach($recordValueToRemove);
	}

	/**
	 * Returns the recordValues
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\MageDeveloper\Dataviewer\Domain\Model\RecordValue> $recordValues
	 */
	public function getRecordValues()
	{
		if ($this->recordValues)
			return $this->recordValues;
	}

	/**
	 * Sets the recordValues
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\MageDeveloper\Dataviewer\Domain\Model\RecordValue> $recordValues
	 * @return void
	 */
	public function setRecordValues(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $recordValues)
	{
		$this->recordValues = $recordValues;
	}

	/**
	 * Gets the deleted status
	 *
	 * @return boolean
	 */
	public function isDeleted()
	{
		return $this->deleted;
	}

	/**
	 * Sets the record value deleted
	 *
	 * @param boolean $deleted
	 * @return void
	 */
	public function setDeleted($deleted)
	{
		$this->deleted = $deleted;
	}

	/**
	 * Gets an according record value by a field
	 *
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Field $field
	 * @return \MageDeveloper\Dataviewer\Domain\Model\RecordValue|bool
	 */
	public function getRecordValueByField(\MageDeveloper\Dataviewer\Domain\Model\Field $field)
	{
		$recordValues = [];

		if ($this->recordValues)
		{
			foreach ($this->recordValues as $_recordValue)
			{
				/* @var \MageDeveloper\Dataviewer\Domain\Model\RecordValue $_recordValue */
				if($_recordValue->getField() instanceof \MageDeveloper\Dataviewer\Domain\Model\Field)
					if ($_recordValue->getField()->getUid() == $field->getUid())
						return $_recordValue;

			}
		}

		return false;
	}

	/**
	 * Gets the timestamp
	 *
	 * @return int
	 */
	public function getTstamp()
	{
		return $this->tstamp;
	}

	/**
	 * Sets the timestamp
	 *
	 * @param int $tstamp
	 * @return void
	 */
	public function setTstamp($tstamp)
	{
		$this->tstamp = $tstamp;
	}

	/**
	 * Gets the sorting value
	 *
	 * @return int
	 */
	public function getSorting()
	{
		return $this->sorting;
	}

	/**
	 * Sets the sorting value
	 *
	 * @param int $sorting
	 * @return void
	 */
	public function setSorting($sorting)
	{
		$this->sorting = $sorting;
	}

	/**
	 * Checks if the fieldvalue is hidden
	 *
	 * @return bool
	 */
	public function isHidden()
	{
		return $this->hidden;
	}

	/**
	 * Sets the hidden status
	 *
	 * @param bool $hidden
	 */
	public function setHidden($hidden)
	{
		$this->hidden = $hidden;
	}

	/**
	 * Gets the hidden status
	 *
	 * @return bool
	 */
	public function getHidden()
	{
		return $this->hidden;
	}

	/**
	 * @param int $_languageUid
	 * @return void
	 */
	public function set_languageUid($_languageUid) 
	{
		$this->_languageUid = $_languageUid;
	}

	/**
	 * @return int
	 */
	public function get_languageUid() 
	{
		return $this->_languageUid;
	}

	/**
	 * Determines if the record has an title field
	 * or needs to use its own title
	 *
	 * @return bool
	 */
	public function hasTitleField()
	{
		if(!$this->getDatatype() instanceof \MageDeveloper\Dataviewer\Domain\Model\Datatype)
			return false;

		$fields = $this->getDatatype()->getFields();

		foreach($fields as $_field)
		{
			/* @var Field $_field */
			if ($_field->getIsRecordTitle())
				return true;
		}

		return false;
	}

	/**
	 * Converts field names for Setters and Getters
	 * @param string $name
	 * @return string
	 */
	protected function _underscore($name)
	{
		$result = strtolower(preg_replace("/(.)([A-Z])/", "$1_$2", $name));
		return $result;
	}

	/**
	 * Gets debugging information for the current record
	 *
	 * @return string
	 */
	public function getDebuggingInformation()
	{
		echo DebugUtility::debugVariable($this, "Record Information");
	}

	/**
	 * Set/Get attribute wrapper
	 * @param string $method
	 * @param array $args
	 * @throws \Exception
	 * @return mixed
	 */
	public function __call($method, $args)
	{
		$key = $this->_underscore(substr($method,3));

		if($key == "__debug")
			return $this->getDebuggingInformation();

		switch( substr($method, 0, 3) )
		{
			case "get":
				$field = $this->getFieldByCode($key);
				if($field instanceof \MageDeveloper\Dataviewer\Domain\Model\Field)
					$this->initializeValue($field);

				return $this->getValues()->getValueByCode($key);
				break;
		}
		return;
	}

	/**
	 * Gets a assigned field by a field code
	 *
	 * @param string $code
	 * @return null|\MageDeveloper\Dataviewer\Domain\Model\Field
	 */
	public function getFieldByCode($code)
	{
		if (!$this->getDatatype())
			return;

		$fields = $this->getDatatype()->getFields();

		/* @var \MageDeveloper\Dataviewer\Domain\Model\Field $_field */
		foreach($fields as $_field)
		{
			if($_field->getCode() == $code)
			{
				return $_field;
			}
		}

		return;
	}

	/**
	 * Prepares the requested value for usage and
	 * builds the structure that is needed for
	 * the according field and value
	 *
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Field $field
	 * @return void
	 */
	public function initializeValue(\MageDeveloper\Dataviewer\Domain\Model\Field $field)
	{
		if (!$this->getDatatype())
			return;

		// We initialize the object manager that is needed for building the value
		$objectManager = $this->getObjectManager();

		/* @var \MageDeveloper\Dataviewer\Service\Settings\FieldtypeSettingsService $fieldtypeSettingsService */
		$fieldtypeSettingsService = $objectManager->get(\MageDeveloper\Dataviewer\Service\Settings\FieldtypeSettingsService::class);

		// We create a blank value model here that will be filled up in the later steps
		$value  	= new \MageDeveloper\Dataviewer\Domain\Model\Value();
		$config		= $fieldtypeSettingsService->getFieldtypeConfiguration($field->getType());
		$valueClass	= $config->getValueClass();

		// In case the value class doesn't exist, we fallback to our general value class
		if(!$objectManager->isRegistered($valueClass))
			$valueClass = \MageDeveloper\Dataviewer\Form\Fieldvalue\General::class;

		/* @var \MageDeveloper\Dataviewer\Form\Fieldvalue\FieldvalueInterface $fieldvalue */
		$fieldvalue = $objectManager->get($valueClass);
		$fieldvalue->setField($field);
		$value->setFieldvalue($fieldvalue);

		$recordValue = $this->getRecordValueByField($field);

		if($recordValue instanceof \MageDeveloper\Dataviewer\Domain\Model\RecordValue)
		{
			if($fieldvalue instanceof \MageDeveloper\Dataviewer\Form\Fieldvalue\FieldvalueInterface)
			{
				// We need to initialize the fieldvalue with the plain value
				$fieldvalue->setValue($recordValue->getValueContent());
			}

		}
		else
		{
			// Temporary RecordValue
			/* @var \MageDeveloper\Dataviewer\Domain\Model\RecordValue $recordValue */
			$recordValue = $objectManager->get(\MageDeveloper\Dataviewer\Domain\Model\RecordValue::class);
			$recordValue->setRecord($this);
		}

		$value->setRecordValue($recordValue);
		$value->setField($field);
		$this->getValues()->addValue($value);

		return;
	}

	/**
	 * Prepares all values of the according fields
	 *
	 * @return void
	 */
	public function initializeValues()
	{
		if (!$this->getDatatype())
			return;

		$fields = $this->getDatatype()->getFields();
		foreach($fields as $_field)
			$this->initializeValue($_field);

		return;
	}
}
