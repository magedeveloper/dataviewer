<?php
namespace MageDeveloper\Dataviewer\Utility;

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
class LocalizationUtility extends \TYPO3\CMS\Extbase\Utility\LocalizationUtility
{
	/**
	 * Translates
	 *
	 * @param string $key
	 * @param array $arguments
	 * @return string
	 */
	public static function translate($key, $arguments = null)
	{
		if (!is_array($arguments))
			$arguments = array($arguments);
			
		return parent::translate($key, \MageDeveloper\Dataviewer\Configuration\ExtensionConfiguration::EXTENSION_KEY, $arguments);
	}
}