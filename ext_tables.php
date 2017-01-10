<?php
if (!defined("TYPO3_MODE")) {
	die ("Access denied.");
}

$extensionName = \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($_EXTKEY);

/***********************************
 * Register Icons
 ***********************************/
\MageDeveloper\Dataviewer\Utility\IconUtility::registerIcons();

/***********************************
 * Static File Implementation
 ***********************************/
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, "Configuration/TypoScript", "DataViewer Extension");

/***********************************
 * Table Configuration "Datatype"
 ***********************************/
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr("tx_dataviewer_domain_model_datatype", "EXT:dataviewer/Resources/Private/Language/locallang_csh_tx_dataviewer_domain_model_datatype.xlf");
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages("tx_dataviewer_domain_model_datatype");

/***********************************
 * Table Configuration "Field"
 ***********************************/
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr("tx_dataviewer_domain_model_field", "EXT:dataviewer/Resources/Private/Language/locallang_csh_tx_dataviewer_domain_model_field.xlf");
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages("tx_dataviewer_domain_model_field");

/***********************************
 * Table Configuration "FieldValue"
 ***********************************/
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr("tx_dataviewer_domain_model_fieldvalue", "EXT:dataviewer/Resources/Private/Language/locallang_csh_tx_dataviewer_domain_model_fieldvalue.xlf");
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages("tx_dataviewer_domain_model_fieldvalue");

/***********************************
 * Table Configuration "Record"
 ***********************************/
//\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr("tx_dataviewer_domain_model_record", "EXT:dataviewer/Resources/Private/Language/locallang_csh_tx_dataviewer_domain_model_record.xlf");
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages("tx_dataviewer_domain_model_record");

/***********************************
 * Table Configuration "RecordValue"
 ***********************************/
//\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr("tx_dataviewer_domain_model_recordvalue", "EXT:dataviewer/Resources/Private/Language/locallang_csh_tx_dataviewer_domain_model_recordvalue.xlf");

/***********************************
 * Table Configuration "Variable"
 ***********************************/
//\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr("tx_dataviewer_domain_model_variable", "EXT:dataviewer/Resources/Private/Language/locallang_csh_tx_dataviewer_domain_model_variable.xlf");
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages("tx_dataviewer_domain_model_variable");

/***********************************
 * Plugin - Display Records
 ***********************************/
$pluginSigPi1 = strtolower($extensionName) . "_record";
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	"Record",
	"LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi1"
);
$GLOBALS["TCA"]["tt_content"]["types"]["list"]["subtypes_excludelist"][$pluginSigPi1] 	= "layout,select_key,recursive";
$GLOBALS["TCA"]["tt_content"]["types"]["list"]["subtypes_addlist"][$pluginSigPi1] 		= "pi_flexform";
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSigPi1, "FILE:EXT:" . $_EXTKEY . "/Configuration/FlexForms/Plugins/Record.xml");

/***********************************
 * Plugin - Search Records
 ***********************************/
$pluginSigPi2 = strtolower($extensionName) . "_search";
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	"Search",
	"LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi2"
);
$GLOBALS["TCA"]["tt_content"]["types"]["list"]["subtypes_excludelist"][$pluginSigPi2] 	= "layout,select_key,recursive";
$GLOBALS["TCA"]["tt_content"]["types"]["list"]["subtypes_addlist"][$pluginSigPi2] 		= "pi_flexform";
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSigPi2, "FILE:EXT:" . $_EXTKEY . "/Configuration/FlexForms/Plugins/Search.xml");

/***********************************
 * Plugin - Letter Selection
 ***********************************/
$pluginSigPi3 = strtolower($extensionName) . "_letter";
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	"Letter",
	"LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi3"
);
$GLOBALS["TCA"]["tt_content"]["types"]["list"]["subtypes_excludelist"][$pluginSigPi3] 	= "layout,select_key,recursive";
$GLOBALS["TCA"]["tt_content"]["types"]["list"]["subtypes_addlist"][$pluginSigPi3] 		= "pi_flexform";
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSigPi3, "FILE:EXT:" . $_EXTKEY . "/Configuration/FlexForms/Plugins/Letter.xml");

/***********************************
 * Plugin - Sorting
 ***********************************/
$pluginSigPi4 = strtolower($extensionName) . "_sort";
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	"Sort",
	"LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi4"
);
$GLOBALS["TCA"]["tt_content"]["types"]["list"]["subtypes_excludelist"][$pluginSigPi4] 	= "layout,select_key,recursive";
$GLOBALS["TCA"]["tt_content"]["types"]["list"]["subtypes_addlist"][$pluginSigPi4] 		= "pi_flexform";
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSigPi4, "FILE:EXT:" . $_EXTKEY . "/Configuration/FlexForms/Plugins/Sort.xml");

/***********************************
 * Plugin - Filtering
 ***********************************/
