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
class NewLinkViewHelper extends AbstractViewHelper implements CompilableInterface
{
	/**
	 * @param int $pid
	 * @param string $table,
	 * @param int $datatype
	 * @return string
	 */
	public function render($pid, $table, $datatype = null)
	{
		return static::renderStatic(
			array(
				'pid' => $pid,
				'datatype' => $datatype,
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
		$urlParameters = array(
			'edit['.$arguments['table'].'][' . $arguments['pid'] . ']' => 'new',
			'returnUrl' => GeneralUtility::getIndpEnv('REQUEST_URI'),
		);
		
		if(isset($arguments["datatype"]) && !is_null($arguments["datatype"]) && is_numeric($arguments["datatype"]))
			$urlParameters["datatype"] = $arguments["datatype"];
	
		return BackendUtility::getModuleUrl(
			'record_edit',
			$urlParameters
		);
	}
}
