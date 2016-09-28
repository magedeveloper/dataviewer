<?php
if (!defined("TYPO3_MODE")) {
	die ("Access denied.");
}

/***********************************
 * New Content Element - Wizard
 ***********************************/
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig("<INCLUDE_TYPOSCRIPT: source=\"FILE:EXT:".$_EXTKEY."/Configuration/PageTS/modWizards.ts\">");

/***********************************
 * Change sorting for Records
 ***********************************/
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig("mod.web_list.tableDisplayOrder.tx_dataviewer_domain_model_record.before = pages, fe_groups, fe_users, tx_dataviewer_domain_model_datatype");

/***********************************
 * Dataviewer Plugins
 * =================================
 * 
 * #1 - Display Record(s)
 * ---------------------------------
 * This plugin adds the possibility
 * to integrate a record, multiple
 * records or a record part to your
 * page.
 * 
 * #2 - Search Records
 * ---------------------------------
 * This plugin can search through
 * selected records. It adds a 
 * searchbox to your site with
 * configurable search options.
 * 
 * #3 - Letter Selection
 * ---------------------------------
 * Adds a letter selection to your
 * site, to select records with
 * their starting letter.
 * 
 * #4 - Sorting
 * ---------------------------------
 * Adds a sort form to your site,
 * which enabled you to sort the
 * displayed records by given
 * sorting elements.
 * 
 * #5 - Filtering
 * ---------------------------------
 * Adds a filter form to your site
 * to filter availble records.
 * 
 * #6 - Selecting
 * ---------------------------------
 * Adds a selection form to your
 * site for selecting records.
 * 
 * #7 - Form
 * ---------------------------------
 * Adds a form to your site with a
 * customizable template. The
 * form, that will be included is
 * for creating new records in the 
 * backend.
 * 
 * 
 ***********************************/
// #1 - Display Records
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'MageDeveloper.'.$_EXTKEY,
	"Record",
	["Record" => "index, list, detail, dynamicDetail, part"], // Cached
	["Record" => "index, list, detail, dynamicDetail, part"], // UnCached
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_PLUGIN
);

// #2 - Search Records
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'MageDeveloper.'.$_EXTKEY,
	"Search",
	["Search" => "index, search, reset"], // Cached
	["Search" => "index, search, reset"], // UnCached
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_PLUGIN
);

// #3 - Letter Selection
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'MageDeveloper.'.$_EXTKEY,
	"Letter",
	["Letter" => "index, letter"], // Cached
	["Letter" => "index, letter"], // UnCached
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_PLUGIN
);

// #4 - Sorting
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'MageDeveloper.'.$_EXTKEY,
	"Sort",
	["Sort" => "index, sort"], // Cached
	["Sort" => "index, sort"], // UnCached
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_PLUGIN
);

// #5 - Filtering
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'MageDeveloper.'.$_EXTKEY,
	"Filter",
	["Filter" => "index, add, remove, reset"], // Cached
	["Filter" => "index, add, remove, reset"], // UnCached
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_PLUGIN
);

// #6 - Selecting
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'MageDeveloper.'.$_EXTKEY,
	"Select",
	["Select" => "index, select"], // Cached
	["Select" => "index, select"], // UnCached
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_PLUGIN
);

// #7 - Form
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'MageDeveloper.'.$_EXTKEY,
	"Form",
	["Form" => "index, post"], // Cached
	["Form" => "index, post"], // UnCached
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_PLUGIN
);
