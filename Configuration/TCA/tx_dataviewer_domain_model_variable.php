<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

return [
	"ctrl" => [
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
		"enablecolumns" => [
			"disabled" => "hidden",
			"starttime" => "starttime",
			"endtime" => "endtime",
		],
		"searchFields" => "variable_name,type,",
		"iconfile" => "EXT:dataviewer/Resources/Public/Icons/Domain/Model/Variable.gif",
	],
	'interface' => [
		'showRecordFieldList' => 'logo, sys_language_uid, l10n_parent, l10n_diffsource, hidden, type, variable_name, variable_value, record, field, table_content, column_name, where_clause',
	],
	'types' => [
		'1' => ['showitem' => 'logo, sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, type, variable_name, variable_value, record, field, table_content, column_name, where_clause, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'],
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
				'foreign_table' => 'tx_dataviewer_domain_model_variable',
				'foreign_table_where' => 'AND tx_dataviewer_domain_model_variable.pid=###CURRENT_PID### AND tx_dataviewer_domain_model_variable.sys_language_uid IN (-1,0)',
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
		'type' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.type',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.0', 0], // Fixed Value
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.1', 1], // TypoScript Value
					//['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.2', 2], // TypoScript Variable Name
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.3', 3], // GET Variable
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.4', 4], // POST Variable
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.5', 5], // Record
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.6', 6], // Record Field
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.7', 7], // Database Value
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.8', 8], // Frontend User
				],
				"default" => "0",
				'size' => 1,
				'maxitems' => 1,
				'eval' => 'required'
			],
		],
		'variable_name' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.variable_name',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'alpha,nospace,required,trim'
			],
		],
		'variable_value' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.variable_value',
			'displayCond' => 'FIELD:type:IN:0,1',
			'config' => [
				'type' => 'text',
				'renderType' => 't3editor',
				'format' => 'typoscript',
				'cols' => 40,
				'rows' => 10,
				'eval' => 'trim'
			],
		],
		'record' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.record',
			'displayCond' => 'FIELD:type:IN:5,6',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'tx_dataviewer_domain_model_record',
				'foreign_table_where' => 'AND tx_dataviewer_domain_model_record.pid=###CURRENT_PID###',
				'size' => 1,
				'maxitems' => 1,
				'multiple' => 0,
			],
		],
		'field' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.field',
			'displayCond' => 'FIELD:type:=:6',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'tx_dataviewer_domain_model_field',
				'foreign_table_where' => 'AND tx_dataviewer_domain_model_field.pid=###CURRENT_PID###',
				'size' => 1,
				'maxitems' => 1,
				'multiple' => 0,
			],
		],
		'table_content' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.table_content',
			'displayCond' => 'FIELD:type:=:7',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'itemsProcFunc' => "MageDeveloper\\Dataviewer\\UserFunc\\Database->populateTablesAction",
				'size' => 1,
				'maxitems' => 1,
				'eval' => ''
			],
		],
		'column_name' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.column_name',
			'displayCond' => 'FIELD:type:=:7',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectMultipleSideBySide',
				'itemsProcFunc' => "MageDeveloper\\Dataviewer\\UserFunc\\Database->populateColumnsAction",
				'size' => 3,
				'maxitems' => 999,
				'minitems' => 1,
				'eval' => ''
			],
		],
		'where_clause' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.where_clause',
			'displayCond' => 'FIELD:type:=:7',
			'config' => [
				'type' => 'text',
				'cols' => 40,
				'rows' => 2,
				'eval' => 'trim',
				'placeholder' => 'x=\'y\' AND z=\'123\' ORDER BY z ASC',
			],
		],
	],
];
