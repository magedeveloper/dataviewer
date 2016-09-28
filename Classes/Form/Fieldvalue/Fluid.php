<?php
namespace MageDeveloper\Dataviewer\Form\Fieldvalue;

use MageDeveloper\Dataviewer\Domain\Model\Field;

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
	 * Standalone View for Rendering Fluid
	 *
	 * @var \MageDeveloper\Dataviewer\Fluid\View\StandaloneView
	 * @inject
	 */
	protected $standaloneView;

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
			$rendered = $this->standaloneView->renderSource($fluidSource);
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
		return $this->getValue();
	}

	/**
	 * Gets the optimized search string for the field
	 *
	 * @return string
	 */
	public function getSearch()
	{
		return $this->getValue();
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
	 * @return mixed
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
