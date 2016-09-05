<?php
namespace MageDeveloper\Dataviewer\ViewHelpers\String;

use MageDeveloper\Dataviewer\ViewHelpers\AbstractViewHelper;
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
class ExplodeViewHelper extends AbstractViewHelper
{
	/**
	 * Creates an code from a string
	 *
	 * @param string $delimeter
	 * @param string $string String to explode
	 * @param bool $removeEmptyValues Remove empty values
	 * @return string
	 */
	public function render($delimeter = ",", $string, $removeEmptyValues = false)
	{
		return GeneralUtility::trimExplode($delimeter, $string, $removeEmptyValues);
	}
}
