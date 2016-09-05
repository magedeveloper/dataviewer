<?php
namespace MageDeveloper\Dataviewer\CmsLayout;

use MageDeveloper\Dataviewer\Configuration\ExtensionConfiguration;

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
abstract class AbstractCmsLayout
{
	/**
	 * The list type for this layout view
	 *
	 * @var string
	 */
	protected $listType;

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
	 * FlexForm Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\FlexFormService
	 * @inject
	 */
	protected $flexFormService;


	/**
	 * Constructor
	 *
	 * @return AbstractCmsLayout
	 */
	public function __construct()
	{
		$this->objectManager 			= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->pluginSettingsService	= $this->objectManager->get(\MageDeveloper\Dataviewer\Service\Settings\Plugin\PluginSettingsService::class);
		$this->flexFormService			= $this->objectManager->get(\MageDeveloper\Dataviewer\Service\FlexFormService::class);
	}

	/**
	 * Gets the final rendered html code for the backend plugin
	 *
	 * @param array $params
	 * @param \TYPO3\CMS\Backend\View\PageLayoutView $pObj
	 * @return string
	 */
	public function getBackendPluginLayout(array $params, \TYPO3\CMS\Backend\View\PageLayoutView $pObj)
	{
		$listType = $params["row"]["list_type"];
		
		if($listType == $this->listType)
		{
			$html = $this->getBackendLayout($listType, $params);
			if($html)
				return $html;
		}
		
		return;
	}

	/**
	 * Gets the backend layout
	 *
	 * @param string $listType
	 * @param array $config Configuration
	 * @param array $additionalVariables
	 * @return string
	 */
	public function getBackendLayout($listType, array $config, array $additionalVariables = array())
	{
		$templatePaths 	= $this->pluginSettingsService->getTemplatePaths();
		$templatePath 	= end($templatePaths);
		sort($templatePaths);
		
		for($i = count($templatePaths) - 1; $i >= 0; $i--)
		{
			$filename	  = "CmsLayout/{$listType}.html";
			$templatePath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($templatePaths[$i]);
			$templateFile = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($templatePaths[$i].$filename);

			if (is_dir($templatePath) && file_exists($templateFile))
				break;
		}
		
		$variables = array();
		if (file_exists($templateFile))
		{
			/* @var \TYPO3\CMS\Fluid\View\StandaloneView $view */
			$view = $this->objectManager->get(\TYPO3\CMS\Fluid\View\StandaloneView::class);
			$view->setTemplatePathAndFilename($templateFile);
			$view->getRequest()->setControllerExtensionName( ExtensionConfiguration::EXTENSION_KEY );
			$imageUrl 				= $this->getImageUrl("Plugins/{$listType}.gif");
			
			$variables["imageUrl"] 	= $imageUrl;

			if (isset($config["row"]) && isset($config["row"]["pi_flexform"]))
			{
				$variables["flexform"] = $this->flexFormService->convertFlexFormContentToArray($config["row"]["pi_flexform"]);
			}

			$variables = array_merge($config, $variables, $additionalVariables);

			$view->assignMultiple($variables);
			return $view->render();
		}

		return;
	}

	/**
	 * Gets the Url of the Resources folder
	 *
	 * @return string
	 */
	public function getResourcesUrl()
	{
		$extensionKey = ExtensionConfiguration::EXTENSION_KEY;
		return '../' . \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath($extensionKey) . 'Resources/';
	}

	/**
	 * Gets the extension image url
	 *
	 * @param string $filename Filename
	 * @return string
	 */
	public function getImageUrl($filename)
	{
		return $this->getResourcesUrl() . 'Public/Icons/' . $filename;
	}
}
