<?php
namespace MageDeveloper\Dataviewer\Form\Fieldvalue;

use MageDeveloper\Dataviewer\Domain\Model\Field;
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
class Fluid extends AbstractFieldvalue implements FieldvalueInterface
{
	/**
	 * Variable Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\VariableRepository
	 * @inject
	 */
	protected $variableRepository;

	/**
	 * Gets the view model
	 *
	 * @return \MageDeveloper\Dataviewer\Fluid\View\StandaloneView
	 */
	protected function _getView()
	{
		$standaloneView = $this->objectManager->get(\MageDeveloper\Dataviewer\Fluid\View\StandaloneView::class);

		// Check for a record and inject it to the view
		if($this->getRecord() !== false)
		{
			$variableIds = GeneralUtility::trimExplode(",", $this->getField()->getConfig("injectVariables"));
			if(count($variableIds))
			{
				/* @var \MageDeveloper\Dataviewer\Controller\RecordController $controller */
				$controller = $this->objectManager->get(\MageDeveloper\Dataviewer\Controller\RecordController::class);
				$variables = $controller->prepareVariables($variableIds);
				$standaloneView->assignMultiple($variables);
			}

			$standaloneView->assign("record", $this->getRecord());
		}

		return $standaloneView;
	}

	/**
	 * Renders the source fluid code
	 *
	 * @return string
	 */
	protected function _renderSource()
	{
		$html = "";
		$items = $this->getFieldtype()->getFieldItems();
		foreach($items as $_fielditem)
		{
			$fluidSource = reset($_fielditem);
			$rendered = $this->_getView()->renderSource($fluidSource);
			$html .= $rendered;
		}

		return $html;
	}

	/**
	 * Gets the optimized value for the field
	 *
	 * @return string
	 */
	public function getValueContent()
	{
		return $this->getFrontendValue();
	}

	/**
	 * Gets the optimized search string for the field
	 *
	 * @return string
	 */
	public function getSearch()
	{
		return $this->getFrontendValue();
	}

	/**
	 * Gets the final frontend value, that is
	 * pushed in {record.field.value}
	 *
	 * This or these values are the most different
	 * part of the whole output, so if you handle
	 * this, you need to have some knowledge,
	 * what value is returned.
	 *
	 * @return string
	 */
	public function getFrontendValue()
	{
		return $this->_renderSource();
	}

	/**
	 * Gets the value or values as a plain string-array for
	 * usage in different possitions to show
	 * and use it when needed as a string
	 *
	 * @return array
	 */
	public function getValueArray()
	{
		return [$this->getFrontendValue()];
	}
}
