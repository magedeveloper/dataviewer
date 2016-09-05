<?php
namespace MageDeveloper\Dataviewer\UserFunc;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
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
