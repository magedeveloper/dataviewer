<?php
namespace MageDeveloper\Dataviewer\ViewHelpers\Backend;

use TYPO3\CMS\Backend\Utility\BackendUtility;
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
class JumpUrlViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper
{
	/**
	 * Creates a javascript jump url for usage in the ToolbarItem
	 *
	 * @param string $link The link to the action
	 * @param string $mod Module Name
	 * @param string $main Main Module
	 * @param int $pageId The Target Page Id
	 * @return string
	 */
	public function render($link, $mod = "web_list", $main = "web", $pageId = 0)
	{
		return 'jump(' . GeneralUtility::quoteJSvalue($link) . ',' . GeneralUtility::quoteJSvalue($mod) . ',' . GeneralUtility::quoteJSvalue($main) . ', ' . (int)$pageId . ');';
	}
}
