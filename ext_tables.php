<?php
if (!defined("TYPO3_MODE")) {
	die ("Access denied.");
}

$extensionName = \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($_EXTKEY);

/***********************************
 * Static File Implementation
 ***********************************/
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, "Configuration/TypoScript", "DataViewer Extension");

/***********************************
 * Register Icons
 ***********************************/
$datatypeIcons = \MageDeveloper\Dataviewer\Utility\IconUtility::getIcons();
$fieldtypeIcons = \MageDeveloper\Dataviewer\Utility\IconUtility::getFieldtypeIcons();
$icons = array_merge($datatypeIcons, $fieldtypeIcons);
\TYPO3\CMS\Backend\Sprite\SpriteManager::addSingleIcons($icons, $_EXTKEY);

/***********************************
 * Table Configuration "Datatype"
 ***********************************/
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr("tx_dataviewer_domain_model_datatype", "EXT:dataviewer/Resources/Private/Language/locallang_csh_tx_dataviewer_domain_model_datatype.xlf");
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages("tx_dataviewer_domain_model_datatype");
$GLOBALS["TCA"]["tx_dataviewer_domain_model_datatype"] = array(
	"ctrl" => array(
		"title"	=> "LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype",
		"label" => "name",
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"dividers2tabs" => TRUE,
		"sortby" => "sorting",
		"versioningWS" => 2,
		"versioning_followPages" => TRUE,
		"languageField" => "sys_language_uid",
		"transOrigPointerField" => "l10n_parent",
		"transOrigDiffSourceField" => "l10n_diffsource",
		"delete" => "deleted",
		"typeicon_column" => "icon",
		"typeicon_classes" => \MageDeveloper\Dataviewer\Utility\IconUtility::getClasses(),
		"enablecolumns" => array(
			"disabled" => "hidden",
			"starttime" => "starttime",
			"endtime" => "endtime",
		),
		"searchFields" => "name,description,icon,templatefile,fields,",
		"dynamicConfigFile" => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . "Configuration/TCA/Datatype.php",
		"iconfile" => "EXT:dataviewer/Resources/Public/Icons/Domain/Model/Datatype.gif"
	),
);

/***********************************
 * Table Configuration "Field"
 ***********************************/
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr("tx_dataviewer_domain_model_field", "EXT:dataviewer/Resources/Private/Language/locallang_csh_tx_dataviewer_domain_model_field.xlf");
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages("tx_dataviewer_domain_model_field");
$GLOBALS["TCA"]["tx_dataviewer_domain_model_field"] = array(
	"ctrl" => array(
		"title"	=> "LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field",
		"label" => "frontend_label",
		"label_userFunc" => "MageDeveloper\\Dataviewer\\LabelUserFunc\\Field->displayLabel",
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"dividers2tabs" => TRUE,
		//"sortby" => "sorting",
		"versioningWS" => 2,
		"versioning_followPages" => TRUE,
		"requestUpdate" => "type,frontend_label",
		"typeicon_column" => "type",
		"typeicon_classes" => \MageDeveloper\Dataviewer\Utility\IconUtility::getFieldtypeIconClasses(),
		"languageField" => "sys_language_uid",
		"transOrigPointerField" => "l10n_parent",
		"transOrigDiffSourceField" => "l10n_diffsource",
		"delete" => "deleted",
		"enablecolumns" => array(
			"disabled" => "hidden",
			"starttime" => "starttime",
			"endtime" => "endtime",
		),
		"searchFields" => "id,type,frontend_label,css_class,unit,is_required,show_description,field_values,",
		"dynamicConfigFile" => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . "Configuration/TCA/Field.php",
		"iconfile" => "EXT:dataviewer/Resources/Public/Icons/Domain/Model/Field.gif",
	),
);

/***********************************
 * Table Configuration "FieldValue"
 ***********************************/
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr("tx_dataviewer_domain_model_fieldvalue", "EXT:dataviewer/Resources/Private/Language/locallang_csh_tx_dataviewer_domain_model_fieldvalue.xlf");
$GLOBALS["TCA"]["tx_dataviewer_domain_model_fieldvalue"] = array(
	"ctrl" => array(
		"title"	=> "LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue",
		"label" => "type",
		"label_userFunc" => "MageDeveloper\\Dataviewer\\LabelUserFunc\\FieldValue->displayLabel",
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"dividers2tabs" => TRUE,
		"hideTable" => true,
		"sortby" => "sorting",
		"versioningWS" => 2,
		"versioning_followPages" => TRUE,
		"requestUpdate" => "type,table_content,column_name",
		"languageField" => "sys_language_uid",
		"transOrigPointerField" => "l10n_parent",
		"transOrigDiffSourceField" => "l10n_diffsource",
		"delete" => "deleted",
		"enablecolumns" => array(
			"disabled" => "hidden",
			"starttime" => "starttime",
			"endtime" => "endtime",
		),
		"searchFields" => "type,value_content,image_content,file_content,table_content,column_name,where_clause,is_readonly,is_default,",
		"dynamicConfigFile" => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . "Configuration/TCA/FieldValue.php",
		"iconfile" => "EXT:dataviewer/Resources/Public/Icons/Domain/Model/FieldValue.gif",
	),
);

/***********************************
 * Table Configuration "Record"
 ***********************************/
