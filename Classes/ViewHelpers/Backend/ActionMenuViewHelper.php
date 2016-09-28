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
class ActionMenuViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Be\Menus\ActionMenuViewHelper
{
	/**
	 * Render FunctionMenu
	 *
	 * @return string
	 */
	public function render($defaultController = null)
	{
		$this->tag->addAttribute("class","form-control t3-js-jumpMenuBox");
		return parent::render($defaultController);
	}
}
