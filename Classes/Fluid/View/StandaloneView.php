<?php
namespace MageDeveloper\Dataviewer\Fluid\View;

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
class StandaloneView extends \TYPO3\CMS\Fluid\View\StandaloneView
{
	/**
	 * Plugin Settings Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Settings\Plugin\PluginSettingsService
	 * @inject
	 */
	protected $pluginSettingsService;

	/**
	 * Renders template source code by a given string
	 *
	 * @param string $source Template Source Code
	 * @return string
	 */
	public function renderSource($source)
	{
		$this->setTemplateSource($source);
		return $this->render();
	}

	/**
	 * Gets the full template path for a file
	 * 
	 * @param string $file
	 * @return string
	 */
	public function getFullTemplatePathForFile($file)
	{
		$templatePaths = $this->pluginSettingsService->getTemplatePaths();
		$templateFile = null;
		
		sort($templatePaths);

		for($i = count($templatePaths) - 1; $i >= 0; $i--)
		{
			$templatePath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($templatePaths[$i]);
			$templateFile = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($templatePaths[$i].$file);

			if (is_dir($templatePath) && file_exists($templateFile))
				break;
		}

		// if we havent found any template that matches
		if(is_null($templateFile))
			$templateFile = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName("EXT:dataviewer/Resources/Private/Templates/{$file}");

		return $templateFile;
	}
	
	/**
	 * Initializes the view with certain properties
	 * 
	 * @return void
	 */
	public function initializeView()
	{
		$templatePaths 	= $this->pluginSettingsService->getTemplatePaths();
		$partialPaths   = $this->pluginSettingsService->getPartialPaths();
		$layoutPaths    = $this->pluginSettingsService->getLayoutPaths();

		$this->setTemplateRootPaths($templatePaths);
		$this->setPartialRootPaths($partialPaths);
		$this->setLayoutRootPaths($layoutPaths);

		parent::initializeView();
	}
}
