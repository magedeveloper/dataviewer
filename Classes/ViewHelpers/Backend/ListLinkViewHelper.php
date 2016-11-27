<?php
namespace MageDeveloper\Dataviewer\ViewHelpers\Backend;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;

/**
 * ViewHelper to create a link to the list module
 * @internal
 */
class ListLinkViewHelper extends AbstractViewHelper implements CompilableInterface
{
	/**
	 * @param int $id
	 * @return string
	 */
	public function render($id)
	{
		return static::renderStatic(
			[
				'id' => $id,
			],
			$this->buildRenderChildrenClosure(),
			$this->renderingContext
		);
	}

	/**
	 * @param array $arguments
	 * @param \Closure $renderChildrenClosure
	 * @param RenderingContextInterface $renderingContext
	 *
	 * @return string
	 */
	public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
	{

		return BackendUtility::getModuleUrl("web_list", ["id" => $arguments['id']]);
	}
}
