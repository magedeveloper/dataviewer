<?php
namespace MageDeveloper\Dataviewer\ViewHelpers\Array;

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
class KeyValueViewHelper extends \MageDeveloper\Dataviewer\ViewHelpers\AbstractViewHelper
{
	/**
	 * Creates a clean option array by given
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return array
	 */
	public function render($key, $value = "")
	{
		return [$key=>$value];
	}

}
