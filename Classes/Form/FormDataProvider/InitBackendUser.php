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
class InitBackendUser implements FormDataProviderInterface
{
	/**
	 * Initializes the backend user
	 *
	 * @param array $result
	 * @return array
	 */
	public function addData(array $result)
	{
		if(!$GLOBALS["BE_USER"])
		{
			/** @var $backendUser \TYPO3\CMS\Core\Authentication\BackendUserAuthentication */
			$backendUser = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Authentication\BackendUserAuthentication::class);
			// The global must be available very early, because methods below
			// might trigger code which relies on it. See: #45625
			$GLOBALS['BE_USER'] = $backendUser;
			$backendUser->start();
		}

		return $result;
	}
}