$pluginSigPi5 = strtolower($extensionName) . "_filter";
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	"Filter",
	"LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi5"
);
$GLOBALS["TCA"]["tt_content"]["types"]["list"]["subtypes_excludelist"][$pluginSigPi5] 	= "layout,select_key,recursive";
$GLOBALS["TCA"]["tt_content"]["types"]["list"]["subtypes_addlist"][$pluginSigPi5] 		= "pi_flexform";
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSigPi5, "FILE:EXT:" . $_EXTKEY . "/Configuration/FlexForms/Plugins/Filter.xml");

/***********************************
 * Plugin - Selecting
 ***********************************/
$pluginSigPi6 = strtolower($extensionName) . "_select";
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	"Select",
	"LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi6"
);
$GLOBALS["TCA"]["tt_content"]["types"]["list"]["subtypes_excludelist"][$pluginSigPi6] 	= "layout,select_key,recursive";
$GLOBALS["TCA"]["tt_content"]["types"]["list"]["subtypes_addlist"][$pluginSigPi6] 		= "pi_flexform";
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSigPi6, "FILE:EXT:" . $_EXTKEY . "/Configuration/FlexForms/Plugins/Select.xml");

/***********************************
 * Plugin - Form
 ***********************************/
$pluginSigPi7 = strtolower($extensionName) . "_form";
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	"Form",
	"LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:plugin.wizarditem_pi7"
);
$GLOBALS["TCA"]["tt_content"]["types"]["list"]["subtypes_excludelist"][$pluginSigPi7] 	= "layout,select_key,recursive";
$GLOBALS["TCA"]["tt_content"]["types"]["list"]["subtypes_addlist"][$pluginSigPi7] 		= "pi_flexform";
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSigPi7, "FILE:EXT:" . $_EXTKEY . "/Configuration/FlexForms/Plugins/Form.xml");


/***********************************
 * CMS Layout Hook
 ***********************************/
$GLOBALS["TYPO3_CONF_VARS"]["SC_OPTIONS"]["cms/layout/class.tx_cms_layout.php"]["list_type_Info"][$pluginSigPi1][$_EXTKEY] = "EXT:" . $_EXTKEY . "/Classes/CmsLayout/Record.php:MageDeveloper\\Dataviewer\\CmsLayout\\Record->getBackendPluginLayout";
$GLOBALS["TYPO3_CONF_VARS"]["SC_OPTIONS"]["cms/layout/class.tx_cms_layout.php"]["list_type_Info"][$pluginSigPi2][$_EXTKEY] = "EXT:" . $_EXTKEY . "/Classes/CmsLayout/Search.php:MageDeveloper\\Dataviewer\\CmsLayout\\Search->getBackendPluginLayout";
$GLOBALS["TYPO3_CONF_VARS"]["SC_OPTIONS"]["cms/layout/class.tx_cms_layout.php"]["list_type_Info"][$pluginSigPi3][$_EXTKEY] = "EXT:" . $_EXTKEY . "/Classes/CmsLayout/Letter.php:MageDeveloper\\Dataviewer\\CmsLayout\\Letter->getBackendPluginLayout";
$GLOBALS["TYPO3_CONF_VARS"]["SC_OPTIONS"]["cms/layout/class.tx_cms_layout.php"]["list_type_Info"][$pluginSigPi4][$_EXTKEY] = "EXT:" . $_EXTKEY . "/Classes/CmsLayout/Sort.php:MageDeveloper\\Dataviewer\\CmsLayout\\Sort->getBackendPluginLayout";
$GLOBALS["TYPO3_CONF_VARS"]["SC_OPTIONS"]["cms/layout/class.tx_cms_layout.php"]["list_type_Info"][$pluginSigPi5][$_EXTKEY] = "EXT:" . $_EXTKEY . "/Classes/CmsLayout/Filter.php:MageDeveloper\\Dataviewer\\CmsLayout\\Filter->getBackendPluginLayout";
$GLOBALS["TYPO3_CONF_VARS"]["SC_OPTIONS"]["cms/layout/class.tx_cms_layout.php"]["list_type_Info"][$pluginSigPi6][$_EXTKEY] = "EXT:" . $_EXTKEY . "/Classes/CmsLayout/Select.php:MageDeveloper\\Dataviewer\\CmsLayout\\Select->getBackendPluginLayout";
$GLOBALS["TYPO3_CONF_VARS"]["SC_OPTIONS"]["cms/layout/class.tx_cms_layout.php"]["list_type_Info"][$pluginSigPi7][$_EXTKEY] = "EXT:" . $_EXTKEY . "/Classes/CmsLayout/Form.php:MageDeveloper\\Dataviewer\\CmsLayout\\Form->getBackendPluginLayout";

/***********************************
 * Backend CSS File Include
 ***********************************/
$cssFile = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . "Resources/Public/Css/dataviewer-backend.css";
$css = @file_get_contents($cssFile);
$GLOBALS["TBE_STYLES"]["inDocStyles_TBEstyle"] .= $css;

/***********************************
 * Hook when saving record
 ***********************************/
