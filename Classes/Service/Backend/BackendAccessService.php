<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Service\Backend;

use Aix\Dataviewer\Domain\Model\AbstractRecordModel;
use Aix\Dataviewer\Domain\Model\Datatype;
use Aix\Dataviewer\Fluid\View\StandaloneView;
use Aix\Dataviewer\Utility\LocalizationUtility;
use Aix\Dataviewer\Utility\UrlUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

class BackendAccessService
{
    /**
     * @var BackendUserAuthentication
     */
    protected $backendUserAuthentication;

    /**
     * @param BackendUserAuthentication $backendUserAuthentication
     */
    public function injectBackendUserAuthentication(BackendUserAuthentication $backendUserAuthentication)
    {
        $this->backendUserAuthentication = $backendUserAuthentication;
    }

    /**
     * @param string $config
     * @return mixed
     */
    public function getDataviewerTSConfig(string $config)
    {
        if (array_key_exists('dataviewer.', $this->getBackendUser()->getTSConfig()['options.'])) {
            if (array_key_exists($config, $this->getBackendUser()->getTSConfig()['options.']['dataviewer.'])) {
                return $this->getBackendUser()->getTSConfig()['options.']['dataviewer.'][$config];
            }
        }
        return null;
    }

	/**
	 * Gets the dataviewer logo url and respects custom logo
	 * settings in the users TSconfig settings.
	 * @return string
	 */
	public function getLogoUrl(): string
	{
		// Default Logo
		$logo = 'EXT:dataviewer/Resources/Public/Images/logo_dataviewer.svg';

		if ($customLogo = $this->getDataviewerTSConfig('customLogo')) {
            $logo = $customLogo;
        }

		return $this->_getImageUrl($logo);
	}

    /**
     * Gets the dataviewer logo url and respects custom logo
     * settings in the users TSconfig settings.
     * @return string
     */
    public function getBrightLogoUrl(): string
    {
        // Default Logo
        $logo = 'EXT:dataviewer/Resources/Public/Images/logo_dataviewer_bright.svg';

        // Checking if a custom logo was set, so we first use this one
        if ($customLogo = $this->getDataviewerTSConfig('customLogo')) {
            $logo = $customLogo;
        }

        // Checking if a custom bright logo is set, so we prefer this one
        if ($customLogoBright = $this->getDataviewerTSConfig('customLogoBright')) {
            $logo = $customLogoBright;
        }

        return $this->_getImageUrl($logo);
    }

    /**
     * Gets a image url from a given
     * filename
     *
     * @param string $file
     * @return string
     */
    protected function _getImageUrl(string $file): string
    {
        return UrlUtility::getFileUrl($file);
    }

