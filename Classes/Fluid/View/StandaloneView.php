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
	 * Renders template source code by a given string
	 *
	 * @param string $source Template Source Code
	 * @return string
	 */
	public function renderSource($source)
	{
		$this->setTemplateSource($source);
		return $this->render();
	
	
		$this->baseRenderingContext->setControllerContext($this->controllerContext);
		$this->templateParser->setConfiguration($this->buildParserConfiguration());

		$parsedTemplate = $this->templateParser->parse($source);

		if ($parsedTemplate->hasLayout())
		{
			$layoutName = $parsedTemplate->getLayoutName($this->baseRenderingContext);
			$layoutIdentifier = $this->getLayoutIdentifier($layoutName);
			if ($this->templateCompiler->has($layoutIdentifier)) {
				$parsedLayout = $this->templateCompiler->get($layoutIdentifier);
			} else {
				$parsedLayout = $this->templateParser->parse($this->getLayoutSource($layoutName));
				if ($parsedLayout->isCompilable()) {
					$this->templateCompiler->store($layoutIdentifier, $parsedLayout);
				}
			}
			$this->startRendering(self::RENDERING_LAYOUT, $parsedTemplate, $this->baseRenderingContext);
			$output = $parsedLayout->render($this->baseRenderingContext);
			$this->stopRendering();
		}
		else
		{
			$this->startRendering(self::RENDERING_TEMPLATE, $parsedTemplate, $this->baseRenderingContext);
			$output = $parsedTemplate->render($this->baseRenderingContext);
			$this->stopRendering();
		}

		return $output;
	}
}
