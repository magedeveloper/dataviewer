<?php
namespace MageDeveloper\Dataviewer\Form\FormDataProvider;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Form\FormDataProviderInterface;

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
class UnsetBackendUser implements FormDataProviderInterface
{
	/**
	 * Initializes the backend user
	 *
	 * @param array $result
	 * @return array
	 */
	public function addData(array $result)
	{
		if(TYPO3_MODE === "FE" && !$GLOBALS["BE_USER"]->user)
			unset($GLOBALS['BE_USER']);

		return $result;
	}
}
