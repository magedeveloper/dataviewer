<?php
namespace MageDeveloper\Dataviewer\ViewHelpers\Page;

use TYPO3\CMS\Backend\Utility\BackendUtility;

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
class GetViewHelper extends \MageDeveloper\Dataviewer\ViewHelpers\AbstractViewHelper
{
	/**
	 * Fetch a page by id
	 *
	 * @param int $id Id of the record to fetch
	 * @return array
	 */
	public function render($id)
	{
		$content = "";
		return BackendUtility::getRecord("pages", $id);
	}

}
