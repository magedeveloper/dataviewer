<?php
defined('TYPO3') or die();

/* Retrieve typeicon classes */
$typeIcons = function () {
    try {
        $dvIconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Aix\Dataviewer\Icon\DataviewerIconRegistry::class);
        return $dvIconRegistry->getDatatypeTypeiconClasses();
    } catch (\Exception $e) {
        return [];
    }
};

return [
    'ctrl' => [
        'title' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'sortby' => 'sorting',
        'versioningWS' => false,
        //"languageField" => "sys_language_uid",
        //"transOrigPointerField" => "l10n_parent",
        //"transOrigDiffSourceField" => "l10n_diffsource",
        'delete' => 'deleted',
        'typeicon_column' => 'icon',
        'typeicon_classes' => $typeIcons(),
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
        'searchFields' => 'name,description,icon,fields,',
        'iconfile' => 'EXT:dataviewer/Resources/Public/Icons/Domain/Model/datatype.svg',
    ],
	'interface' => [
		'showRecordFieldList' => 'logo, hidden,name,tablename,class,description,icon,color,title_divider,hide_records,hide_add,fields,tab_config,disable_general_tab,enable_seo,cache_tca',
	],
	'types' => [
		'1' => [
			'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    logo, name, description, tablename, class,
                --div--;LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:tx_dataviewer_domain_model_datatype.fields,
                    fields,
                --div--;LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:tx_dataviewer_domain_model_datatype.tab_config,
                    disable_general_tab, tab_config,
                --div--;LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:appearance,
                  	icon, color, title_divider, thumbnail_field, hide_records, hide_add, enable_seo,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden,--palette--;;timeRestriction,
                --div--;LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:advanced_settings,
                    cache_tca
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
				'foreign_table' => 'tx_dataviewer_domain_model_datatype',
				'foreign_table_where' => 'AND tx_dataviewer_domain_model_datatype.pid=###CURRENT_PID### AND tx_dataviewer_domain_model_datatype.sys_language_uid IN (-1,0)',
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
		'logo' => [
			'exclude' => true,
			'label' => '',
			'config' => [
				'type' => 'user',
				'userFunc' => 'Aix\Dataviewer\UserFunc\Logo->displayLogoText',
			],
		],
		'name' => [
			'exclude' => true,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.name',
			'config' => [
				'type' => 'input',
				'size' => 30,
                'required' => true,
				'eval' => 'trim'
			],
		],
        'tablename' => [
            'exclude' => true,
            'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf.tx_dataviewer_domain_model_datatype.tablename',
            'config' => [
                'type' => 'user',
                'userFunc' => 'Aix\\Dataviewer\\UserFunc\\Datatype->getTableNameField',
                'size' => 30,
                'eval' => '',
                'readOnly' => 1,
            ],
        ],
        'class' => [
            'exclude' => true,
            'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf.tx_dataviewer_domain_model_datatype.class',
            'config' => [
                'type' => 'user',
                'userFunc' => 'Aix\\Dataviewer\\UserFunc\\Datatype->getClassField',
                'size' => 30,
                'eval' => ''
            ],
        ],
		'description' => [
			'exclude' => true,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.description',
			'config' => [
				'type' => 'text',
				'cols' => 40,
				'rows' => 2,
				'eval' => 'trim',
			],
		],
		'icon' => [
			'exclude' => true,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.icon',
			'config' => [
				'type' => 'user',
				'userFunc' => 'Aix\\Dataviewer\\UserFunc\\Icon->displayIconSelection',
				'size' => 30,
				'eval' => ''
			],
		],
		'color' => [
			'exclude' => true,
            'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.color',
			'config' => [
				'type' => 'input',
				'renderType' => 'colorpicker',
				'size' => 30,
				'eval' => 'trim',
			],
		],
		'title_divider' => [
			'exclude' => true,
            'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.title_divider',
            'description' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.title_divider.description',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['(space)',				"(SPACE)"],
					['.', 					"."],
					[',', 					","],
					[',(space)',			",(SPACE)"],
					['_', 					"_"],
					['-', 					"-"],
					['=', 					"="],
					['(space)-(space)',		"(SPACE)-(SPACE)"],
					[';', 					";"],
					[';(space)', 			";(SPACE)"],
					['+', 					"+"],
					['(space)+(space)', 	"(SPACE)+(SPACE)"],
					['>', 					">"],
					['(space)>(space)', 	"(SPACE)>(SPACE)"],
					['*', 					"*"],
					['(space)*(space)',		"(SPACE)*(SPACE)"],
					['~', 					"~"],
					['(space)~(space)', 	"(SPACE)~(SPACE)"],
					['->', 					"->"],
					['(space)->(space)',	"(SPACE)->(SPACE)"],
					['=>', 					"=>"],
					['(space)=>(space)',	"(SPACE)=>(SPACE)"],
					[':', 					":"],
					[':(space)', 			":(SPACE)"],
					['::', 					"::"],
					['(space)::(space)', 	"(SPACE)::(SPACE)"],
					['/', 					"/"],
					['(space)/(space)',		"(SPACE)/(SPACE)"],
				],
				'size' => 1,
				'maxitems' => 1,
				'eval' => ''
			],
		],
		'hide_records' => [
			'exclude' => true,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.hide_records',
			'config' => [
				'type' => 'check',
				'default' => 0,
			],
		],
        'hide_add' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.hide_add',
            'config'  => [
                'type'    => 'check',
                'default' => 0,
            ],
        ],
        'enable_seo' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.enable_seo',
            'config'  => [
                'type'    => 'check',
                'default' => 1,
            ],
        ],
        'fields' => [
            'exclude' => true,
            'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.fields',
            'onChange' => 'reload',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'enableMultiSelectFilterTextfield' => true,
                'foreign_table' => 'tx_dataviewer_domain_model_field',
                'foreign_table_where' => 'AND tx_dataviewer_domain_model_field.pid=###CURRENT_PID### AND tx_dataviewer_domain_model_field.sys_language_uid IN (-1, 0)',
                'MM' => 'tx_dataviewer_datatype_field_mm',
                'size' => 10,
                'autoSizeMax' => 30,
                'maxitems' => 9999,
                'multiple' => 0,
                'fieldControl' => [
                    'editPopup' => [
                        'disabled' => false,
                    ],
                    'addRecord' => [
                        'disabled' => false,
                    ],
                    'listModule' => [
                        'disabled' => false,
                    ],
                ],
            ],
        ],
		'tab_config' => [
			'exclude' => true,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.tab_config',
			'config' => [
				'type' => 'flex',
				'ds' => [
					'default' => 'FILE:EXT:dataviewer/Configuration/FlexForms/Datatype/TabConfig.xml'
				],
			],
		],
        'disable_general_tab' => [
            'exclude' => true,
            'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.disable_general_tab',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
        'thumbnail_field' => [
            'exclude' => true,
            'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.thumbnail_field',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_dataviewer_domain_model_field',
                'foreign_table_where' => 'AND tx_dataviewer_domain_model_field.pid=###CURRENT_PID### AND tx_dataviewer_domain_model_field.type IN (\'image\',\'inline\',\'relation\') AND tx_dataviewer_domain_model_field.sys_language_uid IN (-1, 0)',
                'size' => 1,
                'minitems' => 0,
                'default' => 0,
            ],
        ],
        'cache_tca' => [
            'exclude' => true,
            'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.cache_tca',
            'config' => [
                'type' => 'check',
                'default' => 1,
            ],
        ],
	],
];

