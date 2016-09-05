<?php
namespace MageDeveloper\Dataviewer\ViewHelpers\Request;

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
class BaseUrlViewHelper extends AbstractViewHelper
{
	/**
	 * Gets the base url setting
	 *
	 * @return string
	 */
	public function render()
	{
		return $GLOBALS['TSFE']->tmpl->setup['config.']['baseURL'];
	}

}