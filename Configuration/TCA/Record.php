<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TCA']['tx_dataviewer_domain_model_record'] = array(
	'ctrl' => $GLOBALS['TCA']['tx_dataviewer_domain_model_record']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'logo, sys_language_uid, l10n_parent, l10n_diffsource, hidden, datatype, record_content',
	),
	'types' => array(
		'1' => array('showitem' => 'logo;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, datatype, record_content, --div--;LLL:EXT:lang/Resources/Private/Language/locallang.xlf:table.language, sys_language_uid, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(
		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0)
				),
			),
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_dataviewer_domain_model_record',
				'foreign_table_where' => 'AND tx_dataviewer_domain_model_record.pid=###CURRENT_PID### AND tx_dataviewer_domain_model_record.sys_language_uid IN (-1,0)',
			),
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),

		't3ver_label' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			)
		),
		'deleted' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.deleted',
			'config' => array(
				'type' => 'check',
			),
		),
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'endtime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'logo' => array(
			'exclude' => 1,
			'label' => '',
			'config' => array(
				'type' => 'user',
				'userFunc' => 'MageDeveloper\Dataviewer\UserFunc\Logo->displayLogoText',
			),
		),
		'datatype' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_record.datatype',
			'displayCond' => 'FIELD:datatype:REQ:0',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => array (
					array('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:flexform.please_select', 0),
				),
				'itemsProcFunc' => "MageDeveloper\\Dataviewer\\UserFunc\\Datatype->populateDatatypesAction",
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
				'multiple' => 0,
			),
		),
		'title' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_record.title',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => ''
			),
		),
		'record_content' => array(
			'exclude' => 1,
			'label' => '',
			'config' => array(
				'type' => 'user',
				'userFunc' => "MageDeveloper\\Dataviewer\\Form\\Renderer\\RecordRenderer->render",
			),
		),
		'icon' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_record.icon',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => ''
			),
		),
		'record_values' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_record.record_values',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectMultipleSideBySide',
				'foreign_table' => 'tx_dataviewer_domain_model_recordvalue',
				'foreign_field' => 'record',
				'foreign_table_where' => 'AND tx_dataviewer_domain_model_recordvalue.pid=###CURRENT_PID###',
				'size' => 10,
				'maxitems' => 9999,
				'multiple' => 1,
			),
		),
	),
);
