<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Service\Auth;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

class       AuthenticationService
extends     FrontendUserAuthentication
implements  SingletonInterface
{
    /**
     * Logs out the current frontend user
     *
     * @return bool
     * @throws AspectNotFoundException
     */
    public function logout(): bool
    {
        if ($this->isLoggedIn() && $this->getFrontendUserUid()) {
            $GLOBALS['TSFE']->fe_user->logoff();

            return true;
        }

        return false;
    }

    /**
     * Checks if a user is logged in
     *
     * @return bool
     * @throws AspectNotFoundException
     */
    public function isLoggedIn(): bool
    {
        return GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('frontend.user', 'id', 0) > 0;
    }

    /**
     * Gets the current logged in frontend user details
     *
     * @return array|null
     * @throws AspectNotFoundException
     */
    public function getFrontendUser(): ?array
    {
        if ($feUserAuth = $this->getFrontendUserAuthentication()) {
            if (is_array($feUserAuth->user) && !empty($feUserAuth->user)) {
                return array_change_key_case($feUserAuth->user, CASE_LOWER);
            }
        }

        return null;
    }

    /**
     * Gets the current logged in frontend user details
     *
     * @return FrontendUserAuthentication|null
     * @throws AspectNotFoundException
     */
    public function getFrontendUserAuthentication(): ?FrontendUserAuthentication
    {
        if ($this->isLoggedIn() && !empty($GLOBALS['TSFE']->fe_user->user['uid'])) {
            return $GLOBALS['TSFE']->fe_user;
        }

        return null;
    }

    /**
     * Get the uid of the current feuser
     *
     * @return int|null
     * @throws AspectNotFoundException
     */
    public function getFrontendUserUid(): ?int
    {
        $feUser = $this->getFrontendUser();

        if ($this->isLoggedIn() && isset($feUser['uid'])) {
            return intval($feUser['uid']);
        }

        return null;
    }

}
