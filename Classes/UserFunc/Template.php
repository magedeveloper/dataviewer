<?php
namespace MageDeveloper\Dataviewer\UserFunc;

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
class Template
{
	/**
	 * Object Manager
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Plugin Settings Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Settings\Plugin\PluginSettingsService
	 * @inject
	 */
	protected $pluginSettingsService;

	/**
	 * Constructor
	 *
	 * @return Letter
	 */
	public function __construct()
	{
		$this->objectManager 			= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->pluginSettingsService	= $this->objectManager->get(\MageDeveloper\Dataviewer\Service\Settings\Plugin\PluginSettingsService::class);
	}

	/**
	 * Populate flexform predefined templates
	 *
	 * @param array $config Configuration Array
	 * @param array $parentObject Parent Object
	 * @return array
	 */
	public function populateTemplates(array &$config, &$parentObject)
	{
		$configuration = $this->pluginSettingsService->getPredefinedTemplates();
		$options = [];

		if(is_array($configuration))
		{
			$options[] = [LocalizationUtility::translate("flexform.predefined_templates"), "--div--"];
			foreach($configuration as $_template=>$_file)
			{
				$filePath = GeneralUtility::getFileAbsFileName($_file);
				if(file_exists($filePath))
				{
					$id = $_template;
					$options[] = [$id, $id];
				}
				
			}	
		}

		$config["items"] = array_merge($config["items"], $options);
	}

}
