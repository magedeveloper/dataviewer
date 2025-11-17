<?php
defined('TYPO3') or die();

/* Retrieve typeicon classes */
$typeIcons = function () {
    try {
        $dvIconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Aix\Dataviewer\Icon\DataviewerIconRegistry::class);
        return $dvIconRegistry->getFieldTypeIconClasses();
    } catch (\Exception $e) {
        return [];
    }
};

$palettes = function() {
    $palettes = [];
    try {
        // We need to ignore exceptions here in case the table does not exist
        /* @var \TYPO3\CMS\Core\Database\Connection $query */
        $query = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)
            ->getConnectionForTable("tx_dataviewer_domain_model_field");

        $fields = $query->select(['palette'], 'tx_dataviewer_domain_model_field',[],['palette'])->fetchAll();

        foreach($fields as $_field) {
            if($_field['palette'] != '') {
                $palettes[] = $_field['palette'];
            }
        }

    } catch (\Exception $e) {}

    array_unique($palettes);
    foreach($palettes as $_i=>$_p) {
        $palettes[$_i] = [$_p,$_p];
    }
    return $palettes;
};

/* Retrieve frontend types */
$frontendTypes = function () {

    // label, value
    $types = [
        ['string', 'string'],
        ['boolean', 'bool'],
        ['integer', 'int'],
        ['float', 'float'],
        ['\\DateTime', '\\DateTime'],
        ['\\StdClass', '\\StdClass'],
        ['\\TYPO3\\CMS\\Extbase\\Domain\\Model\\File', '\\TYPO3\\CMS\\Extbase\\Domain\\Model\\File'],
        ['\\TYPO3\\CMS\\Extbase\\Domain\\Model\\FileReference', '\\TYPO3\\CMS\\Extbase\\Domain\\Model\\FileReference'],
        ['\\TYPO3\\CMS\\Extbase\\Domain\\Model\\Folder', '\\TYPO3\\CMS\\Extbase\\Domain\\Model\\Folder'],
        ['\\TYPO3\\CMS\\Extbase\\Domain\\Model\\BackendUser', '\\TYPO3\\CMS\\Extbase\\Domain\\Model\\BackendUser'],
        ['\\TYPO3\\CMS\\Extbase\\Domain\\Model\\BackendUserGroup', '\\TYPO3\\CMS\\Extbase\\Domain\\Model\\BackendUserGroup'],
        ['\\TYPO3\\CMS\\Extbase\\Domain\\Model\\FrontendUser', '\\TYPO3\\CMS\\Extbase\\Domain\\Model\\FrontendUser'],
        ['\\TYPO3\\CMS\\Extbase\\Domain\\Model\\FrontendUserGroup', '\\TYPO3\\CMS\\Extbase\\Domain\\Model\\FrontendUserGroup'],
        ['\\TYPO3\\CMS\\Extbase\\Domain\\Model\\Category', '\\TYPO3\\CMS\\Extbase\\Domain\\Model\\Category'],
    ];

    try {
        // We need to ignore exceptions here in case the table does not exist
        /* @var \TYPO3\CMS\Core\Database\Connection $query */
        $query = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)
            ->getConnectionForTable("tx_dataviewer_domain_model_datatype");

        $datatypes = $query->select(['name'], 'tx_dataviewer_domain_model_datatype')->fetchAll();

        foreach ($datatypes as $_datatype) {
            $className =  function($datatypeName) {
                $parts = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(' ', $datatypeName);
                return '\\Aix\\Dataviewer\\Domain\\Model\\Record\\'.implode('\\', $parts);
            };

            $fullyQualifiedClassName = $className($_datatype['name']);
            if (class_exists($fullyQualifiedClassName)) {
                $types[] = [
                    $fullyQualifiedClassName,
                    $fullyQualifiedClassName
                ];
            }
        }

    } catch (\Exception $e) {}

    return $types;
};


