<?php
defined('TYPO3') or die();

return [
    'ctrl' => [
        'title' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable',
        'label' => 'variable_name',
        'label_userFunc' => "Aix\\Dataviewer\\LabelUserFunc\\Variable->displayLabel",
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'versioningWS' => false,
        'hideTable' => false,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
        'searchFields' => 'variable_name,type,parameter_name',
        'iconfile' => 'EXT:dataviewer/Resources/Public/Icons/Domain/Model/variable.svg',
    ],
	'interface' => [
		'showRecordFieldList' => 'logo, sys_language_uid, l10n_parent, l10n_diffsource, hidden, type, variable_name, ext_conf, typoscript_path, session_key, server, page, variable_value, record, field, table_content, column_name, where_clause, user_func, allowed_values, regex, value_switch, datatype',
	],
	'types' => [
		'1' => [
			'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    logo, type, variable_name, parameter_name, ext_conf, typoscript_path, session_key, server, page, variable_value, record, field, table_content, column_name, where_clause, user_func,datatype,--palette--;LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:flexform.section.variable_settings;valueSettings,
                --div--;LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:tx_dataviewer_domain_model_variable.value_switch,
                    value_switch,
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
        'valueSettings' => ['showitem' => 'type_cast, regex, --linebreak--, allowed_values'],
	],
	'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ],
                ],
                'default' => 0,
            ]
        ],
		'l10n_parent' => [
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => true,
			'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['', 0],
				],
				'foreign_table' => 'tx_dataviewer_domain_model_variable',
				'foreign_table_where' => 'AND tx_dataviewer_domain_model_variable.pid=###CURRENT_PID### AND tx_dataviewer_domain_model_variable.sys_language_uid IN (-1,0)',
                'default' => 0,
            ],
		],
		'l10n_diffsource' => [
			'config' => [
				'type' => 'passthrough',
			],
		],
		't3ver_label' => [
			'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'max' => 30
			]
		],
		'crdate' => [
			'exclude' => true,
			'label' => '',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'int'
			],
		],
		'tstamp' => [
			'exclude' => true,
			'label' => '',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'int'
			],
		],
		'deleted' => [
			'exclude' => true,
			'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.deleted',
			'config' => [
				'type' => 'check',
			],
		],
		'hidden' => [
			'exclude' => true,
			'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
			'config' => [
				'type' => 'check',
			],
		],
		'starttime' => [
			'exclude' => true,
			'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
			'config' => [
				'type' => 'input',
				'renderType' => 'inputDateTime',
				'eval' => 'datetime,int',
				'default' => 0,
				'behaviour' => [
					'allowLanguageSynchronization' => true,
				]
			]
		],
		'endtime' => [
			'exclude' => true,
			'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
			'config' => [
				'type' => 'input',
				'renderType' => 'inputDateTime',
				'eval' => 'datetime,int',
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
			'exclude' => true,
			'label' => '',
			'config' => [
				'type' => 'user',
				'userFunc' => 'Aix\Dataviewer\UserFunc\Logo->displayLogoText',
			],
		],
		'type' => [
			'exclude' => true,
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
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.5', 5], // GET/POST Variable
					//['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.6', 6], // Fixed Record Field
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.7', 7], // Database Value
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.8', 8], // Frontend User
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.9', 9], // SERVER Variable
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.10', 10], // Dynamic Record
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.11', 11], // User Session Variable
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.12', 12], // Page Id
                    ['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.13', 13], // User Func
                    ['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.14', 14], // Backend User
                    ['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.15', 15], // Language Id
                    ['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.16', 16], // Dataviewer Session Service Container
                    ['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.17', 17], // Extension Configuration
                    ['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.18', 18], // Extension Settings TypoScript
				],
				"default" => "0",
				'size' => 1,
				'maxitems' => 1,
				'eval' => '',
                'required' => true,
			],
		],
		'server' => [
			'exclude' => true,
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
            'exclude' => true,
            'label'   => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.variable_name',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'nospace,trim',
                'required' => true,
            ],
        ],
        'parameter_name' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.parameter_name',
            'description' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.parameter_name.description',
            'displayCond' => 'FIELD:type:IN:3,4,5',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'nospace,trim'
            ],
        ],
		'session_key' => [
			'exclude' => true,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.session_key',
			'displayCond' => 'FIELD:type:IN:11',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'nospace,trim'
			],
		],
		'variable_value' => [
			'exclude' => true,
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
			'exclude' => true,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.user_func',
			'displayCond' => 'FIELD:type:IN:13',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim',
                'required' => true,
				'placeholder' => 'VendorName\ExtensionName\UserFunc\YourUserFunc->userFuncMethod',
			],
		],
		'page' => [
			'exclude' => true,
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
        'ext_conf' => [
            'exclude' => true,
            'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.ext_conf',
            'onChange' => 'reload',
            'displayCond' => 'FIELD:type:IN:17',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'itemsProcFunc' => "Aix\\Dataviewer\\UserFunc\\Extension->populateLoadedExtensions",
                'size' => 1,
                'maxitems' => 1,
                'eval' => ''
            ],
        ],
        'typoscript_path' => [
            'exclude' => true,
            'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.typoscript_path',
            'description' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.typoscript_path.description',
            'displayCond' => 'FIELD:type:IN:18',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'nospace,trim',
                'required' => true,
            ],
        ],
		'table_content' => [
			'exclude' => true,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.table_content',
			'onChange' => 'reload',
			'displayCond' => 'FIELD:type:=:7',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'itemsProcFunc' => "Aix\\Dataviewer\\UserFunc\\Database->populateTablesAction",
				'size' => 1,
				'maxitems' => 1,
				'eval' => ''
			],
		],
		'column_name' => [
			'exclude' => true,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.column_name',
			'onChange' => 'reload',
			'displayCond' => 'FIELD:type:=:7',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectMultipleSideBySide',
				'enableMultiSelectFilterTextfield' => true,
				'itemsProcFunc' => "Aix\\Dataviewer\\UserFunc\\Database->populateColumnsAction",
				'size' => 3,
				'maxitems' => 999,
				'minitems' => 1,
				'eval' => ''
			],
		],
		'where_clause' => [
			'exclude' => true,
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
			'exclude' => true,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.type_cast',
			'displayCond' => 'FIELD:type:IN:3,4,5',
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
				'eval' => '',
                'required' => true,
			],
		],
        'allowed_values' => [
            'exclude' => true,
            'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.allowed_values',
            'displayCond' => 'FIELD:type:IN:3,4,5',
            'config' => [
                'type' => 'flex',
                'ds' => [
                    'default' => 'FILE:EXT:dataviewer/Configuration/FlexForms/Variable/AllowedValues.xml'
                ],
            ],
        ],
        'regex' => [
            'exclude' => true,
            'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.regex',
            'displayCond' => 'FIELD:type:IN:3,4,5',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 2
            ],
        ],
        'value_switch' => [
            'exclude' => true,
            'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.value_switch',
            'displayCond' => 'FIELD:type:IN:3,4,5',
            'config' => [
                'type' => 'flex',
                'ds' => [
                    'default' => 'FILE:EXT:dataviewer/Configuration/FlexForms/Variable/ValueSwitch.xml'
                ],
            ],
        ],
        'datatype' => [
            'exclude'     => true,
            'label'       => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype',
            'displayCond' => 'FIELD:type:IN:10',
            'config'      => [
                'type'                => 'select',
                'renderType'          => 'selectSingle',
                'multiple'            => 0,
                'maxitems'            => 1,
                'items' => [
                    ['', 0],
                ],
                'foreign_table'       => 'tx_dataviewer_domain_model_datatype',
                'foreign_table_where' => 'AND tx_dataviewer_domain_model_datatype.sys_language_uid IN (-1, 0)',
            ],
        ],
	],
];
