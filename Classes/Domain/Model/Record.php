<?php
namespace MageDeveloper\Dataviewer\Domain\Model;

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
	 * Field is hidden
	 *
	 * @var boolean
	 */
	protected $hidden = FALSE;

	/**
	 * Finalized record values
	 * 
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\ValueRepository
	 */
	protected $values;

	/**
	 * __construct
	 */
	public function __construct()
	{
		//Do not remove the next line: It would break the functionality
		$this->initStorageObjects();
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
	 * Prepares the main values that are combined through
	 * the recordValues
	 * 
	 * @return void
	 */
	public function initializeValues()
	{
		if($this->values instanceof \MageDeveloper\Dataviewer\Domain\Repository\ValueRepository)
			return;
		else	
			$this->values = new \MageDeveloper\Dataviewer\Domain\Repository\ValueRepository();
			
		$objectManager = GeneralUtility::makeInstance(ObjectManager::class);
		/* @var \MageDeveloper\Dataviewer\Service\Settings\FieldtypeSettingsService $fieldtypeSettingsService */
		$fieldtypeSettingsService = $objectManager->get(\MageDeveloper\Dataviewer\Service\Settings\FieldtypeSettingsService::class);
		
		if (!$this->getDatatype())
			return;
		
		$fields = $this->getDatatype()->getFields();
		/* @var \MageDeveloper\Dataviewer\Domain\Model\Field $_field */
		foreach($fields as $_field)
		{
			// We create a value for each field
			$value 			= new \MageDeveloper\Dataviewer\Domain\Model\Value();
			$config			= $fieldtypeSettingsService->getFieldtypeConfiguration($_field->getType());
			$valueClass		= $config->getValueClass();

			if(!$objectManager->isRegistered($valueClass)) {
				$valueClass = \MageDeveloper\Dataviewer\Form\Fieldvalue\General::class;
			}

			/* @var \MageDeveloper\Dataviewer\Form\Fieldvalue\FieldvalueInterface $fieldvalue */
			$fieldvalue = $objectManager->get($valueClass);
			$fieldvalue->setField($_field);
			$value->setFieldvalue($fieldvalue);

			$recordValue = $this->getRecordValueByField($_field);
			if($recordValue instanceof \MageDeveloper\Dataviewer\Domain\Model\RecordValue)
			{
				if($fieldvalue instanceof \MageDeveloper\Dataviewer\Form\Fieldvalue\FieldvalueInterface)
				{
					// We need to initialize the fieldvalue with the plain value
					$fieldvalue->setValue($recordValue->getValueContent());
				}

				$value->setRecordValue($recordValue);
			}
			else
			{
			}

			$value->setField($_field);
			
			$this->values->addValue($value);			
		}
		
		return;
	}

	/**
	 * Gets the values
	 * 
	 * @return \MageDeveloper\Dataviewer\Domain\Repository\ValueRepository
	 */
	public function getValues()
	{
		if(!$this->values instanceof \MageDeveloper\Dataviewer\Domain\Repository\ValueRepository)
			$this->initializeValues();
			
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
	 * @return string
	 */
	public function getTitle()
	{
		if (!$this->title && $this->getDatatype())
			return $this->getDatatype()->getName() . " " . \MageDeveloper\Dataviewer\Utility\LocalizationUtility::translate("entry", $this->getUid());
		
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

		$this->title .= $divider.$append;
		$this->title = trim($this->title, $divider);
		$this->title = str_replace("X", " ", $this->title);
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
	 * Set/Get attribute wrapper
	 * @param string $method
	 * @param array $args
	 * @throws \Exception
	 * @return mixed
	 */
	public function __call($method, $args)
	{
		$this->initializeValues();
		
		$key = $this->_underscore(substr($method,3));
		switch( substr($method, 0, 3) )
		{
			case "get":
				return $this->values->getValueByCode($key);
				break;
		}
		return;
	}

}
