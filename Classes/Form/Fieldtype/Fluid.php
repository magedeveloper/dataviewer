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
	 * Standalone View
	 * 
	 * @var StandaloneView
	 */
	protected $standaloneView;

	/**
	 * Gets a instance for a standalone view
	 * 
	 * @return StandaloneView
	 */
	protected function _getStandaloneView()
	{
		if(!$this->standaloneView instanceof StandaloneView)
		{
			$this->standaloneView = $this->objectManager->get(StandaloneView::class);
		}
		
		return $this->standaloneView;
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
			$view = $this->_getStandaloneView();
			$view->assign("record", $this->getRecord());

			foreach($this->getFieldItems() as $_fielditem)
			{
				$fluidSource = reset($_fielditem);
				$rendered = $view->renderSource($fluidSource);
				$html .= $rendered;
			}
		}

		if($this->getField()->getConfig("generateOutput"))
		{
			// We add an empty field here to let this thing going to be generated in the record datahandling
			$row = $this->getDatabaseRow();
			$html .= "<input type=\"hidden\" name=\"data[tx_dataviewer_domain_model_record][{$row["uid"]}][{$this->getField()->getUid()}]\" value=\"\" />";
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
