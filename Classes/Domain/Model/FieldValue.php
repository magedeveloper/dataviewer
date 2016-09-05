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
class FieldValue extends AbstractModel
{
	/**
	 * Field Value Types
	 * 
	 * @var string
	 */
	const TYPE_FIXED_VALUE		= 0;
	const TYPE_DATABASE			= 1;
	const TYPE_TYPOSCRIPT		= 2;
	const TYPE_FIELDVALUES		= 3;

	/**
	 * Selection of Value Type
	 * 
	 * @var integer
	 * @validate NotEmpty
	 */
	protected $type = 0;

	/**
	 * Value Content
	 * 
	 * @var string
	 */
	protected $valueContent = '';

	/**
	 * Field Content
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Model\Field
	 */
	protected $fieldContent = NULL;

	/**
	 * Table Select Field
	 * 
	 * @var string
	 */
	protected $tableContent = '';

	/**
	 * Column Select Field
	 * 
	 * @var string
	 */
	protected $columnName = '';

	/**
	 * Where Clause for selection
	 * 
	 * @var string
	 */
	protected $whereClause = '';

	/**
	 * Read Only
	 * 
	 * @var boolean
	 */
	protected $isReadonly = FALSE;

	/**
	 * Is Default
	 * 
	 * @var boolean
	 */
	protected $isDefault = FALSE;

	/**
	 * Pretends to be empty
	 *
	 * @var boolean
	 */
	protected $pretendsEmpty = FALSE;

	/**
	 * Value is passed to frontend
	 *
	 * @var boolean
	 */
	protected $passToFe = false;

	/**
	 * Field of the record value
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Model\Field
	 */
	protected $field = NULL;

	/**
	 * Field is hidden
	 *
	 * @var boolean
	 */
	protected $hidden = FALSE;

	/**
	 * Returns the type
	 * 
	 * @return int $type
	 */
	public function getType() 
	{
		return $this->type;
	}

	/**
	 * Sets the type
	 * 
	 * @param integer $type
	 * @return void
	 */
	public function setType($type) 
	{
		$this->type = $type;
	}

	/**
	 * Returns the valueContent
	 * 
	 * @return string $valueContent
	 */
	public function getValueContent() 
	{
		return $this->valueContent;
	}

	/**
	 * Sets the valueContent
	 * 
	 * @param string $valueContent
	 * @return void
	 */
	public function setValueContent($valueContent) 
	{
		$this->valueContent = $valueContent;
	}

	/**
	 * Returns the field content
	 *
	 * @return \MageDeveloper\Dataviewer\Domain\Model\Field $field
	 */
	public function getFieldContent()
	{
		return $this->fieldContent;
	}

	/**
	 * Sets the field content
	 *
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Field $fieldContent
	 * @return void
	 */
	public function setFieldContent(\MageDeveloper\Dataviewer\Domain\Model\Field $fieldContent)
	{
		$this->fieldContent = $fieldContent;
	}

	/**
	 * Returns the tableContent
	 * 
	 * @return string $tableContent
	 */
	public function getTableContent() 
	{
		return $this->tableContent;
	}

	/**
	 * Sets the tableContent
	 * 
	 * @param string $tableContent
	 * @return void
	 */
	public function setTableContent($tableContent) 
	{
		$this->tableContent = $tableContent;
	}

	/**
	 * Returns the columnName
	 * 
	 * @return string $columnName
	 */
	public function getColumnName() 
	{
		return $this->columnName;
	}

	/**
	 * Sets the columnName
	 * 
	 * @param string $columnName
	 * @return void
	 */
	public function setColumnName($columnName) 
	{
		$this->columnName = $columnName;
	}

	/**
	 * Returns the whereClause
	 * 
	 * @return string $whereClause
	 */
	public function getWhereClause() 
	{
		return $this->whereClause;
	}

	/**
	 * Sets the whereClause
	 * 
	 * @param string $whereClause
	 * @return void
	 */
	public function setWhereClause($whereClause) 
	{
		$this->whereClause = $whereClause;
	}

	/**
	 * Returns the boolean state of readonly
	 * 
	 * @return boolean
	 */
	public function isReadonly() 
	{
		return $this->readonly;
	}

	/**
	 * Returns the boolean state of default
	 * 
	 * @return boolean
	 */
	public function isDefault() 
	{
		return $this->isDefault;
	}

	/**
	 * Returns the isDefault
	 * 
	 * @return boolean isDefault
	 */
	public function getIsDefault() 
	{
		return $this->isDefault;
	}

	/**
	 * Sets the isDefault
	 * 
	 * @param boolean $isDefault
	 * @return boolean isDefault
	 */
	public function setIsDefault($isDefault) 
	{
		$this->isDefault = $isDefault;
	}

	/**
	 * Returns the isReadonly
	 * 
	 * @return boolean isReadonly
	 */
	public function getIsReadonly() 
	{
		return $this->isReadonly;
	}

	/**
	 * Sets the isReadonly
	 * 
	 * @param boolean $isReadonly
	 * @return boolean isReadonly
	 */
	public function setIsReadonly($isReadonly) 
	{
		$this->isReadonly = $isReadonly;
	}

	/**
	 * Returns the field
	 *
	 * @return \MageDeveloper\Dataviewer\Domain\Model\Field $field
	 */
	public function getField()
	{
		return $this->field;
	}

	/**
	 * Sets the field
	 *
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Field $field
	 * @return void
	 */
	public function setField(\MageDeveloper\Dataviewer\Domain\Model\Field $field)
	{
		$this->field = $field;
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
	 * Gets the pretends empty status
	 * 
	 * @return boolean
	 */
	public function getPretendsEmpty()
	{
		return $this->pretendsEmpty;
	}

	/**
	 * Sets the pretends empty status
	 * 
	 * @param boolean $pretendsEmpty
	 * @return void
	 */
	public function setPretendsEmpty($pretendsEmpty)
	{
		$this->pretendsEmpty = $pretendsEmpty;
	}

	/**
	 * Get the setting for
	 * pass to frontend
	 * 
	 * @return bool
	 */
	public function getPassToFe()
	{
		return $this->passToFe;
	}

	/**
	 * Sets pass to frontend
	 * 
	 * @param bool $passToFe
	 * @return void
	 */
	public function setPassToFe($passToFe)
	{
		$this->passToFe = $passToFe;
	}

}
