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
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments()
	{
		$this->registerArgument("arguments", "array", "The arguments for the template", false, []);
		$this->registerArgument("template", "string", "The template file that has to be used", true, null);
	
		parent::initializeArguments();
	}

	/**
	 * Render Method
	 *
	 * @return void
	 */
	public function render()
	{
		$template = $this->arguments["template"];
		$predefined = $this->pluginSettingsService->getPredefinedTemplateById($template);
		
		if(!is_null($predefined))
			$template = $predefined;
		
		$templateFile = GeneralUtility::getFileAbsFileName($template);
		
		if (file_exists($templateFile))
		{
			/* @var \TYPO3\CMS\Fluid\View\StandaloneView $standaloneView */
			$standaloneView = $this->objectManager->get(\TYPO3\CMS\Fluid\View\StandaloneView::class);
			$standaloneView->setTemplatePathAndFilename($templateFile);
			$standaloneView->getRequest()->setControllerExtensionName( \MageDeveloper\Dataviewer\Configuration\ExtensionConfiguration::EXTENSION_KEY );
			$standaloneView->assignMultiple($this->arguments["arguments"]);
		
			return $standaloneView->render();
		}
	
		return "Template not found!";
	}
}
