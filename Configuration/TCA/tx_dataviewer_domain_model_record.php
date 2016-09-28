<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}


return [
	"ctrl" => [
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
		"enablecolumns" => [
			"disabled" => "hidden",
			"starttime" => "starttime",
			"endtime" => "endtime",
		],
		"searchFields" => "title",
		"iconfile" => "EXT:dataviewer/Resources/Public/Icons/Domain/Model/Record.gif",
	],
	'interface' => [
		'showRecordFieldList' => 'logo, sys_language_uid, l10n_parent, l10n_diffsource, hidden, datatype, record_content',
	],
	'types' => [
		'1' => ['showitem' => 'logo;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, datatype, record_content, --div--;LLL:EXT:lang/Resources/Private/Language/locallang.xlf:table.language, sys_language_uid, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'],
	],
	'palettes' => [
		'1' => ['showitem' => ''],
	],
	'columns' => [
		'sys_language_uid' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => [
					['LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1],
					['LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0],
				],
			],
		],
		'l10n_parent' => [
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['', 0],
				],
				'foreign_table' => 'tx_dataviewer_domain_model_record',
				'foreign_table_where' => 'AND tx_dataviewer_domain_model_record.pid=###CURRENT_PID### AND tx_dataviewer_domain_model_record.sys_language_uid IN (-1,0)',
			],
		],
		'l10n_diffsource' => [
			'config' => [
				'type' => 'passthrough',
			],
		],
		't3ver_label' => [
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			],
		],
		'deleted' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.deleted',
			'config' => [
				'type' => 'check',
			],
		],
		'hidden' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => [
				'type' => 'check',
			],
		],
		'starttime' => [
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
			'config' => [
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => [
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y')),
				],
			],
		],
		'endtime' => [
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
			'config' => [
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => [
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y')),
				],
			],
		],
		'logo' => [
			'exclude' => 1,
			'label' => '',
			'config' => [
				'type' => 'user',
				'userFunc' => 'MageDeveloper\Dataviewer\UserFunc\Logo->displayLogoText',
			],
		],
		'datatype' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_record.datatype',
			'displayCond' => 'FIELD:datatype:REQ:0',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:flexform.please_select', 0],
				],
				'itemsProcFunc' => "MageDeveloper\\Dataviewer\\UserFunc\\Datatype->populateDatatypesAction",
				'foreign_table' => 'tx_dataviewer_domain_model_datatype',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
				'multiple' => 0,
			],
		],
		'title' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_record.title',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => ''
			],
		],
		'record_content' => [
			'exclude' => 1,
			'label' => '',
			'config' => [
				'type' => 'user',
				'userFunc' => "MageDeveloper\\Dataviewer\\Form\\Renderer\\RecordRenderer->render",
			],
		],
		'icon' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_record.icon',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => ''
			],
		],
		'record_values' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_record.record_values',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectMultipleSideBySide',
				'foreign_table' => 'tx_dataviewer_domain_model_recordvalue',
				'foreign_field' => 'record',
				'foreign_table_where' => 'AND tx_dataviewer_domain_model_recordvalue.pid=###CURRENT_PID###',
				'size' => 10,
				'maxitems' => 9999,
				'multiple' => 1,
			],
		],
	],
];
