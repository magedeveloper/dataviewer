<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TCA']['tx_dataviewer_domain_model_field'] = array(
	'ctrl' => $GLOBALS['TCA']['tx_dataviewer_domain_model_field']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'logo, sys_language_uid, l10n_parent, l10n_diffsource, hidden, type, field_conf, tab_name, frontend_label, variable_name, id, templatefile, description, css_class, column_width, unit, is_record_title, show_title, field_values, validation, display_cond, request_update, field_ids',
	),
	'types' => array(
		'1' => array('showitem' => 'logo, sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, type, field_conf, is_record_title, --div--;LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:frontend_settings, frontend_label, variable_name, id, templatefile, --div--;LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:backend_settings, tab_name, show_title, description, column_width, css_class, --div--;LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.field_values, field_values, unit, --div--;LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:flexform.validation, validation, --div--;LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:flexform.display_cond, field_ids, display_cond, request_update, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(
		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0)
				),
			),
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_dataviewer_domain_model_field',
				'foreign_table_where' => 'AND tx_dataviewer_domain_model_field.pid=###CURRENT_PID### AND tx_dataviewer_domain_model_field.sys_language_uid IN (-1,0)',
			),
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),
		't3ver_label' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			)
		),
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),
		'tstamp' => array(
			'exclude' => 1,
			'label' => '',
			'config' => array(
				'type' => 'none',
				'format' => 'datetime',
				'eval' => 'datetime',
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'endtime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'logo' => array(
			'exclude' => 1,
			'label' => '',
			'config' => array(
				'type' => 'user',
				'userFunc' => 'MageDeveloper\Dataviewer\UserFunc\Logo->displayLogoText',
			),
		),
		'id' => array(
			'exclude' => 1,
			'label' => '',
			'config' => array(
				'type' => 'user',
				'userFunc' => 'MageDeveloper\Dataviewer\UserFunc\Field->displayGeneratedFieldIdentifier',
			),
		),
		'type' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.type',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'itemsProcFunc' => "MageDeveloper\\Dataviewer\\UserFunc\\Fieldtype->populateFieldtypes",
				'size' => 1,
				'maxitems' => 1,
				'eval' => '', 
				'showIconTable' => true,
			),
		),
		'column_width' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.column_width',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => array(
					array('12 (100%)', 	"col-sm-12"),
					array('11', 		"col-sm-11"),
					array('10', 		"col-sm-10"),
					array('9 (75%)', 	"col-sm-9"),
					array('8 (66%)', 	"col-sm-8"),
					array('7', 			"col-sm-7"),
					array('6 (50%)', 	"col-sm-6"),
					array('5', 			"col-sm-5"),
					array('4 (33%)',	"col-sm-4"),
					array('3 (25%)',	"col-sm-3"),
					array('2', 			"col-sm-2"),
					array('1', 			"col-sm-1"),
				),
				'size' => 1,
				'maxitems' => 1,
				'eval' => ''
			),
		),
		'field_conf' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.field_configuration',
			'config' => array(
				'type' => 'flex',
				'ds_pointerField' => 'type',
				'ds' => \MageDeveloper\Dataviewer\Utility\FieldtypeConfigurationUtility::getDsConfig(),
			),
		),
		'tab_name' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.tab_name',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'frontend_label' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.frontend_label',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'variable_name' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.variable_name',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,lower,alpha'
			),
		),
		'is_active' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.is_active',
			'config' => array(
				'type' => 'check',
				'default' => 1,
			),
		),
		'description' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.description',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 5,
				'eval' => 'trim',				
			),
		),
		'css_class' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.css_class',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim',
				'placeholder' => 'callout-info',
			),
		),
		'unit' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.unit',
			'displayCond' => 'FIELD:type:!IN:IMAGE,FILE,PAGE',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'show_title' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.show_title',
			'config' => array(
				'type' => 'check',
				'default' => 1
			)
		),
		'is_record_title' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.is_record_title',
			'config' => array(
				'type' => 'check',
				'default' => 0
			)
		),
		'validation' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.validation',
			'config' => array(
				'type' => 'flex',
				'ds' => array(
					'default' => 'FILE:EXT:dataviewer/Configuration/FlexForms/Field/Validation.xml'
				),
			),
		),
		'field_ids' => array(
			'exclude' => 1,
			'label' => '',
			'config' => array(
				'type' => 'user',
				'userFunc' => 'MageDeveloper\Dataviewer\UserFunc\Field->displayAvailableFieldIds',
			),
		),
		'display_cond' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.display_cond',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 10,
				'eval' => 'trim',
				'renderType' => 't3editor',
				'format' => 'xml',
			),
		),
		'request_update' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.request_update',
			'config' => array(
				'type' => 'check',
				'default' => 0
			)
		),
		'field_values' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.field_values',
			'config' => array(
				'type' => 'inline',
				'foreign_table' => 'tx_dataviewer_domain_model_fieldvalue',
				'foreign_field' => 'field',
				'foreign_sortby' => 'sorting',
				'maxitems'      => 9999,
				'appearance' => array(
					'collapseAll' => 1,
					'levelLinksPosition' => 'top',
					'showSynchronizationLink' => 1,
					'showPossibleLocalizationRecords' => 1,
					'useSortable' => 1,
					'showAllLocalizationLink' => 1
				),
			),

		),
		'templatefile' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_field.templatefile',
			'config' => array(
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => 'html',
				'max_size' => 2000,
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			),
		),
	),
);
