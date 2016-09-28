<?php
namespace MageDeveloper\Dataviewer\Service\Auth;

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
class 		AuthenticationService 
extends 	\TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication
implements 	\TYPO3\CMS\Core\SingletonInterface
{
	/**
	 * Injects the salted passwords service
	 *
	 * @var \TYPO3\CMS\Saltedpasswords\SaltedPasswordService
	 * @inject
	 */
	protected $saltedPasswordService;

	/**
	 * Authenticate a user by credentials
	 *
	 * @param string $username Username
	 * @param string $password Password
	 * @return bool
	 */
	public function auth($username, $password)
	{
		//$this->tsfe = \TYPO3\CMS\Frontend\Utility\EidUtility::initFeUser();

		$loginData = [
			"uname" 		=> $username, //username
			"uident"		=> $password, //password
			"uident_text"	=> $password, //password
			"status"		=> "login"
		];

		$GLOBALS["TSFE"]->fe_user->checkPid 	= 0;
		$info 	= $GLOBALS["TSFE"]->fe_user->getAuthInfoArray();
		$user 	= $GLOBALS["TSFE"]->fe_user->fetchUserRecord( $info["db_user"] ,$loginData["uname"] );

		$ok = false;

		if (is_array($user))
		{
			// Salted Passwords?
			if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded("saltedpasswords"))
			{
				$ok = $this->saltedPasswordService->compareUident($user, $loginData);

				if (!$ok)
				{
					$user["password"] = $password;
					$ok = $this->saltedPasswordService->compareUident($user, $loginData);
				}

			}
			else
			{
				$ok = $GLOBALS["TSFE"]->fe_user->compareUident( $user, $loginData );
			}
		}

		if($ok)
		{
			$GLOBALS["TSFE"]->fe_user->createUserSession( $user );
			$GLOBALS["TSFE"]->fe_user->user = $user;

			return true;
		}

		return false;
	}

	/**
	 * Logs out the current frontend user
	 *
	 * @return bool
	 */
	public function logout()
	{
		if ($this->isLoggedIn() && $this->getFrontendUserUid())
		{
			$GLOBALS["TSFE"]->fe_user->logoff();
			return true;
		}

		return false;
	}

	/**
	 * Checks if a user is logged in
	 *
	 * @return bool
	 */
	public function isLoggedIn()
	{
		return !empty($GLOBALS["TSFE"]->fe_user->user["uid"]);
	}

	/**
	 * Gets the current logged in frontend user details
	 *
	 * @return array|null
	 */
	public function getFrontendUser()
	{
		if ($this->isLoggedIn() && !empty($GLOBALS["TSFE"]->fe_user->user["uid"]))
		{
			$fe_user = $GLOBALS["TSFE"]->fe_user->user;
			return array_change_key_case($fe_user, CASE_LOWER);
		}

		return null;
	}

	/**
	 * Get the uid of the current feuser
	 *
	 * @return int|null
	 */
	public function getFrontendUserUid()
	{
		$feUser = $this->getFrontendUser();
		
		if ($this->isLoggedIn() && isset($feUser["uid"]))
		{
			
			return intval($feUser["uid"]);
		}

		return null;
	}
	
}