$GLOBALS["TYPO3_CONF_VARS"]["SC_OPTIONS"]["t3lib/class.t3lib_tcemain.php"]["processDatamapClass"][$_EXTKEY] = "MageDeveloper\\Dataviewer\\DataHandling\\DataHandlingHook";
$GLOBALS["TYPO3_CONF_VARS"]["SC_OPTIONS"]["t3lib/class.t3lib_tcemain.php"]["processCmdmapClass"][$_EXTKEY] 	= "MageDeveloper\\Dataviewer\\DataHandling\\DataHandlingHook";

/***********************************
 * Hook for Record List
 ***********************************/
$GLOBALS["TYPO3_CONF_VARS"]["SC_OPTIONS"]["typo3/class.db_list_extra.inc"]["getTable"][$_EXTKEY] = "MageDeveloper\\Dataviewer\\Hooks\\RecordList";

/***********************************
 * Hook for adding datatype buttons
 * to the DocHeader Button Bar
 ***********************************/
$GLOBALS["TYPO3_CONF_VARS"]["SC_OPTIONS"]["Backend\\Template\\Components\\ButtonBar"]["getButtonsHook"][$_EXTKEY] = "MageDeveloper\\Dataviewer\\Hooks\\DocHeaderButtons->getButtons";

/**
 * We need to modify the inlineParentRecord Configuration in order to get the INLINE Elements to work
 */
$GLOBALS["TYPO3_CONF_VARS"]["SYS"]["formEngine"]["formDataGroup"]["inlineParentRecord"]["MageDeveloper\\Dataviewer\\Form\\FormDataProvider\\PrepareInlineTca"] = [];

/**
 * We need to modify the formDataGroups for the TcaDatabaseRecord FormDataGroup to get the Category Element correctly to work
 */
$GLOBALS["TYPO3_CONF_VARS"]["SYS"]["formEngine"]["formDataGroup"]["tcaDatabaseRecord"]["MageDeveloper\\Dataviewer\\Form\\FormDataProvider\\PrepareSelectTreeTca"] = [];

/**
 * Backend DataViewer Widget on Top
 */
$GLOBALS['TYPO3_CONF_VARS']['BE']['toolbarItems'][] = 'MageDeveloper\\Dataviewer\\Hooks\\ToolbarItem';

/***********************************
 * Backend Module
 ***********************************/
if (TYPO3_MODE === "BE")
{
	$GLOBALS["TCA"]["pages"]["columns"]["module"]["config"]["items"][] = [
		0 => "LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:tx_dataviewer",
		1 => "--div--",
	];

	$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
	if ($objectManager->isRegistered(\MageDeveloper\Dataviewer\Domain\Repository\DatatypeRepository::class) &&
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded("dataviewer")
	)
	{
		///////////////////////////////////////////////////////////
		// We generate page icons for each datatype, that exists //
		///////////////////////////////////////////////////////////

		try {
			// We need to ignore exceptions here in case the
			// table does not exist
			/* @var \MageDeveloper\Dataviewer\Domain\Repository\DatatypeRepository $datatypeRepository */
			$datatypeRepository = $objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\DatatypeRepository::class);
			$datatypes = $datatypeRepository->findAll(false);

			$GLOBALS["TCA"]["pages"]["columns"]["module"]["config"]["items"][] = [
				0 => "DataViewer Icons",
				1 => "--div--",
			];

			foreach($datatypes as $_datatype)
			{
				/* @var \MageDeveloper\Dataviewer\Domain\Model\Datatype $_datatype */
				$iconId = "extensions-dataviewer-".$_datatype->getIcon();

				if(!isset($GLOBALS["TCA"]["pages"]["ctrl"]["typeicon_classes"]["contains-dataviewer-{$iconId}"]))
				{
					$GLOBALS["TCA"]["pages"]["columns"]["module"]["config"]["items"][] = [
						0 => $_datatype->getName(),
						1 => "dataviewer-{$iconId}",
						2 => $iconId
					];
					$GLOBALS["TCA"]["pages"]["ctrl"]["typeicon_classes"]["contains-dataviewer-{$iconId}"] = $iconId;
				}

			}

			$GLOBALS["TCA"]["pages"]["columns"]["module"]["config"]["items"][] = [
				0 => "LLL:EXT:lang/locallang_view_help.xlf:TOC_extensions",
				1 => "--div--",
			];

		} catch(\Exception $e)
		{
			// No exception printing here        
		}

	}

	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		"MageDeveloper.".$_EXTKEY,
		"web",          			// Main area
		"dataviewer",         		// Name of the module
		"",             			// Position of the module
		[   	        			// Allowed controller action combinations
			"BackendModule" => "index, records, datatypes, datatypesDetails, recordsDetails, createRecord",
			"BackendCsvAssistant" => "index, page, datatype, file, assign, import, review",
		],
		[	// Additional configuration
			"access"    => "user,group",
			"icon"      => "EXT:dataviewer/Resources/Public/Images/module_icon.png",
			"labels" 	=> "LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf",
		]
	);

	$GLOBALS["TYPO3_CONF_VARS"]["EXTCONF"]["cms"]["db_layout"]["addTables"]["tx_dataviewer_domain_model_record"][] = [
		"MENU" => "",
		"fList" => "title, datatype",
		"icon" => true,
	];

}

