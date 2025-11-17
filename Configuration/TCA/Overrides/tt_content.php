<?php
defined('TYPO3') or die();

call_user_func(
    function () {

        // Add 'dataviewer' group to CType dropdown
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItemGroup('tt_content', 'CType','dataviewer', 'LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:tx_dataviewer.plugins');

        /***********************************
         * Plugin - List
         ***********************************/
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Dataviewer',
            'List',
            'LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.list',
            'dataviewer-icon-dataviewer-list',
            'dataviewer'
        );

        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['dataviewer_list'] = 'layout,select_key,recursive';
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['dataviewer_list']     = 'pi_flexform';

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
            'dataviewer_list',
            'FILE:EXT:dataviewer/Configuration/FlexForms/Plugins/List.xml'
        );

        /***********************************
         * Plugin - Detail
         ***********************************/
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Dataviewer',
            'Detail',
            'LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.detail',
            'dataviewer-icon-dataviewer-detail',
            'dataviewer'
        );

        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['dataviewer_detail'] = 'layout,select_key,recursive';
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['dataviewer_detail']     = 'pi_flexform';

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
            'dataviewer_detail',
            'FILE:EXT:dataviewer/Configuration/FlexForms/Plugins/Detail.xml'
        );

        /***********************************
         * Plugin - Dynamic
         ***********************************/
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Dataviewer',
            'Dynamic',
            'LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.dynamic',
            'dataviewer-icon-dataviewer-dynamic',
            'dataviewer'
        );

        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['dataviewer_dynamic'] = 'layout,select_key,recursive';
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['dataviewer_dynamic']     = 'pi_flexform';

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
            'dataviewer_dynamic',
            'FILE:EXT:dataviewer/Configuration/FlexForms/Plugins/Dynamic.xml'
        );

        /***********************************
         * Plugin - Plain
         ***********************************/
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Dataviewer',
            'Plain',
            'LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.plain',
            'dataviewer-icon-dataviewer-plain',
            'dataviewer'
        );

        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['dataviewer_plain'] = 'layout,select_key,recursive';
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['dataviewer_plain']     = 'pi_flexform';

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
            'dataviewer_plain',
            'FILE:EXT:dataviewer/Configuration/FlexForms/Plugins/Plain.xml'
        );
    }
);
