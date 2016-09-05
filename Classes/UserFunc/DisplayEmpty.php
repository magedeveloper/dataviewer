<?php
namespace MageDeveloper\Dataviewer\UserFunc;

use MageDeveloper\Dataviewer\Utility\LocalizationUtility;

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
class DisplayEmpty
{
	/**
	 * Just display nothing :)
	 *
	 * @param array $config Configuration Array
	 * @param array $parentObject Parent Object
	 * @return array
	 */
	public function displayNothing(array &$config, &$parentObject)
	{
		return "";
	}

	/**
	 * Just display nothing :)
	 *
	 * @param array $config Configuration Array
	 * @param array $parentObject Parent Object
	 * @return array
	 */
	public function displayNoConfigurationMessage(array &$config, &$parentObject)
	{
		$message = LocalizationUtility::translate("message.this_field_has_no_configuration");
		return "<div class=\"message message-alert\">{$message}</div>";
	}

	/**
	 * Just display nothing :)
	 *
	 * @param array $config Configuration Array
	 * @param array $parentObject Parent Object
	 * @return array
	 */
	public function displayErrorText(array &$config, &$parentObject)
	{
		$message = "Error @ {$config["itemFormElName"]}";
	
		$parameters = $config["parameters"];
		if(isset($parameters["message"]))
			$message = $parameters["message"];
			
		return "<div class=\"alert alert-danger\">{$message}</div>";
	}
}
