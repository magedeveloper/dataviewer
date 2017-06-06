<?php
namespace MageDeveloper\Dataviewer\UserFunc;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use MageDeveloper\Dataviewer\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

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
	 * Object Manager
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Backend Access Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Backend\BackendAccessService
	 * @inject
	 */
	protected $backendAccessService;

	/**
	 * Constructor
	 *
	 * @return Logo
	 */
	public function __construct()
	{
		$this->objectManager 			= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->backendAccessService	= $this->objectManager->get(\MageDeveloper\Dataviewer\Service\Backend\BackendAccessService::class);
	}

	/**
	 * Displays the dataviewer logo in a field
	 *
	 * @param array $config
	 * @param array $parentObject
	 * @return string
	 */
	public function displayLogoText(array &$config, &$parentObject)
	{
		if($this->backendAccessService->disableDataViewerLogo())
			return;
	
		$logoUrl = $this->backendAccessService->getLogoUrl();
		$supportEmail = $this->backendAccessService->getSupportEmail();

		$version = ExtensionManagementUtility::getExtensionVersion("dataviewer");

		$html = "";
		$html .= "<img src=\"{$logoUrl}\" border=\"0\" alt=\"DataViewer\" title=\"DataViewer {$version}\" />";
		$html .= "<div style=\"margin-top:10px;\">Version <strong>{$version}</strong>&nbsp;| Mail:&nbsp;<a href=\"mailto:{$supportEmail}\">{$supportEmail}</a></div>";

        if(!$this->backendAccessService->disableDonationMessage())
        {
            $html .= "<small>"
                . LocalizationUtility::translate("donate.if_you_like")
                ."&nbsp;"
                ."<a style=\"color:orange; font-weight:bold; display:inline;\" href=\"https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=HQP7AJZXJEWMQ&item_name=Support%20for%20Extension%20Development%20DataViewer\" target=\"_blank\">"
                . LocalizationUtility::translate("donate.please_donate")
                . "</a>!"
                . "</small>"
            ;
        }

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
		$parameters = $config["parameters"];

		$html = "";
		$html .= $this->displayLogoText($config, $parentObject);

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
		
		if(!isset($row["pages"]) && $row["pages"] != "")
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
		if($this->backendAccessService->disableDataViewerLogo())
			return;
	
		$html = "";
		$path = GeneralUtility::getFileAbsFileName("EXT:dataviewer/Resources/Public/Images/logo_dataviewer.png");
		$logoUrl = PathUtility::getAbsoluteWebPath($path);
		
		$html .= "<img src=\"{$logoUrl}\" border=\"0\" alt=\"DataViewer\" title=\"MageDeveloper DataViewer\" />";
		return $html;
	}


}
