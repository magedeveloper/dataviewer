<?php
namespace MageDeveloper\Dataviewer\UserFunc;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use MageDeveloper\Dataviewer\Utility\LocalizationUtility;
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
class Logo
{
	/**
	 * Displays the dataviewer logo in a field
	 *
	 * @param array $config
	 * @param array $parentObject
	 * @return string
	 */
	public function displayLogoText(array &$config, &$parentObject)
	{
		$html = "";
		$logoUrl = ExtensionManagementUtility::extRelPath("dataviewer") . "Resources/Public/Images/logo_dataviewer_text.png";
		$html .= "<img src=\"{$logoUrl}\" border=\"0\" alt=\"DataViewer\" title=\"MageDeveloper DataViewer\" class=\"dataviewer-logo\" />";
		return $html;
	}

	/**
	 * Displays the dataviewer logo in a field and a message
	 *
	 * @param array $config
	 * @param array $parentObject
	 * @return string
	 */
	public function displayLogoAndMessage(array &$config, &$parentObject)
	{
		$html = "";
		$html .= $this->displayLogoText($config, $parentObject);

		$parameters = $config["parameters"];
		if(isset($parameters["message"])) 
		{
			$severity = (isset($parameters["severity"]))?$parameters["severity"]:"info";
			$html .= "<div class=\"alert alert-{$severity}\" style=\"margin-top:25px;\">{$parameters["message"]}</div>";
		}
		return $html;
	}

	/**
	 * Display an information message, when no record
	 * storage pages were selected
	 *
	 * @param array $config Configuration Array
	 * @param array $parentObject Parent Object
	 * @return array
	 */
	public function displayLogoAndMessageOnEmptyRecordStoragePage(array &$config, &$parentObject)
	{
		$row = $config["row"];

		if($row["pages"] == "")
		{
			$message = LocalizationUtility::translate("message.no_record_storage_page_configured");
			$config["parameters"]["message"] = $message;
			$config["parameters"]["severity"] = "warning";
			return $this->displayLogoAndMessage($config, $parentObject);
		}
		
		return $this->displayLogoText($config, $parentObject);
	}

	/**
	 * Displays the dataviewer logo in a field
	 *
	 * @param array $config
	 * @param array $parentObject
	 * @return string
	 */
	public function displayLogo(array &$config, &$parentObject)
	{
		$html = "";
		$logoUrl = ExtensionManagementUtility::extRelPath("dataviewer") . "Resources/Public/Images/logo_dataviewer.png";
		$html .= "<img src=\"{$logoUrl}\" border=\"0\" alt=\"DataViewer\" title=\"MageDeveloper DataViewer\" />";
		return $html;
	}
}
