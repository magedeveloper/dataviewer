<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

return [
	"ctrl" => [
		"title"	=> "LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field",
		"label" => "frontend_label",
		"label_userFunc" => "MageDeveloper\\Dataviewer\\LabelUserFunc\\Field->displayLabel",
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"dividers2tabs" => TRUE,
		"default_sortby" => "ORDER BY uid DESC",
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
		"enablecolumns" => [
			"disabled" => "hidden",
			"starttime" => "starttime",
			"endtime" => "endtime",
		],
		"searchFields" => "id,type,frontend_label,css_class,unit,is_required,show_description,field_values,",
		"iconfile" => "EXT:dataviewer/Resources/Public/Icons/Domain/Model/Field.gif",
	],
	'interface' => [
		'showRecordFieldList' => 'logo, sys_language_uid, l10n_parent, l10n_diffsource, hidden, exclude, type, field_conf, tab_name, frontend_label, variable_name, id, templatefile, description, css_class, column_width, unit, is_record_title, show_title, field_values, validation, display_cond, request_update, field_ids',
	],
	'types' => [
		'1' => ['showitem' => 'logo, sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden, exclude;;1, type, field_conf, is_record_title, --div--;LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:frontend_settings, frontend_label, variable_name, id, templatefile, --div--;LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:backend_settings, tab_name, show_title, description, column_width, css_class, --div--;LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.field_values, field_values, unit, --div--;LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:flexform.validation, validation, --div--;LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:flexform.display_cond, field_ids, display_cond, request_update, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'],
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
				'foreign_table' => 'tx_dataviewer_domain_model_field',
				'foreign_table_where' => 'AND tx_dataviewer_domain_model_field.pid=###CURRENT_PID### AND tx_dataviewer_domain_model_field.sys_language_uid IN (-1,0)',
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
		'hidden' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => [
				'type' => 'check',
			],
		],
		'exclude' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.exclude',
			'config' => [
				'type' => 'check',
			],
		],
		'tstamp' => [
			'exclude' => 1,
			'label' => '',
			'config' => [
				'type' => 'none',
				'format' => 'datetime',
				'eval' => 'datetime',
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
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
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
		'id' => [
			'exclude' => 1,
			'label' => '',
			'config' => [
				'type' => 'user',
				'userFunc' => 'MageDeveloper\Dataviewer\UserFunc\Field->displayGeneratedFieldIdentifier',
			],
		],
		'type' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.type',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'itemsProcFunc' => "MageDeveloper\\Dataviewer\\UserFunc\\Fieldtype->populateFieldtypes",
				'size' => 1,
				'maxitems' => 1,
				'eval' => '', 
				'showIconTable' => true,
			],
		],
		'column_width' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.column_width',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['12 (100%)', 	"col-sm-12"],
					['11', 		"col-sm-11"],
					['10', 		"col-sm-10"],
					['9 (75%)', 	"col-sm-9"],
					['8 (66%)', 	"col-sm-8"],
					['7', 			"col-sm-7"],
					['6 (50%)', 	"col-sm-6"],
					['5', 			"col-sm-5"],
					['4 (33%)',	"col-sm-4"],
					['3 (25%)',	"col-sm-3"],
					['2', 			"col-sm-2"],
					['1', 			"col-sm-1"],
				],
				'size' => 1,
				'maxitems' => 1,
				'eval' => ''
			],
		],
		'field_conf' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.field_configuration',
			'config' => [
				'type' => 'flex',
				'ds_pointerField' => 'type',
				'ds' => \MageDeveloper\Dataviewer\Utility\FieldtypeConfigurationUtility::getDsConfig(),
			],
		],
		'tab_name' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.tab_name',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			],
		],
		'frontend_label' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.frontend_label',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			],
		],
		'variable_name' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.variable_name',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,lower,alpha'
			],
		],
		'is_active' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.is_active',
			'config' => [
				'type' => 'check',
				'default' => 1,
			],
		],
		'description' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.description',
			'config' => [
				'type' => 'text',
				'cols' => 40,
				'rows' => 5,
				'eval' => 'trim',				
			],
		],
		'css_class' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.css_class',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim',
				'placeholder' => 'callout-info',
			],
		],
		'unit' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.unit',
			'displayCond' => 'FIELD:type:!IN:IMAGE,FILE,PAGE',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			],
		],
		'show_title' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.show_title',
			'config' => [
				'type' => 'check',
				'default' => 1
			],
		],
		'is_record_title' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.is_record_title',
			'config' => [
				'type' => 'check',
				'default' => 0
			],
		],
		'validation' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.validation',
			'config' => [
				'type' => 'flex',
				'ds' => [
					'default' => 'FILE:EXT:dataviewer/Configuration/FlexForms/Field/Validation.xml'
				],
			],
		],
		'field_ids' => [
			'exclude' => 1,
			'label' => '',
			'config' => [
				'type' => 'user',
				'userFunc' => 'MageDeveloper\Dataviewer\UserFunc\Field->displayAvailableFieldIds',
			],
		],
		'display_cond' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.display_cond',
			'config' => [
				'type' => 'text',
				'cols' => 40,
				'rows' => 10,
				'eval' => 'trim',
				'renderType' => 't3editor',
				'format' => 'xml',
			],
		],
		'request_update' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.request_update',
			'config' => [
				'type' => 'check',
				'default' => 0
			],
		],
		'field_values' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.field_values',
			'config' => [
				'type' => 'inline',
				'foreign_table' => 'tx_dataviewer_domain_model_fieldvalue',
				'foreign_field' => 'field',
				'foreign_sortby' => 'sorting',
				'maxitems'      => 9999,
				'appearance' => [
					'collapseAll' => 1,
					'levelLinksPosition' => 'top',
					'showSynchronizationLink' => 1,
					'showPossibleLocalizationRecords' => 1,
					'useSortable' => 1,
					'showAllLocalizationLink' => 1
				],
			],

		],
		'templatefile' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.templatefile',
			'config' => [
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => 'html',
				'max_size' => 2000,
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			],
		],
	],
];
