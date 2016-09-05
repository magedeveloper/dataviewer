<?php
namespace MageDeveloper\Dataviewer\ViewHelpers\V;

use MageDeveloper\Dataviewer\ViewHelpers\AbstractViewHelper;

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
abstract class AbstractVarViewHelper extends AbstractViewHelper
{
	/**
	 * Variable Storage Identifier
	 * @var string
	 */
	const TEMPLATE_VARIABLE_IDENTIFIER = "dataviewer_variable_storage";

	/**
	 * Abstract
	 */
	public function render()
	{
	}
}