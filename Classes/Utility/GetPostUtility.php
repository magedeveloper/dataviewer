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

class GetPostUtility
{
	/**
	 * Secures a GET variable
	 *
	 * @param string $variable
	 * @return string
	 */
	public static function secureVariableGet($variable)
	{
		$variable = htmlspecialchars($variable);
		$variable = strip_tags($variable);

		return $variable;
	}

	/**
	 * Secures a POST variable
	 *
	 * @param string $variable
	 * @return string
	 */
	public static function secureVariablePost($variable)
	{
		$variable = htmlspecialchars($variable);
		$variable = strip_tags($variable);

		return $variable;
	}
}
