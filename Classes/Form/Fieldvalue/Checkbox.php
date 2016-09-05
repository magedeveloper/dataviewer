<?php
namespace MageDeveloper\Dataviewer\Form\Fieldvalue;

use MageDeveloper\Dataviewer\Domain\Model\Field;
use MageDeveloper\Dataviewer\Utility\CheckboxUtility;
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
class Checkbox extends AbstractFieldvalue implements FieldvalueInterface
{
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
	 * Gets all selected items
	 * 
	 * @param int $value
	 * @return array
	 */
	protected function _getSelectedItems($value)
	{
		// We need to get all checkboxes first
		$items = $this->getItems();

		$selectedIds = CheckboxUtility::getSelectedIds($value);
		$keys = array_filter($selectedIds);
		$values = array_intersect_key($items, $keys);
		
		return $values;
	}

	/**
	 * Gets all available items
	 * 
	 * @return array
	 */
	public function getItems()
	{
		$items = $this->getFieldtype()->getFieldItems();
		foreach($items as $i=>$_fi)
			$items[$i] = reset($_fi);
			
		return $items;	
	}

	/**
	 * Gets the optimized search string for the field
	 *
	 * @return string
	 */
	public function getSearch()
	{
		$value = $this->getValue();
		$values = $this->_getSelectedItems($value);

		$checkboxStringValue = "";
		foreach($values as $_value)
			$checkboxStringValue .= $_value.",";

		return trim($checkboxStringValue, ",");
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
		$value = $this->getValue();
		return $this->_getSelectedItems($value);
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
        return $this->getFrontendValue();
    }
}
