<?php
namespace MageDeveloper\Dataviewer\ViewHelpers\Form\Checkbox;

use MageDeveloper\Dataviewer\Domain\Model\Value;

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
class IsCheckedViewHelper extends \MageDeveloper\Dataviewer\ViewHelpers\AbstractViewHelper
{
	/**
	 * Evaluates if a option is checked
	 *
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Value $value
	 * @param string $option
	 * @return bool
	 */
	public function render(Value $value, $option)
	{
		$fieldvalue = $value->getFieldvalue();
		$valueArray = $fieldvalue->getValueArray();
		
		foreach($valueArray as $_valueInfo)
		{
			$value		= $_valueInfo["value"];
			$selected	= $_valueInfo["selected"];
			
			if($value == $option)
				if($selected)
					return true;
				else
					return false;	
			
		}
		
		return false;
	}

}