    /**
     * Gets the edit button for
     * an record
     * @param AbstractRecordModel $record
     * @param Datatype $datatype
     * @return string
     */
	public function getRecordEditButton(AbstractRecordModel $record, Datatype $datatype): string
    {
        $buttonHtml = '';
        if ($record->getUid() > 0) {
            $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
            //$icon = $iconFactory->getIcon('extensions-dataviewer-'.$datatype->getIcon(), Icon::SIZE_SMALL)->render();
            $icon = '';
            $editView = GeneralUtility::makeInstance(StandaloneView::class);
            $source = '
            <style type="text/css">
                .dvEditButton { position:fixed; right:15px; top:15px; z-index:99999;  }
                .dvEditButton a { opacity: .5; color:#000; background-color:#f2f2f2; color:#000; padding:5px; margin:5px; border: 1px solid #ddd; -webkit-box-shadow: 3px 3px 5px 0px rgba(0,0,0,0.34); -moz-box-shadow: 3px 3px 5px 0px rgba(0,0,0,0.34); box-shadow: 3px 3px 5px 0px rgba(0,0,0,0.34); }
                .dvEditButton a:hover { text-decoration:none; background-color:#fff; opacity: 1;}
            </style>
	        <div class="dvEditButton">
	            <a href="{dv:backend.editLink(id:'.$record->getUid().',table:\''.$datatype->getTablename().'\')}" target="_self">
	                '.$icon.' '.LocalizationUtility::translate('LLL:EXT:core/Resources/Private/Language/locallang_mod_web_list.xlf:edit').'
	            </a>
            </div>
	        ';
            $editView->setTemplateSource($source);
            $buttonHtml = $editView->render();
        }

        return $buttonHtml;
    }

    /**
     * Gets the setting from the user TSconfig to show
     * and record edit button in the frontend
     *
     * @return bool
     */
    public function showRecordEditButton(): bool
    {
        return (bool)$this->getDataviewerTSConfig('enableRecordEditButton');
    }

	/**
	 * Gets the setting from the user TSconfig to disable
	 * the dataviewer logo in the backend
	 *
	 * @return bool
	 */
	public function disableDataviewerLogo(): bool
	{
        return (bool)$this->getDataviewerTSConfig('disableDataviewerLogo');
	}

	/**
	 * Gets the dataviewer support email and resprects custom email
	 * address that is configured in the users TSconfig settings.
	 *
	 * @return string
	 */
	public function getSupportEmail(): string
	{
		return $this->getDataviewerTSConfig('customSupportEmail')??'kontakt@aixdesign.net';
	}

	/**
	 * Checks whether the user has access to this toolbar item
	 * @return bool TRUE if user has access, FALSE if not
	 */
	public function disableToolbarItem(): bool
	{
		return (bool)$this->getDataviewerTSConfig('disableDataviewerToolbarItem')??false;
	}

    /**
     * Checks for disabling a message
     * @return bool
     */
	public function disableSupportMessage(): bool
    {
        return (bool)$this->getDataviewerTSConfig('disableSupportMessage')??false;
    }

	/**
	 * Gets the storage pids of the accessible
	 * mounts
	 * @return array
	 */
	public function getAccessibleStoragePids(): array
	{
		$beUser = $this->getBackendUser();
		return $beUser->returnWebmounts();
	}

	/**
	 * Check if logged in as admin
	 * @return bool
	 */
	public function isAdmin(): bool
	{
	    $beUser = $this->getBackendUser();
	    if ($beUser) {
            return (bool)$beUser->isAdmin();
        }

	    return false;
	}

    /**
     * Gets a pages ts config by a given
     * page id
     * @param int $pageId
     * @return array
     */
    public function getPageTSConfig(int $pageId): array
    {
        $pageTSConfig = BackendUtility::getPagesTSconfig($pageId);

        if (isset($pageTSConfig['tx_dataviewer.'])) {
            $config = $pageTSConfig['tx_dataviewer.'];
            if (is_array($config)) {
                return $config;
            }
        }

        return [];
    }

    /**
     * Gets a pages docHeader Datatype Ids by
     * a given page id
     * @param int $pageId
     * @return array
     */
    public function getDocHeaderDatatypes(int $pageId): array
    {
        $config = $this->getPageTSConfig($pageId);
        $datatypeIds = [];
        if (isset($config['docHeaderDatatypes'])) {
            $datatypeIds = GeneralUtility::trimExplode(',', $config['docHeaderDatatypes']);
        }

        return $datatypeIds;
    }

    /**
     * Gets the current selected workspace id
     * @return int
     */
    public function getWorkspaceId(): int
    {
        $wsId = 0;
        //Backend
        if (!empty($GLOBALS['BE_USER']->workspace)) {
            $wsId = $GLOBALS['BE_USER']->workspace;
        } elseif (!empty($GLOBALS['TSFE']->sys_page->versioningWorkspaceId)) {
            $wsId = $GLOBALS['TSFE']->sys_page->versioningWorkspaceId;
        }

        return (int)$wsId;
    }

	/**
	 * Returns the current BE user.
	 * @return BackendUserAuthentication
	 */
	public function getBackendUser(): ?BackendUserAuthentication
	{
		return $GLOBALS['BE_USER'];
	}
}
