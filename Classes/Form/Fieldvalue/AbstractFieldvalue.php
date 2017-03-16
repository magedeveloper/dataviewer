<?php
namespace MageDeveloper\Dataviewer\Form\Fieldvalue;

use MageDeveloper\Dataviewer\Domain\Model\Field;
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
abstract class AbstractFieldvalue
{
	/**
	 * Object Manager
	 *
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Fieldtype Settings Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Settings\FieldtypeSettingsService
	 * @inject
	 */
	protected $fieldtypeSettingsService;
	
	/**
	 * Field Instance
	 * 
	 * @var Field
	 */
	protected $field;

	/**
	 * Value
	 * 
	 * @var mixed
	 */
	protected $value;

	/**
	 * According RecordValue
	 * 
	 * @var \MageDeveloper\Dataviewer\Domain\Model\RecordValue
	 */
	protected $recordValue;

	/**
	 * Constructor
	 *
	 * @return AbstractFieldvalue
	 */
	public function __construct()
	{
		$this->objectManager 				= GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->fieldtypeSettingsService		= $this->objectManager->get(\MageDeveloper\Dataviewer\Service\Settings\FieldtypeSettingsService::class);
	}

	/**
	 * Gets the field
	 * 
	 * @return \MageDeveloper\Dataviewer\Domain\Model\Field
	 */
	public function getField()
	{
		return $this->field;
	}

	/**
	 * Sets the field instance
	 * 
	 * @param Field $field
	 * @return AbstractFieldvalue
	 */
	public function setField(Field $field)
	{
		$this->field = $field;
		return $this;
	}

	/**
	 * Gets the value
	 * 
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * Sets the value
	 * 
	 * @param mixed $value
	 * @return AbstractFieldvalue
	 */
	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}

	/**
	 * Gets the according recordValue
	 * 
	 * @return \MageDeveloper\Dataviewer\Domain\Model\RecordValue
	 */
	public function getRecordValue()
	{
		return $this->recordValue;
	}

	/**
	 * Sets the according recordValue
	 * 
	 * @param \MageDeveloper\Dataviewer\Domain\Model\RecordValue $recordValue
	 * @return void
	 */
	public function setRecordValue($recordValue)
	{
		$this->recordValue = $recordValue;
	}

	/**
	 * Gets the according record
	 * 
	 * @return \MageDeveloper\Dataviewer\Domain\Model\Record|bool
	 */
	public function getRecord()
	{
		if($this->getRecordValue() instanceof \MageDeveloper\Dataviewer\Domain\Model\RecordValue)
			return $this->getRecordValue()->getRecord();
			
		return false;	
	}

	/**
	 * Initializes the fieldvalue with the
	 * given field and value
	 * 
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Field $field
	 * @param mixed $value
	 * @return void
	 */
	public function init(Field $field, $value)
	{
		$this->field = $field;
		$this->value = $value;
	}

	/**
	 * Gets the according fieldtype
	 * 
	 * @return \MageDeveloper\Dataviewer\Form\Fieldtype\AbstractFieldtype|null
	 */
	public function getFieldtype()
	{
		$identifier = $this->getField()->getType();
		$fieldtype = $this->getFieldtypeByIdentifier($identifier);
		$fieldtype->setField($this->getField());
		
		return $fieldtype;
	}

	/**
	 * Gets an fieldtype model by identifier
	 * 
	 * @param string $fieldtypeIdentifier
	 * @return \MageDeveloper\Dataviewer\Form\Fieldtype\AbstractFieldtype|null
	 */
	public function getFieldtypeByIdentifier($fieldtypeIdentifier)
	{
		/* @var \MageDeveloper\Dataviewer\Domain\Model\FieldtypeConfiguration $fieldtypeConfiguration */
		$fieldtypeConfiguration = $this->fieldtypeSettingsService->getFieldtypeConfiguration($fieldtypeIdentifier);
		$fieldClass = $fieldtypeConfiguration->getFieldClass();
		
		if ($this->objectManager->isRegistered($fieldClass))
			return $this->objectManager->get($fieldClass);
		
		return null;	
	}

	/**
	 * Checks if input value is an xml
	 * 
	 * @param mixed $value
	 * @return bool
	 */
	protected function _isXml($value)
	{
		if(is_string($value) && $value != "")
		{
			libxml_use_internal_errors(true);
			$doc = new \DOMDocument('1.0', 'utf-8');
			$doc->loadXML($value);
			$errors = libxml_get_errors();
			return empty($errors);
		}
		
		return false;
	}
}
