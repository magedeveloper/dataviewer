<?php
namespace MageDeveloper\Dataviewer\Hooks;

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
class Constants
{
	/**
	 * Object Manager
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;


	/**
	 * Standalone View
	 * 
	 * @var \MageDeveloper\Dataviewer\Fluid\View\StandaloneView
	 * @inject
	 */
	protected $standaloneView;


	/**
	 * Constructor
	 * 
	 * @return Constants
	 */
	public function __construct()
	{
		$this->objectManager 	= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->standaloneView	= $this->objectManager->get(\MageDeveloper\Dataviewer\Fluid\View\StandaloneView::class);
	}

	/**
	 * Displays the information field in the constants editor
	 *
	 * @param array $config
	 * @param \TYPO3\CMS\Core\TypoScript\ExtendedTemplateService $pObj
	 * @return string
	 */
	public function displayExtensionInformation($config, $pObj)
	{
		$config["fieldName"] 	= "";
		$config["fieldValue"]	= "";

		$_EXTKEY = "dataviewer";
		$EM_CONF = null;
		$emConf = "EXT:dataviewer/ext_emconf.php";
		$emConf = GeneralUtility::getFileAbsFileName($emConf);
		require_once($emConf);
		
		if(!is_array($EM_CONF[$_EXTKEY]))
			die("Error loading extension information");
		
		$templateFile = "EXT:dataviewer/Resources/Private/Templates/Constants/ExtInformation.html";
		$templateFile = GeneralUtility::getFileAbsFileName($templateFile);
		$this->standaloneView->setTemplatePathAndFilename($templateFile);
		
		$this->standaloneView->assign("config", $config);
		$this->standaloneView->assign("emConf", $EM_CONF[$_EXTKEY]);
		return $this->standaloneView->render();
	}

}
