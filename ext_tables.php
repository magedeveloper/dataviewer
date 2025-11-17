<?php
defined('TYPO3') or die();

$boot = static function (): void  {

    /***********************************
     * Static File Implementation
     ***********************************/
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('dataviewer', 'Configuration/TypoScript', '[Dataviewer] General Configuration');

    /***********************************
     * Additional Information in Constants
     ***********************************/
    $GLOBALS['TYPO3_CONF_VARS']['BE']['dataviewer']['additionalInformation'] = '
        <br />
        <hr style="background-color: #c0c0c0; color:#c0c0c0;" size="1" />
        <div style="float:left;">
        Name: aixdesign.net, Bastian Zagar<br />
        E-Mail: <a href="mailto:kontakt@aixdesign.net">kontakt@aixdesign.net</a><br />
        Website: <a href="https://www.aixdesign.net" target="_blank">www.aixdesign.net</a>
        </div>
        <div style="clear:both;"></div>
        <hr style="background-color: #c0c0c0; color:#c0c0c0;" size="1" />
    ';

    /***********************************
     * Backend CSS File Include
     ***********************************/
    $cssFile = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('dataviewer') . 'Resources/Public/Css/dataviewer-backend.css';
    $css = @file_get_contents($cssFile);
    $GLOBALS['TYPO3_CONF_VARS']['BE']['stylesheets'][] = $css;

    /***********************************
     * Icon Registration
     ***********************************/
    if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('dataviewer')) {
        /* @var \Aix\Dataviewer\Icon\DataviewerIconRegistry $dataviewerIconRegistry */
        $dataviewerIconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Aix\Dataviewer\Icon\DataviewerIconRegistry::class);
        // Register datatype icons to page behaviour
        $dataviewerIconRegistry->registerDatatypeIcons();
        // Register icons from dataviewer icon folder
        $dataviewerIconRegistry->registerDataviewerIcons();

        /* @var \Aix\Dataviewer\Configuration\ConfigurationRegistry $dvConfigRegistry */
        $dvConfigRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Aix\Dataviewer\Configuration\ConfigurationRegistry::class);
        // Register table orderings
        $dvConfigRegistry->registerTableOrderings();
    }

    /***********************************
     * Custom User Element for
     * Configuring the Template
     * @later!
     ***********************************/
//    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry']['templateConfigurator'] = [
//        'nodeName' => 'templateConfigurator',
//        'priority' => '1',
//        'class' => 'Aix\\Dataviewer\\Form\\Element\\TemplateConfigurator',
//    ];

};
$boot();
unset($boot);

