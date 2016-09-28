<?php
namespace MageDeveloper\Dataviewer\Form\Fieldvalue;

use MageDeveloper\Dataviewer\Domain\Model\Field;

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
class TypoScript extends AbstractFieldvalue implements FieldvalueInterface
{
	/**
	 * TypoScript Utility
	 *
	 * @var \MageDeveloper\Dataviewer\Utility\TypoScriptUtility
	 * @inject
	 */
	protected $typoScriptUtility;
	
	/**
	 * Gets rendered typoscript as single parts
	 * and returns them as an array
	 * 
	 * @return array
	 */
	protected function _getRenderedTypoScript()
	{
		$parts = [];
		$items = $this->getFieldtype()->getFieldItems();
		
		foreach($items as $_item)
		{
			$typoScript = reset($_item);
			$parts[] = $this->typoScriptUtility->getTypoScriptValue($typoScript);
		}
		
		return $parts;
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
	 * @return mixed
	 */
	public function getFrontendValue()
	{
		return $this->_getRenderedTypoScript();
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
        return [$this->getFrontendValue()];
    }
}
