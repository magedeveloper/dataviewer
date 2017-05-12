<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

return [
	"ctrl" => [
		"title"	=> "LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype",
		"label" => "name",
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"dividers2tabs" => TRUE,
		"sortby" => "sorting",
		"versioningWS" => true,
		"languageField" => "sys_language_uid",
		"transOrigPointerField" => "l10n_parent",
		"transOrigDiffSourceField" => "l10n_diffsource",
		"delete" => "deleted",
		"typeicon_column" => "icon",
		"typeicon_classes" => \MageDeveloper\Dataviewer\Utility\IconUtility::getClasses(),
		"enablecolumns" => [
			"disabled" => "hidden",
			"starttime" => "starttime",
			"endtime" => "endtime",
		],
		"searchFields" => "name,description,icon,templatefile,fields,",
		"iconfile" => "EXT:dataviewer/Resources/Public/Icons/Domain/Model/Datatype.gif"
	],
	'interface' => [
		'showRecordFieldList' => 'logo, sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, description, templatefile, icon, color, title_divider, hide_records, hide_add, fields, tab_config',
	],
	'types' => [
		'1' => [
			'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    logo, name, description, templatefile, fields, tab_config,
                --div--;LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:appearance,
                  	icon, color, title_divider, hide_records, hide_add,
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
				'default' => 0,
				'fieldWizard' => [
					'selectIcons' => [
						'disabled' => false,
					],
				],
			]
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
				'foreign_table' => 'tx_dataviewer_domain_model_datatype',
				'foreign_table_where' => 'AND tx_dataviewer_domain_model_datatype.pid=###CURRENT_PID### AND tx_dataviewer_domain_model_datatype.sys_language_uid IN (-1,0)',
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
		'name' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.name',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			],
		],
		'description' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.description',
			'config' => [
				'type' => 'text',
				'cols' => 40,
				'rows' => 5,
				'eval' => 'trim',
			],
		],
		'icon' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.icon',
			'config' => [
				'type' => 'user',
				'userFunc' => 'MageDeveloper\\Dataviewer\\UserFunc\\Icon->displayIconSelection',
				'size' => 30,
				'eval' => ''
			],
		],
		'templatefile' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.templatefile',
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
		'color' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.color',
			'config' => [
				'type' => 'input',
				'renderType' => 'colorpicker',
				'size' => 30,
				'eval' => 'trim',
			],
		],
		'title_divider' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.title_divider',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['(space)',				"X"],
					['.', 					"."],
					[',', 					","],
					[',(space)',			",X"],
					['_', 					"_"],
					['-', 					"-"],
					['=', 					"="],
					['(space)-(space)',		"X-X"],
					[';', 					";"],
					[';(space)', 			";X"],
					['+', 					"+"],
					['(space)+(space)', 	"X+X"],
					['>', 					">"],
					['(space)>(space)', 	"X>X"],
					['*', 					"*"],
					['(space)*(space)',		"X*X"],
					['~', 					"~"],
					['(space)~(space)', 	"X~X"],
					['->', 					"->"],
					['(space)->(space)',	"X->X"],
					['=>', 					"=>"],
					['(space)=>(space)',	"X=>X"],
					[':', 					":"],
					[':(space)', 			":X"],
					['::', 					"::"],
					['(space)::(space)', 	"X::X"],
					['/', 					"/"],
					['(space)/(space)',		"X/X"],
				],
				'size' => 1,
				'maxitems' => 1,
				'eval' => ''
			],
		],
		'hide_records' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.hide_records',
			'config' => [
				'type' => 'check',
				'default' => 0,
			],
		],
		'hide_add' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.hide_add',
			'config' => [
				'type' => 'check',
				'default' => 0,
			],
		],
		'fields' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.fields',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectMultipleSideBySide',
				'foreign_table' => 'tx_dataviewer_domain_model_field',
				'foreign_table_where' => 'AND tx_dataviewer_domain_model_field.pid=###CURRENT_PID###',
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
				'wizards' => [
					'_PADDING' => 4,
					'_VERTICAL' => 1,
					'suggest' => [
						'type' => 'suggest'
					],
					'edit' => [
						'type' => 'popup',
						'title' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:cm.edit',
						'module' => [
							'name' => 'wizard_edit',
						],
						'icon' => 'actions-open',
						'popup_onlyOpenIfSelected' => 1,
						'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
					],
					'add' => [
						'type' => 'script',
						'title' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:cm.new',
						'icon' => 'actions-add',
						'params' => [
							'table' => 'tx_dataviewer_domain_model_field',
							'pid' => '###CURRENT_PID###',
							'setValue' => 'prepend'
						],
						'module' => [
							'name' => 'wizard_add'
						],
					],
				],
			],
		],
		'tab_config' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.tab_config',
			'config' => [
				'type' => 'flex',
				'ds' => [
					'default' => 'FILE:EXT:dataviewer/Configuration/FlexForms/Datatype/TabConfig.xml'
				],
			],
		],
	],
];