//\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr("tx_dataviewer_domain_model_record", "EXT:dataviewer/Resources/Private/Language/locallang_csh_tx_dataviewer_domain_model_record.xlf");
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages("tx_dataviewer_domain_model_record");
$GLOBALS["TCA"]["tx_dataviewer_domain_model_record"] = array(
	"ctrl" => array(
		"title"	=> "LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_record",
		"label" => "title",
		"label_userFunc" => "MageDeveloper\\Dataviewer\\LabelUserFunc\\Record->displayLabel",
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"dividers2tabs" => TRUE,
		"sortby" => "sorting",
		"versioningWS" => false,
		//"versioningWS" => 2,
		"versioning_followPages" => TRUE,
		"requestUpdate" => "datatype",
		"languageField" => "sys_language_uid",
		"transOrigPointerField" => "l10n_parent",
		"transOrigDiffSourceField" => "l10n_diffsource",
		"typeicon_column" => "icon",
		"typeicon_classes" => \MageDeveloper\Dataviewer\Utility\IconUtility::getClasses(),
		"delete" => "deleted",
		"enablecolumns" => array(
			"disabled" => "hidden",
			"starttime" => "starttime",
			"endtime" => "endtime",
		),
		"searchFields" => "title",
		"dynamicConfigFile" => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . "Configuration/TCA/Record.php",
		"iconfile" => "EXT:dataviewer/Resources/Public/Icons/Domain/Model/Record.gif",
	),
);

/***********************************
 * Table Configuration "RecordValue"
 ***********************************/
//\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr("tx_dataviewer_domain_model_recordvalue", "EXT:dataviewer/Resources/Private/Language/locallang_csh_tx_dataviewer_domain_model_recordvalue.xlf");
$GLOBALS["TCA"]["tx_dataviewer_domain_model_recordvalue"] = array(
	"ctrl" => array(
		"title"	=> "LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_recordvalue",
		"label" => "value_content",
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"dividers2tabs" => TRUE,
		"versioningWS" => 2,
		"hideTable" => true,
		"versioning_followPages" => TRUE,
		"languageField" => "sys_language_uid",
		"transOrigPointerField" => "l10n_parent",
		"transOrigDiffSourceField" => "l10n_diffsource",
		"delete" => "deleted",
		"enablecolumns" => array(
			"disabled" => "hidden",
			"starttime" => "starttime",
			"endtime" => "endtime",
		),
		"searchFields" => "value_content,record,field,",
		"dynamicConfigFile" => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . "Configuration/TCA/RecordValue.php",
		"iconfile" => "EXT:dataviewer/Resources/Public/Icons/Domain/Model/RecordValue.gif",
	),
);

/***********************************
 * Table Configuration "Variable"
 ***********************************/
//\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr("tx_dataviewer_domain_model_variable", "EXT:dataviewer/Resources/Private/Language/locallang_csh_tx_dataviewer_domain_model_variable.xlf");
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages("tx_dataviewer_domain_model_variable");
$GLOBALS["TCA"]["tx_dataviewer_domain_model_variable"] = array(
	"ctrl" => array(
		"title"	=> "LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable",
		"label" => "variable_name",
		"label_userFunc" => "MageDeveloper\\Dataviewer\\LabelUserFunc\\Variable->displayLabel",
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"dividers2tabs" => TRUE,
		"versioningWS" => 2,
		"hideTable" => false,
		"requestUpdate" => "type,table_content,column_name",
		"versioning_followPages" => TRUE,
		"languageField" => "sys_language_uid",
		"transOrigPointerField" => "l10n_parent",
		"transOrigDiffSourceField" => "l10n_diffsource",
		"delete" => "deleted",
		"enablecolumns" => array(
			"disabled" => "hidden",
			"starttime" => "starttime",
			"endtime" => "endtime",
		),
		"searchFields" => "variable_name,type,",
		"dynamicConfigFile" => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . "Configuration/TCA/Variable.php",
		"iconfile" => "EXT:dataviewer/Resources/Public/Icons/Domain/Model/Variable.gif",
	),
);

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
 * We need to modify the inlineParentRecord Configuration in Order to get the INLINE Elements to work
 */
$GLOBALS["TYPO3_CONF_VARS"]["SYS"]["formEngine"]["formDataGroup"]["inlineParentRecord"]["MageDeveloper\\Dataviewer\\Form\\FormDataProvider\\PrepareDataviewerTca"] = array();

/***********************************
 * Backend Module
 ***********************************/
if (TYPO3_MODE === "BE") {
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		"MageDeveloper.".$_EXTKEY,
		"web",          			// Main area
		"dataviewer",         		// Name of the module
		"",             			// Position of the module
		array(          			// Allowed controller action combinations
			"BackendModule" => "index, records, datatypes, datatypesDetails",
		),
		array(          			// Additional configuration
			"access"    => "user,group",
			"icon"      => "EXT:dataviewer/Resources/Public/Images/module_icon.png",
			"labels" 	=> "LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf"
		)
	);

	$GLOBALS["TYPO3_CONF_VARS"]["EXTCONF"]["cms"]["db_layout"]["addTables"]["tx_dataviewer_domain_model_record"][] = array(
		"MENU" => "",
		"fList" => "title, datatype",
		"icon" => true,
	);
	
}
