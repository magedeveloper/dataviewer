<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TCA']['tx_dataviewer_domain_model_variable'] = array(
	'ctrl' => $GLOBALS['TCA']['tx_dataviewer_domain_model_variable']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'logo, sys_language_uid, l10n_parent, l10n_diffsource, hidden, type, variable_name, variable_value, record, field, table_content, column_name, where_clause',
	),
	'types' => array(
		'1' => array('showitem' => 'logo, sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, type, variable_name, variable_value, record, field, table_content, column_name, where_clause, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'),
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
				'foreign_table' => 'tx_dataviewer_domain_model_variable',
				'foreign_table_where' => 'AND tx_dataviewer_domain_model_variable.pid=###CURRENT_PID### AND tx_dataviewer_domain_model_variable.sys_language_uid IN (-1,0)',
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
		'deleted' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.deleted',
			'config' => array(
				'type' => 'check',
			),
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
		'logo' => array(
			'exclude' => 1,
			'label' => '',
			'config' => array(
				'type' => 'user',
				'userFunc' => 'MageDeveloper\Dataviewer\UserFunc\Logo->displayLogoText',
			),
		),
		'type' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.type',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => array(
					array('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.0', 0), // Fixed Value
					array('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.1', 1), // TypoScript Value
					//array('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.2', 2), // TypoScript Variable Name
					array('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.3', 3), // GET Variable
					array('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.4', 4), // POST Variable
					array('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.5', 5), // Record
					array('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.6', 6), // Record Field
					array('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.7', 7), // Database Value
					array('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:variable_type.8', 8), // Frontend User
				),
				"default" => "0",
				'size' => 1,
				'maxitems' => 1,
				'eval' => 'required'
			),
		),
		'variable_name' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.variable_name',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'alpha,nospace,required,trim'
			),
		),
		'variable_value' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.variable_value',
			'displayCond' => 'FIELD:type:IN:0,1',
			'config' => array(
				'type' => 'text',
				'renderType' => 't3editor',
				'format' => 'typoscript',
				'cols' => 40,
				'rows' => 10,
				'eval' => 'trim'
			)
		),
		'record' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.record',
			'displayCond' => 'FIELD:type:IN:5,6',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'tx_dataviewer_domain_model_record',
				'foreign_table_where' => 'AND tx_dataviewer_domain_model_record.pid=###CURRENT_PID###',
				'size' => 1,
				'maxitems' => 1,
				'multiple' => 0,
			),
		),
		'field' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.field',
			'displayCond' => 'FIELD:type:=:6',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'tx_dataviewer_domain_model_field',
				'foreign_table_where' => 'AND tx_dataviewer_domain_model_field.pid=###CURRENT_PID###',
				'size' => 1,
				'maxitems' => 1,
				'multiple' => 0,
			),
		),
		'table_content' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.table_content',
			'displayCond' => 'FIELD:type:=:7',
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
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.column_name',
			'displayCond' => 'FIELD:type:=:7',
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
			'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_variable.where_clause',
			'displayCond' => 'FIELD:type:=:7',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 2,
				'eval' => 'trim',
				'placeholder' => 'x=\'y\' AND z=\'123\' ORDER BY z ASC',
			)
		),
	),
);
