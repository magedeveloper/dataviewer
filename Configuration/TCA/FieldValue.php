<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TCA']['tx_dataviewer_domain_model_fieldvalue'] = array(
	'ctrl' => $GLOBALS['TCA']['tx_dataviewer_domain_model_fieldvalue']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, type, value_content, field_content, table_content, column_name, where_clause, result, is_default, pretends_empty, pass_to_fe',
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, type, --div--;LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:value_content, value_content, field_content, table_content, column_name, where_clause, result, is_default, pretends_empty, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'),
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
				'foreign_table' => 'tx_dataviewer_domain_model_fieldvalue',
				'foreign_table_where' => 'AND tx_dataviewer_domain_model_fieldvalue.pid=###CURRENT_PID### AND tx_dataviewer_domain_model_fieldvalue.sys_language_uid IN (-1,0)',
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
		'type' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.type',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => array(
					array('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:type.0', 0), // Fixed Value(s)
					array('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:type.1', 1), // Database Value(s)
					array('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:type.2', 2), // TypoScript
					array('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:type.3', 3), // Field Content
				),
				"default" => "0",
				'size' => 1,
				'maxitems' => 1,
				'eval' => 'required'
			),
		),
		'field_content' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.field_content',
			'displayCond' => 'FIELD:type:=:3',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'tx_dataviewer_domain_model_field',
				'foreign_table_where' => 'AND tx_dataviewer_domain_model_field.pid=###CURRENT_PID###',
				'size' => 1,
                'items' => array(
                    array('------------------', 0),
                ),
				'maxitems' => 1,
				'multiple' => 0,
			),
		),
		'value_content' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.value_content',
			'displayCond' => 'FIELD:type:IN:0,2',
			'config' => array(
				'type' => 'text',
				'renderType' => 't3editor',
				'format' => 'typoscript',
				'cols' => 40,
				'rows' => 10,
				'eval' => 'trim'
			)
		),
		'table_content' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.table_content',
			'displayCond' => 'FIELD:type:IN:1',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'itemsProcFunc' => "MageDeveloper\\Dataviewer\\UserFunc\\Database->populateTablesAction",
				'size' => 1,
				'maxitems' => 1,
				'eval' => ''
			),
		),
		'column_name' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.column_name',
			'displayCond' => 'FIELD:type:IN:1',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectMultipleSideBySide',
				'itemsProcFunc' => "MageDeveloper\\Dataviewer\\UserFunc\\Database->populateColumnsAction",
				'size' => 3,
				'maxitems' => 999,
				'minitems' => 1,
				'eval' => ''
			),
		),
		'where_clause' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.where_clause',
			'displayCond' => 'FIELD:type:IN:1',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 2,
				'eval' => 'trim',
				'placeholder' => 'x=\'y\' AND z=\'123\' ORDER BY z ASC',
			)
		),
		'result' => array(
			'exclude' => 1,
			'label' => '',
			'displayCond' => 'FIELD:type:IN:1',
			'config' => array(
				'type' => 'user',
				'userFunc' => "MageDeveloper\\Dataviewer\\UserFunc\\Database->displayTableContentResult",
				'eval' => ''
			),
		),
		'is_readonly' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.is_readonly',
			'config' => array(
				'type' => 'check',
				'default' => 0
			)
		),
		'is_default' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.is_default',
			'displayCond' => 'FIELD:type:IN:0,1,2,3',
			'config' => array(
				'type' => 'check',
				'default' => 0
			)
		),
		'pass_to_fe' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.pass_to_fe',
			'config' => array(
				'type' => 'check',
				'default' => 0
			)
		),
		'pretends_empty' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_fieldvalue.pretends_empty',
			'displayCond' => 'FIELD:type:IN:0,1,2',
			'config' => array(
				'type' => 'check',
				'default' => 0
			)
		),
		'field' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),
	),
);
