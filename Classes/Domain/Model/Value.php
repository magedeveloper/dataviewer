<?php
namespace MageDeveloper\Dataviewer\Domain\Model;

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
class Value
{
	/**
	 * Field
	 * 
	 * @var \MageDeveloper\Dataviewer\Domain\Model\Field
	 */
	protected $field;

	/**
	 * Record Value
	 * 
	 * @var \MageDeveloper\Dataviewer\Domain\Model\RecordValue
	 */
	protected $recordValue;

	/**
	 * Fieldvalue
	 * 
	 * @var \MageDeveloper\Dataviewer\Form\Fieldvalue\FieldvalueInterface
	 */
	protected $fieldvalue;

	/**
	 * Gets the field model
	 * 
	 * @return \MageDeveloper\Dataviewer\Domain\Model\Field
	 */
	public function getField()
	{
		return $this->field;
	}

	/**
	 * Gets the according record
	 * 
	 * @return \MageDeveloper\Dataviewer\Domain\Model\Record
	 */
	public function getRecord()
	{
		return $this->getRecordValue()->getRecord();
	}

	/**
	 * Sets the field model
	 * 
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Field $field
	 * @return void
	 */
	public function setField(\MageDeveloper\Dataviewer\Domain\Model\Field &$field)
	{
		$this->field = $field;
	}

	/**
	 * Gets the record value
	 * 
	 * @return \MageDeveloper\Dataviewer\Domain\Model\RecordValue
	 */
	public function getRecordValue()
	{
		return $this->recordValue;
	}

	/**
	 * Sets the record value
	 * 
	 * @param \MageDeveloper\Dataviewer\Domain\Model\RecordValue $recordValue
	 * @return void
	 */
	public function setRecordValue(\MageDeveloper\Dataviewer\Domain\Model\RecordValue &$recordValue)
	{
		$this->recordValue = $recordValue;
	}

	/**
	 * Gets the fieldvalue
	 * 
	 * @return \MageDeveloper\Dataviewer\Form\Fieldvalue\FieldvalueInterface
	 */
	public function getFieldvalue()
	{
		return $this->fieldvalue;
	}

	/**
	 * Sets the fieldvalue
	 * 
	 * @param \MageDeveloper\Dataviewer\Form\Fieldvalue\FieldvalueInterface $fieldvalue
	 * @return void
	 */
	public function setFieldvalue($fieldvalue)
	{
		$this->fieldvalue = $fieldvalue;
	}

	/**
	 * Gets the fieldtype
	 * 
	 * @return mixed
	 */
	public function getFieldtype()
	{
		return	$this->getFieldvalue()->getFieldtype();
	}
	
	/**
	 * Gets the final value for the frontend output
	 * 
	 * @return string
	 */
	public function getValue()
	{
		if($this->getFieldvalue())
			return $this->getFieldvalue()->getFrontendValue();
	}

	/**
	 * Gets the final value as an array for the
	 * frontend output
	 * 
	 * @return array
	 */
	public function getValueArray()
	{
		if($this->getFieldvalue())
			return $this->getFieldvalue()->getValueArray();
	}
}
