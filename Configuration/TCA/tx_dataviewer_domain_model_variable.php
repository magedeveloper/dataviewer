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
		"versioningWS" => true,
		"hideTable" => false,
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
		'showRecordFieldList' => 'logo, sys_language_uid, l10n_parent, l10n_diffsource, hidden, type, variable_name, session_key, server, page, variable_value, record, field, table_content, column_name, where_clause, user_func',
	],
	'types' => [
		'1' => [
			'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    logo, type, variable_name, session_key, server, page, variable_value, record, field, table_content, column_name, where_clause, user_func, type_cast,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                    --palette--;;language,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden,--palette--;;timeRestriction
            ',
		],
	],
	'palettes' => [
		'timeRestriction' => ['showitem' => 'starttime, endtime'],
		'language' => ['showitem' => 'sys_language_uid, l10n_parent'],
	],
	'columns' => [
		'sys_language_uid' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.language',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => [
					['LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages', -1],
					['LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.default_value', 0],
				],
			],
		],
		'l10n_parent' => [
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
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
			'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'max' => 30
			]
		],
		'deleted' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.deleted',
			'config' => [
				'type' => 'check',
			],
		],
		'hidden' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
			'config' => [
				'type' => 'check',
			],
		],
		'starttime' => [
			'exclude' => true,
			'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
			'config' => [
				'type' => 'input',
				'renderType' => 'inputDateTime',
				'eval' => 'datetime',
				'default' => 0,
				'behaviour' => [
					'allowLanguageSynchronization' => true,
				]
			]
		],
		'endtime' => [
			'exclude' => true,
			'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
			'config' => [
				'type' => 'input',
				'renderType' => 'inputDateTime',
				'eval' => 'datetime',
				'default' => 0,
				'range' => [
					'upper' => mktime(0, 0, 0, 1, 1, 2038),
				],
				'behaviour' => [
					'allowLanguageSynchronization' => true,
				]
			]
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
			'onChange' => 'reload',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.0', 0], // Fixed Value
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.1', 1], // TypoScript Value
					//['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.2', 2], // TypoScript Variable Name
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.3', 3], // GET Variable
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.4', 4], // POST Variable
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.5', 5], // Fixed Record
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.6', 6], // Fixed Record Field
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.7', 7], // Database Value
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.8', 8], // Frontend User
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.9', 9], // SERVER Variable
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.10', 10], // Dynamic Record
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.11', 11], // User Session Variable
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.12', 12], // Page Id
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.13', 13], // User Func
				],
				"default" => "0",
				'size' => 1,
				'maxitems' => 1,
				'eval' => 'required'
			],
		],
		'server' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.server',
			'displayCond' => 'FIELD:type:IN:9',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['SERVER_ADDR', 'SERVER_ADDR'], 			// SERVER_ADDR
					['SERVER_NAME', 'SERVER_NAME'], 			// SERVER_NAME
					['REQUEST_METHOD', 'REQUEST_METHOD'], 		// REQUEST_METHOD
					['QUERY_STRING', 'QUERY_STRING'], 			// QUERY_STRING
					['DOCUMENT_ROOT', 'DOCUMENT_ROOT'], 		// DOCUMENT_ROOT
					['HTTP_HOST', 'HTTP_HOST'], 				// HTTP_HOST
					['HTTP_REFERER', 'HTTP_REFERER'], 			// HTTP_REFERER
					['HTTP_USER_AGENT', 'HTTP_USER_AGENT'], 	// HTTP_USER_AGENT
					['HTTPS', 'HTTPS'], 						// HTTPS
					['REMOTE_ADDR', 'REMOTE_ADDR'], 			// REMOTE_ADDR
					['SERVER_PORT', 'SERVER_PORT'], 			// SERVER_PORT
					['REQUEST_URI', 'REQUEST_URI'], 			// REQUEST_URI
				],
				"default" => "REMOTE_ADDR",
				'size' => 1,
				'maxitems' => 1,
			],
		],
		'variable_name' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.variable_name',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'nospace,required,trim'
			],
		],
		'session_key' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.session_key',
			'displayCond' => 'FIELD:type:IN:11',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'nospace,required,trim'
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
		'user_func' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.user_func',
			'displayCond' => 'FIELD:type:IN:13',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'required,trim',
				'placeholder' => 'VendorName\ExtensionName\UserFunc\YourUserFunc->userFuncMethod',
			],
		],
		'page' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.page',
			'displayCond' => 'FIELD:type:IN:12',
			'config' => [
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'pages',
				'size' => 1,
				'maxitems' => 1,
				'multiple' => 0,
			],
		],
		'record' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.record',
			'onChange' => 'reload',
			'displayCond' => 'FIELD:type:IN:5,6',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'itemsProcFunc' => "MageDeveloper\\Dataviewer\\UserFunc\\Record->populateRecordsAction",
				'items' => [
					['', 0],
				],
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
				'itemsProcFunc' => "MageDeveloper\\Dataviewer\\UserFunc\\Field->populateFieldsByRecord",
				'items' => [
					['', 0],
				],
				'size' => 1,
				'maxitems' => 1,
				'multiple' => 0,
			],
		],
		'table_content' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.table_content',
			'onChange' => 'reload',
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
			'onChange' => 'reload',
			'displayCond' => 'FIELD:type:=:7',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectMultipleSideBySide',
				'enableMultiSelectFilterTextfield' => true,
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
		'type_cast' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.type_cast',
			'displayCond' => 'FIELD:type:IN:3,4',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type_cast.0', 0], // No type definition
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type_cast.1', 1], // Boolean
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type_cast.2', 2], // Integer
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type_cast.3', 3], // Float
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type_cast.4', 4], // String
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type_cast.5', 5], // Array
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type_cast.6', 6], // Object
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type_cast.7', 7], // NULL
				],
				"default" => "0",
				'size' => 1,
				'maxitems' => 1,
				'eval' => 'required'
			],
		],
	],
];
