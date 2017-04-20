<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

return [
	"ctrl" => [
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
		"requestUpdate" => "type,table_content",
		"languageField" => "sys_language_uid",
		"transOrigPointerField" => "l10n_parent",
		"transOrigDiffSourceField" => "l10n_diffsource",
		"delete" => "deleted",
		"enablecolumns" => [
			"disabled" => "hidden",
			"starttime" => "starttime",
			"endtime" => "endtime",
		],
		"searchFields" => "type,value_content,image_content,file_content,table_content,column_name,where_clause,is_readonly,is_default,",
		"iconfile" => "EXT:dataviewer/Resources/Public/Icons/Domain/Model/FieldValue.gif",
	],
	'interface' => [
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, type, value_content, field_content, table_content, column_name, markers, where_clause, result, is_default, pretends_empty, pass_to_fe',
	],
	'types' => [
		'1' => ['showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, type, --div--;LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:value_content, value_content, field_content, table_content, column_name, markers, where_clause, result, is_default, pretends_empty, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.access, starttime, endtime'],
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
				'foreign_table' => 'tx_dataviewer_domain_model_fieldvalue',
				'foreign_table_where' => 'AND tx_dataviewer_domain_model_fieldvalue.pid=###CURRENT_PID### AND tx_dataviewer_domain_model_fieldvalue.sys_language_uid IN (-1,0)',
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
		'type' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.type',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:type.0', 0], // Fixed Value(s)
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:type.1', 1], // Database Value(s)
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:type.2', 2], // TypoScript
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:type.3', 3], // Field Content
				],
				//'itemsProcFunc' => "MageDeveloper\\Dataviewer\\UserFunc\\Fieldvalue->populateFieldvalueTypes",
				"default" => "0",
				'size' => 1,
				'maxitems' => 1,
				'eval' => 'required'
			],
		],
		'field_content' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.field_content',
			'displayCond' => 'FIELD:type:=:3',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'tx_dataviewer_domain_model_field',
				'foreign_table_where' => 'AND tx_dataviewer_domain_model_field.pid=###CURRENT_PID###',
				'size' => 1,
                'items' => [
                    ['------------------', 0],
                ],
				'maxitems' => 1,
				'multiple' => 0,
			],
		],
		'value_content' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.value_content',
			'displayCond' => 'FIELD:type:IN:0,2',
			'config' => [
				'type' => 'text',
				'renderType' => 't3editor',
				'format' => 'typoscript',
				'cols' => 40,
				'rows' => 10,
				'eval' => 'trim',
			],
		],
		'table_content' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.table_content',
			'displayCond' => 'FIELD:type:IN:1',
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
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.column_name',
			'displayCond' => 'FIELD:type:IN:1',
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
		'markers' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:flexform.available_markers',
            'displayCond' => 'FIELD:type:IN:1',
            'config' => [
                'type' => 'user',
                'userFunc' => "MageDeveloper\\Dataviewer\\UserFunc\\Text->displayAvailableMarkers",
                'eval' => '',
                'parameters' => [
                    'template' => "EXT:dataviewer/Resources/Private/Templates/CmsLayout/available_markers.html",
                    'includeRecord' => 1,
                ],
            ],
        ],
		'where_clause' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.where_clause',
			'displayCond' => 'FIELD:type:IN:1',
			'config' => [
				'type' => 'text',
				'cols' => 40,
				'rows' => 2,
				'eval' => 'trim',
				'placeholder' => 'x=\'y\' AND z=\'123\' ORDER BY z ASC',
			],
		],
		'result' => [
			'exclude' => 1,
			'label' => '',
			'displayCond' => 'FIELD:type:IN:1',
			'config' => [
				'type' => 'user',
				'userFunc' => "MageDeveloper\\Dataviewer\\UserFunc\\Database->displayTableContentResult",
				'eval' => '',
			],
		],
		'is_readonly' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.is_readonly',
			'config' => [
				'type' => 'check',
				'default' => 0
			],
		],
		'is_default' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.is_default',
			'displayCond' => 'FIELD:type:IN:0,1,2,3',
			'config' => [
				'type' => 'check',
				'default' => 0
			],
		],
		'pass_to_fe' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.pass_to_fe',
			'config' => [
				'type' => 'check',
				'default' => 0
			],
		],
		'pretends_empty' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.pretends_empty',
			'displayCond' => 'FIELD:type:IN:0,1,2',
			'config' => [
				'type' => 'check',
				'default' => 0,
			],
		],
		'field' => [
			'config' => [
				'type' => 'passthrough',
			],
		],
	],
];
