<?php
namespace MageDeveloper\Dataviewer\Service\Backend;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

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
class BackendAccessService
{
	/**
	 * Gets the dataviewer logo url and respects custom logo
	 * settings in the users TSconfig settings.
	 *
	 * @return string
	 */
	public function getLogoUrl()
	{
		// Default Logo
		$logo = "EXT:dataviewer/Resources/Public/Images/logo_dataviewer_text.png";

		if($customLogo = $this->_getBackendUser()->getTSConfigVal('options.dataviewer.customLogo'))
			$logo = $customLogo;

		$logo = GeneralUtility::getFileAbsFileName($logo);

		if(file_exists($logo))
		{
			$filename = basename($logo);
			$path = pathinfo($logo, PATHINFO_DIRNAME);
			$path = PathUtility::getRelativePathTo($path);

			return $path.$filename;
		}

		return "";
	}

	/**
	 * Gets the setting from the user TSconfig to disable
	 * the dataviewer logo in the backend
	 *
	 * @return bool
	 */
	public function disableDataViewerLogo()
	{
		return (bool)$this->_getBackendUser()->getTSConfigVal("options.dataviewer.disableDataViewerLogo");
	}

	/**
	 * Gets the dataviewer support email and resprects custom email
	 * address that is configured in the users TSconfig settings.
	 *
	 * @return string
	 */
	public function getSupportEmail()
	{
		$email = "kontakt@magedeveloper.de";

		if($customSupportEmail = $this->_getBackendUser()->getTSConfigVal("options.dataviewer.customSupportEmail"))
			$email = $customSupportEmail;

		return $email;
	}

	/**
	 * Checks whether the user has access to this toolbar item
	 *
	 * @return bool TRUE if user has access, FALSE if not
	 */
	public function disableToolbarItem()
	{
		$disabled = $this->_getBackendUser()->getTSConfigVal('options.dataviewer.disableDataViewerToolbarItem');

		if(is_null($disabled))
			$disabled = false;

		return (bool)!$disabled;
	}

    /**
     * Checks for disabling a message
     * 
     * @return bool
     */
	public function disableDonationMessage()
    {
        $disabled = (bool)$this->_getBackendUser()->getTSConfigVal('options.dataviewer.disableDonationMessage');
        return $disabled;
    }

	/**
	 * Gets the storage pids of the accessible
	 * mounts
	 * 
	 * @return array
	 */
	public function getAccessibleStoragePids()
	{
		$beUser = $this->_getBackendUser();
		return $beUser->returnWebmounts();
	}

	/**
	 * Check if logged in as admin
	 * 
	 * @return bool
	 */
	public function isAdmin()
	{
		return (bool)$this->_getBackendUser()->isAdmin();
	}

	/**
	 * Returns the current BE user.
	 *
	 * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
	 */
	protected function _getBackendUser()
	{
		return $GLOBALS['BE_USER'];
	}
}
