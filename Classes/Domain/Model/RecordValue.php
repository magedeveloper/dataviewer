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
class RecordValue extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
	/**
	 * Value Content
	 *
	 * @var string
	 */
	protected $valueContent = '';

	/**
	 * record
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Model\Record
	 */
	protected $record = NULL;

	/**
	 * Field of the record value
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Model\Field
	 */
	protected $field = NULL;

	/**
	 * Field of the record value
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Model\FieldValue
	 */
	protected $fieldValue = NULL;

	/**
	 * Field is deleted
	 *
	 * @var boolean
	 */
	protected $deleted = FALSE;

	/**
	 * Search Var
	 *
	 * @var string
	 */
	protected $search;

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
	 * Returns the record
	 *
	 * @return \MageDeveloper\Dataviewer\Domain\Model\Record $record
	 */
	public function getRecord()
	{
		return $this->record;
	}

	/**
	 * Sets the record
	 *
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Record $record
	 * @return void
	 */
	public function setRecord(\MageDeveloper\Dataviewer\Domain\Model\Record $record)
	{
		$this->record = $record;
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
	 * Checks if the record has an uid
	 *
	 * @return bool
	 */
	public function hasUid()
	{
		return ($this->getUid() > 0);
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
	 * Gets the search var content
	 *
	 * @return string
	 */
	public function getSearch()
	{
		return $this->search;
	}

	/**
	 * Sets the search var content
	 *
	 * @param string $search
	 * @return string
	 */
	public function setSearch($search)
	{
		$this->search = $search;
	}
}
