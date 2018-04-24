<?php
namespace MageDeveloper\Dataviewer\ViewHelpers\Render;

use MageDeveloper\Dataviewer\Utility\DebugUtility;
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
class TemplateViewHelper extends \MageDeveloper\Dataviewer\ViewHelpers\AbstractViewHelper
{
    /**
     * Cache Manager
     *
     * @var \TYPO3\CMS\Core\Cache\CacheManager
     * @inject
     */
    protected $cacheManager;

	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments()
	{
		$this->registerArgument("arguments", "array", "The arguments for the template", false, []);
		$this->registerArgument("template", "string", "The template file that has to be used", true, null);
	    $this->registerArgument("cache", "bool", "Enables or disables cache", false, false);
	    $this->registerArgument("lifetime", "int", "Cache Lifetime", false, null);
	    $this->registerArgument("cacheIdentifier", "string","Cache Identifier", false, null);

		parent::initializeArguments();
	}

    /**
     * Render Method
     *
     * @return string
     */
	public function render()
	{
		$template = $this->arguments["template"];
		$predefined = $this->pluginSettingsService->getPredefinedTemplateById($template);
		
		if(!is_null($predefined))
			$template = $predefined;

        $cache = $this->cacheManager->getCache("cache_hash");
        if($this->hasArgument("cacheIdentifier")) {
            $cacheIdentifier = $this->arguments["cacheIdentifier"];
        } else {
            $cacheIdentifier = md5(json_encode($this->arguments["arguments"]).$template);
        }

        if( ($this->arguments["cache"] == true || $this->arguments["cacheIdentifier"]) && $cache->has($cacheIdentifier)) {
            // We try to load the output from the cache
            return $cache->get($cacheIdentifier);
        }

		$templateFile = GeneralUtility::getFileAbsFileName($template);
		
		if (file_exists($templateFile))
		{
			/* @var \TYPO3\CMS\Fluid\View\StandaloneView $standaloneView */
			$standaloneView = $this->objectManager->get(\TYPO3\CMS\Fluid\View\StandaloneView::class);
			$standaloneView->setTemplatePathAndFilename($templateFile);
			$standaloneView->getRequest()->setControllerExtensionName( \MageDeveloper\Dataviewer\Configuration\ExtensionConfiguration::EXTENSION_KEY );
			$standaloneView->assignMultiple($this->arguments["arguments"]);
			$standaloneView->assign("cacheIdentifier", $cacheIdentifier);

			$output = $standaloneView->render();

            $lifetime = $this->pluginSettingsService->getConfiguration("developer.cache_lifetime");
            if($this->hasArgument("lifetime")) {
                $lifetime = (int)$this->arguments["lifetime"];
            }

			$cache->set($cacheIdentifier, $output, [], $lifetime);
			return $output;
		}
	
		return "Template not found!";
	}
}
