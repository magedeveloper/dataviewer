<?php
namespace MageDeveloper\Dataviewer\Form\Fieldtype;

use MageDeveloper\Dataviewer\Domain\Model\Field;
use MageDeveloper\Dataviewer\Domain\Model\RecordValue;
use MageDeveloper\Dataviewer\Fluid\View\StandaloneView;
use TYPO3\CMS\Backend\Form\Container\SingleFieldContainer;

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
class Fluid extends AbstractFieldtype
{
	/**
	 * Gets a instance for a standalone view
	 * 
	 * @return StandaloneView
	 */
	protected function _getStandaloneView()
	{
		$view = $this->objectManager->get(StandaloneView::class);
		
		// TODO:
		// This needs of course a way to add custom variables to our view
		// so we later need to add a signal slot here
		
		return $view;
	} 
	 
	/**
	 * Renders a field
	 *
	 * @return array
	 */
	public function render()
	{
		$html = "";
		
		if($this->getField()->getConfig("showInBackend"))
		{
			// Show only field content in backend, when
			// the checkbox is set
			foreach($this->getFieldItems() as $_fielditem)
			{
				$fluidSource = reset($_fielditem);
				$view = $this->_getStandaloneView();
				$rendered = $view->renderSource($fluidSource);
				$html .= $rendered;
			}
		}

		return [
			'additionalJavaScriptPost' => [],
			'additionalJavaScriptSubmit' => [],
			'additionalHiddenFields' => [],
			'additionalInlineLanguageLabelFiles' => [],
			'stylesheetFiles' => [],
			'requireJsModules' => [],
			'extJSCODE' => '',
			'inlineData' => [],
			'html' => $html,
		];
	}
}
