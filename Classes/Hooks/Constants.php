<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Hooks;

use Aix\Dataviewer\Fluid\View\StandaloneView;
use Aix\Dataviewer\Icon\DataviewerIconRegistry;
use Aix\Dataviewer\Service\Backend\BackendAccessService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Constants
{
    /**
     * @var StandaloneView
     */
    protected $standaloneView;

    /**
     * @var DataviewerIconRegistry
     */
    protected $dataviewerIconRegistry;

    /**
     * @var BackendAccessService
     */
    protected $backendAccessService;

    /**
     * @param StandaloneView $standaloneView
     */
    public function injectStandaloneView(StandaloneView $standaloneView)
    {
        $this->standaloneView = $standaloneView;
    }

    /**
     * @param DataviewerIconRegistry $dataviewerIconRegistry
     */
    public function injectDataviewerIconRegistry(DataviewerIconRegistry $dataviewerIconRegistry)
    {
        $this->dataviewerIconRegistry = $dataviewerIconRegistry;
    }

    /**
     * @param BackendAccessService $backendAccessService
     */
    public function injectBackendAccessService(BackendAccessService $backendAccessService)
    {
        $this->backendAccessService = $backendAccessService;
    }

    /**
     * Displays the information field in the constants editor
     * @param array $config
     * @param ExtendedTemplateService $extTs
     * @return string
     */
    public function displayExtensionInformation(array $config): string
    {
        $config['fieldName']  = '';
        $config['fieldValue'] = '';

        $_EXTKEY = 'dataviewer';
        $EM_CONF = null;
        $emConf  = 'EXT:dataviewer/ext_emconf.php';
        $emConfPro = 'EXT:dataviewer_pro/ext_emconf.php';
        $emConf = GeneralUtility::getFileAbsFileName($emConf);
        $emConfPro = GeneralUtility::getFileAbsFileName($emConfPro);

        // Check for pro extension existence and use that instead
        if(file_exists($emConfPro)) {
            $emConf = $emConfPro;
        }

        require_once($emConf);

        if (!is_array($EM_CONF[$_EXTKEY])) {
            return '<div class="alert alert-danger" style="padding:10px;">Error loading extension information!</div>';
        }

        $icons = $this->dataviewerIconRegistry->getIcons();

        // Prepare icon path
        foreach ($icons as $_iconId => $_iconPath) {
            $pos               = strpos($_iconPath, 'ext/dataviewer/');
            if($pos !== false) {
                $correctedIconPath = substr($_iconPath, $pos);
                $correctedIconPath = str_replace('ext/dataviewer/', 'EXT:dataviewer/', $correctedIconPath);
                $icons[$_iconId]   = $correctedIconPath;
            }
        }

        $templateFile = 'EXT:dataviewer/Resources/Private/Templates/Backend/Constants/ExtInformation.html';
        $templateFile = GeneralUtility::getFileAbsFileName($templateFile);
        $this->standaloneView->setTemplatePathAndFilename($templateFile);

        $additionalInformation = $GLOBALS['TYPO3_CONF_VARS']['BE']['dataviewer']['additionalInformation'] ?? $GLOBALS['TYPO3_CONF_VARS']['BE']['dataviewer']['additionalInformation'];

        $this->standaloneView->assign('additionalInformation', $additionalInformation);
        $this->standaloneView->assign('config', $config);
        $this->standaloneView->assign('emConf', $EM_CONF[$_EXTKEY]);
        $this->standaloneView->assign('icons', $icons);
        $this->standaloneView->assign('logoUrl', $this->backendAccessService->getLogoUrl());
        $this->standaloneView->assign('supportMessage', $this->backendAccessService->disableSupportMessage());

        return $this->standaloneView->render();
    }

}
