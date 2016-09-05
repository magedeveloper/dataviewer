<?php
namespace MageDeveloper\Dataviewer\ViewHelpers\Backend;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;

/**
 * ViewHelper to create a link to edit a note
 * @internal
 */
class EditLinkViewHelper extends AbstractViewHelper implements CompilableInterface
{
	/**
	 * @param int $id
	 * @param string $table
	 * @return string
	 */
	public function render($id, $table)
	{
		return static::renderStatic(
			array(
				'id' => $id,
				'table' => $table,
			),
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
		return BackendUtility::getModuleUrl(
			'record_edit',
			array(
				'edit['.$arguments['table'].'][' . $arguments['id'] . ']' => 'edit',
				'returnUrl' => GeneralUtility::getIndpEnv('REQUEST_URI')
			)
		);
	}
}
