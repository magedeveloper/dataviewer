<?php
namespace MageDeveloper\Dataviewer\Form\Fieldtype;

use MageDeveloper\Dataviewer\Domain\Model\Field;
use MageDeveloper\Dataviewer\Domain\Model\RecordValue;
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
class Hidden extends AbstractFieldtype
{
	/**
	 * Renders a field
	 *
	 * @return array
	 */
	public function render()
	{
		return [
			'additionalJavaScriptPost' => [],
			'additionalJavaScriptSubmit' => [],
			'additionalHiddenFields' => [],
			'additionalInlineLanguageLabelFiles' => [],
			'stylesheetFiles' => [],
			'requireJsModules' => [],
			'extJSCODE' => '',
			'inlineData' => [],
			'html' => "",
		];
	}
}
