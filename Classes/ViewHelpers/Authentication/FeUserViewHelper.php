<?php
namespace MageDeveloper\Dataviewer\ViewHelpers\Authentication;

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
class FeUserViewHelper extends AbstractViewHelper
{
	/**
	 * authenticationService
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Auth\AuthenticationService
	 * @inject
	 */
	protected $authenticationService;

	/**
	 * Returns the current fe user if 
	 * he is logged in
	 *
	 * @return array
	 */
	public function render()
	{
		// Implementation of the current frontend user
		if ($this->authenticationService->isLoggedIn())
		{
			$feUser = $this->authenticationService->getFrontendUser();
			return $feUser;
		}

		return null;
	}
}