return [
	"ctrl" => [
		"title"	=> "LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field",
		"label" => "frontend_label",
        "label_userFunc" => "Aix\\Dataviewer\\LabelUserFunc\\Field->displayLabel",
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"dividers2tabs" => TRUE,
		"default_sortby" => "ORDER BY uid DESC",
        "hideTable" => false,
		//"sortby" => "sorting",
		"versioningWS" => false,
		"typeicon_column" => "type",
		"typeicon_classes" => $typeIcons(),
		"languageField" => "sys_language_uid",
		"transOrigPointerField" => "l10n_parent",
		"transOrigDiffSourceField" => "l10n_diffsource",
		"delete" => "deleted",
		"enablecolumns" => [
			"disabled" => "hidden",
			"starttime" => "starttime",
			"endtime" => "endtime",
		],
        "security" => [
            "ignorePageTypeRestriction" => true,
        ],
		"searchFields" => "id,type,frontend_label,css_class,unit,is_required,show_description,field_values,",
        "iconfile" => "EXT:dataviewer/Resources/Public/Icons/Domain/Model/field.svg",
	],
	'interface' => [
		'showRecordFieldList' => 'logo, sys_language_uid, l10n_parent, l10n_diffsource, hidden, exclude, type, field_conf, frontend_label, variable_name, id, is_record_title, palette, description, database_type, is_index, field_values, validation, request_update, display_cond, frontend_type, is_object_storage, backend_searchable,cache_tca',
	],
	'types' => [
		'1' => [
			'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    logo, type, field_conf,
                --div--;LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:frontend_settings,
                  	--palette--;;label, frontend_type, is_object_storage,
                --div--;LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:backend_settings,
                  --palette--;;titles, backend_searchable, --palette--;;excludings, palette, description,
                --div--;LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:database_settings,
                  database_type, is_index,
                --div--;LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.field_values,
                  	field_values,
                --div--;LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:flexform.validation,
                	validation,
                --div--;LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:flexform.display_cond,
                	 request_update, --palette--;;displaycond,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                    --palette--;;language,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden,--palette--;;timeRestriction,
                --div--;LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:advanced_settings,
                    cache_tca
            ',
		],
	],
	'palettes' => [
		'timeRestriction' => ['showitem' => 'starttime, endtime'],
		'label' => ['showitem' => 'frontend_label, variable_name, --linebreak--, id'],
		'language' => ['showitem' => 'sys_language_uid, l10n_parent'],
		'titles' => ['showitem' => 'is_record_title, use_as_path_segment'],
		'displaycond' => ['showitem' => 'field_ids,--linebreak--,display_cond'],
        'excludings' => ['showitem' => 'exclude, l10n_exclude'],
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
                'foreign_table' => 'tx_dataviewer_domain_model_field',
                'foreign_table_where' => 'AND tx_dataviewer_domain_model_field.uid=###REC_FIELD_l10n_parent### AND tx_dataviewer_domain_model_field.sys_language_uid IN (-1,0)',
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
		'exclude' => [
			'exclude' => true,
            'l10n_mode' => 'exclude',
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.exclude',
			'config' => [
			    'default' => 1,
				'type' => 'check',
			],
		],
        'l10n_exclude' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.l10n_exclude',
            'config' => [
                'default' => 1,
                'type' => 'check',
            ],
        ],
		'tstamp' => [
			'exclude' => true,
			'label' => '',
			'config' => [
				'type' => 'none',
				'format' => 'datetime',
				'eval' => 'datetime,int',
			],
		],
		'starttime' => [
			'exclude' => true,
            'l10n_mode' => 'exclude',
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
            'l10n_mode' => 'exclude',
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
		'id' => [
			'exclude' => true,
			'label' => '',
			'config' => [
				'type' => 'user',
				'userFunc' => 'Aix\Dataviewer\UserFunc\Field->displayGeneratedFieldIdentifier',
			],
		],
		'type' => [
            'l10n_mode' => 'exclude',
			'exclude' => true,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.type',
			'onChange' => 'reload',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'itemsProcFunc' => "Aix\\Dataviewer\\UserFunc\\Field->populateFieldtypes",
				'items' => [
					['', 	""],
				],
				'size' => 1,
				'maxitems' => 1,
				'eval' => '',
                'required' => true,
				'fieldWizard' => [
					'selectIcons' => [
						'disabled' => false,
					],
				],
			],
		],
		'field_conf' => [
            'l10n_mode' => 'exclude',
			'exclude' => true,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.field_configuration',
			'config' => [
				'type' => 'flex',
				'ds_pointerField' => 'type',
                'ds' => [
                    'default' => 'FILE:EXT:dataviewer/Configuration/FlexForms/Field/Empty.xml',
                ],
			],
		],
		'frontend_label' => [
			'exclude' => true,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.frontend_label',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim',
                'required' => true,
			],
		],
        'backend_searchable' => [
            'l10n_mode' => 'exclude',
            'exclude' => true,
            'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.backend_searchable',
            'config' => [
                'type' => 'check',
                'default' => 0
            ],
        ],
		'variable_name' => [
            'l10n_mode' => 'exclude',
			'exclude' => true,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.variable_name',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,lower,alpha',
                'required' => true,
			],
		],
		'is_active' => [
			'exclude' => true,
            'l10n_mode' => 'exclude',
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.is_active',
			'config' => [
				'type' => 'check',
				'default' => 1,
			],
		],
        'is_record_title' => [
            'l10n_mode' => 'exclude',
            'exclude' => true,
            'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.is_record_title',
            'config' => [
                'type' => 'check',
                'default' => 0
            ],
        ],
        'use_as_path_segment' => [
            'l10n_mode' => 'exclude',
            'exclude' => true,
            'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.use_as_path_segment',
            'config' => [
                'type' => 'check',
                'default' => 0
            ],
        ],
        'palette' => [
            'l10n_mode' => 'exclude',
            'exclude' => true,
            'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.palette',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'valuePicker' => [
                    'items' => $palettes(),
                ],
                'eval' => '',
            ],
        ],
        'description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.description',
            'description' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:tx_dataviewer_domain_model_field.description.your_text_here',
            'config' => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 3,
            ],
        ],
		'field_ids' => [
			'exclude' => true,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:flexform.available_field_ids',
			'config' => [
				'type' => 'user',
				'userFunc' => 'Aix\Dataviewer\UserFunc\Field->displayAvailableFieldIds',
                'parameters' => [
                    'template' => "EXT:dataviewer/Resources/Private/Templates/UserFunc/Field/AvailableFieldIds.html",
                ],
			],
		],
		'display_cond' => [
			'exclude' => true,
            'l10n_mode' => 'exclude',
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
        'is_index' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.is_index',
            'config' => [
                'type' => 'check',
            ],
        ],
        'database_type' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.database_type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.database_type.default', '--div--'],
                    ['LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.database_type.inherit', ''],
                    ['LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.database_type.overrides', '--div--'],
                    ['varchar(255) DEFAULT \'\' NOT NULL', 'varchar(255) DEFAULT \'\' NOT NULL'],
                    ['tinyint(1) unsigned DEFAULT \'0\' NOT NULL', 'tinyint(1) unsigned DEFAULT \'0\' NOT NULL'],
                    ['int(11) unsigned DEFAULT \'0\' NOT NULL','int(11) unsigned DEFAULT \'0\' NOT NULL'],
                    ['mediumtext','mediumtext'],
                    ['text', 'text'],
                ],
                'size' => 1,
                'maxitems' => 1,
                'eval' => ''
            ],
        ],
        'frontend_type' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.frontend_type',
            'config' => [
                'type' => 'input',
                'valuePicker' => [
                    'items' => $frontendTypes(),
                ],
                'size' => 50,
                'placeholder' => 'string',
                'default' => 'string',
                'required' => true,
                'eval' => 'trim',
            ],
        ],
        'is_object_storage' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.is_object_storage',
            'description' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.is_object_storage.description',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
		'request_update' => [
            'l10n_mode' => 'exclude',
			'exclude' => true,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.request_update',
			'config' => [
				'type' => 'check',
				'default' => 0
			],
		],
        'field_values' => [
            'exclude' => true,
            'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.field_values',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_dataviewer_domain_model_fieldvalue',
                'foreign_field' => 'field',
                'foreign_table_where' => 'AND tx_dataviewer_domain_model_fieldvalue.pid=###CURRENT_PID### AND tx_dataviewer_domain_model_fieldvalue.sys_language_uid=###REC_FIELD_sys_language_uid###',
                'foreign_sortby' => 'sorting',
                'maxitems'      => 9999,
                'minitems'      => 1,
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
        'cache_tca' => [
            'exclude' => true,
            'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.cache_tca',
            'config' => [
                'type' => 'check',
            ],
        ],
	],
];
