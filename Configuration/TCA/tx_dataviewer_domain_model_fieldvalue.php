<?php
defined('TYPO3') or die();

return [
    'ctrl' => [
        'title' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue',
        'label' => 'type',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'hideTable' => true,
        'sortby' => 'sorting',
        'versioningWS' => false,
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
        'searchFields' => 'type,value_content,image_content,file_content,table_content,column_name,where_clause,is_readonly,is_default,',
        'iconfile' => 'EXT:dataviewer/Resources/Public/Icons/Domain/Model/fieldvalue.svg',
	],
	'interface' => [
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, type, info, value_content, field_content, table_content, column_name, markers, where_clause, result, is_default, pretends_empty, pass_to_fe',
	],
	'types' => [
		'1' => [
			'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    type,
                --div--;LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:value_content,
                  	info, field_content, table_content, column_name, markers, value_content, where_clause, result, is_default, pretends_empty,
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
				'foreign_table' => 'tx_dataviewer_domain_model_fieldvalue',
				'foreign_table_where' => 'AND tx_dataviewer_domain_model_fieldvalue.pid=###CURRENT_PID### AND tx_dataviewer_domain_model_fieldvalue.sys_language_uid IN (-1,0)',
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
		'type' => [
			'exclude' => true,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.type',
			'onChange' => 'reload',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:type.10', 10], // Static Value(s)
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:type.20', 20], // Database Value(s)
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:type.30', 30], // TypoScript
					['LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:type.40', 40], // Field Content
				],
				//'itemsProcFunc' => "Aix\\Dataviewer\\UserFunc\\Fieldvalue->populateFieldvalueTypes",
				'default' => '10',
				'size' => 1,
				'maxitems' => 1,
				'eval' => '',
                'required' => true,
			],
		],
        'info' => [
            'exclude' => true,
            'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:flexform.available_markers',
            'config' => [
                'type' => 'user',
                'userFunc' => 'Aix\\Dataviewer\\UserFunc\\Template->displayAvailableMarkers',
                'parameters' => [
                    'template' => 'EXT:dataviewer/Resources/Private/Templates/UserFunc/Field/AvailableMarkers.html',
                    'includeRecord' => 0,
                    'markers' => [
                        'datatype' => [
                            'type' => 'Aix\\Dataviewer\\Domain\\Model\\Datatype',
                            'name' => 'datatype',
                            'description' => 'The according datatype',
                        ],
                        'field' => [
                            'type' => 'Aix\\Dataviewer\\Domain\\Model\\Field',
                            'name' => 'field',
                            'description' => 'The according field',
                        ],
                    ],
                ],
            ],
        ],
		'field_content' => [
			'exclude' => true,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.field_content',
			'displayCond' => 'FIELD:type:=:40',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'tx_dataviewer_domain_model_field',
				'foreign_table_where' => 'AND tx_dataviewer_domain_model_field.pid=###CURRENT_PID###',
				'size' => 1,
                'items' => [
                    ['', 0],
                ],
				'maxitems' => 1,
				'multiple' => 0,
                'default' => 0,
            ],
		],
		'table_content' => [
			'exclude' => true,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.table_content',
			'onChange' => 'reload',
			'displayCond' => 'FIELD:type:IN:20',
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
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.column_name',
			'displayCond' => 'FIELD:type:IN:20',
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
        'value_content' => [
            'exclude' => true,
            'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.value_content',
            'displayCond' => 'FIELD:type:IN:10,30,40',
            'config' => [
                'type' => 'text',
                'renderType' => 't3editor',
                'format' => 'html',
                'cols' => 40,
                'rows' => 10,
                'eval' => 'trim',
            ],
        ],
		'where_clause' => [
			'exclude' => true,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.where_clause',
			'displayCond' => 'FIELD:type:IN:20',
			'config' => [
				'type' => 'text',
				'cols' => 40,
				'rows' => 2,
				'eval' => 'trim',
				'placeholder' => 'x=\'y\' AND z=\'123\' ORDER BY z ASC',
			],
		],
		'result' => [
			'exclude' => true,
			'label' => '',
			'displayCond' => 'FIELD:type:IN:20',
			'config' => [
				'type' => 'user',
				'userFunc' => "Aix\\Dataviewer\\UserFunc\\Database->displayTableContentResult",
				'eval' => '',
			],
		],
		'is_readonly' => [
			'exclude' => true,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.is_readonly',
			'config' => [
				'type' => 'check',
				'default' => 0
			],
		],
		'is_default' => [
			'exclude' => true,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.is_default',
			'displayCond' => 'FIELD:type:IN:10,20,30,40',
			'config' => [
				'type' => 'check',
				'default' => 0
			],
		],
		'pass_to_fe' => [
			'exclude' => true,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.pass_to_fe',
			'config' => [
				'type' => 'check',
				'default' => 0
			],
		],
		'pretends_empty' => [
			'exclude' => true,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.pretends_empty',
			'displayCond' => 'FIELD:type:IN:10,20,30',
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
