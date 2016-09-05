<?php
namespace MageDeveloper\Dataviewer\Utility;

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

class ClassUtility
{
	/**
	 * Generates a class name by a table name
	 * 
	 * @param string $string
	 * @return string
	 */
	public static function underscoredToUpperCamelCaseUnderscored($string)
	{
		$upperCamelCase = str_replace(' ', '', ucwords(str_replace('_', ' ', GeneralUtility::strtolower($string))));
		$upperCamelCase = preg_replace('/(?<=\\w)(?=[A-Z])/',"_$1", $upperCamelCase);
		return $upperCamelCase;
	}
}