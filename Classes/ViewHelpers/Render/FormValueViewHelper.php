<?php
namespace MageDeveloper\Dataviewer\ViewHelpers\Render;

use MageDeveloper\Dataviewer\Configuration\ExtensionConfiguration as Configuration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * MageDeveloper Dataviewer Extension
 * -----------------------------------
 *
 * @category    TYPO3 Extension
 * @package     MageDeveloper\Dataviewer
 * @author		Bastian Zagar

 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class FormValueViewHelper extends \MageDeveloper\Dataviewer\ViewHelpers\AbstractViewHelper
{
	/**
	 * Renders a formValue
	 * 
	 * @param \MageDeveloper\Dataviewer\Domain\Model\FormValue $formValue
	 * @return mixed
	 */
	public function render(\MageDeveloper\Dataviewer\Domain\Model\FormValue $formValue)
	{
		$templateFile = $this->pluginSettingsService->getTemplatePath() . "Index/Part.html";
		$templateFile = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($templateFile);

		/* @var \TYPO3\CMS\Fluid\View\StandaloneView $view */
		$view = $this->objectManager->get(\TYPO3\CMS\Fluid\View\StandaloneView::class);
		$view->setLayoutRootPaths(array($this->pluginSettingsService->getLayoutPath()));
		$view->setPartialRootPaths(array($this->pluginSettingsService->getPartialPath()));
		$view->setTemplatePathAndFilename($templateFile);
		$view->getRequest()->setControllerExtensionName( Configuration::EXTENSION_KEY );

		$view->assign("formValue", $formValue);

		return $view->render();
	}
}