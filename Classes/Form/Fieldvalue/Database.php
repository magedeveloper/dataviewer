<?php
namespace MageDeveloper\Dataviewer\Form\Fieldvalue;

use MageDeveloper\Dataviewer\Domain\Model\Field;
use MageDeveloper\Dataviewer\Domain\Model\FieldValue;

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
class Database extends AbstractFieldvalue implements FieldvalueInterface
{
	/**
	 * Field Repository
	 * 
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\FieldRepository
	 * @inject
	 */
	protected $fieldRepository;

	/**
	 * Gets a solved field value
	 * 
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Field $field
	 * @return array
	 */
	public function getDatabaseItemsFromField(Field $field)
	{
		$values = [];
		$fieldValues = $field->getFieldValues();

		if($field->hasDatabaseValues())
		{
			/* @var \MageDeveloper\Dataviewer\Domain\Model\FieldValue $_fieldValue */
			foreach($fieldValues as $_fieldValue)
			{
				if($_fieldValue->getType() == FieldValue::TYPE_DATABASE)
				{
					$items = $this->fieldRepository->findEntriesForFieldValue($_fieldValue);
					$values = array_merge($values, $items);
				}
			}

		}
		
		return $values;
	}

	/**
	 * Gets the optimized value for the field
	 *
	 * @return string
	 */
	public function getValueContent()
	{
		return $this->getValue();
	}

	/**
	 * Gets the optimized search string for the field
	 *
	 * @return string
	 */
	public function getSearch()
	{
		return $this->getValue();
	}

	/**
	 * Gets the final frontend value, that is
	 * pushed in {record.field.value}
	 *
	 * This or these values are the most different
	 * part of the whole output, so if you handle
	 * this, you need to have some knowledge,
	 * what value is returned.
	 *
	 * @return array
	 */
	public function getFrontendValue()
	{
		$field = $this->getField();
		return $this->getDatabaseItemsFromField($field);
	}

	/**
	 * Gets the value or values as a plain string-array for
	 * usage in different possitions to show
	 * and use it when needed as a string
	 *
	 * @return array
	 */
	public function getValueArray()
	{
		$values = $this->getFrontendValue();
		$valueArr = [];
		
		foreach($values as $_value)
			$valueArr[] = reset($_value);
		
		return $valueArr;
	}
}
