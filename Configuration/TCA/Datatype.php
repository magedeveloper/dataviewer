<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TCA']['tx_dataviewer_domain_model_datatype'] = array(
		'ctrl' => $GLOBALS['TCA']['tx_dataviewer_domain_model_datatype']['ctrl'],
		'interface' => array(
				'showRecordFieldList' => 'logo, sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, description, templatefile, icon, color, hide_records, fields',
		),
		'types' => array(
				'1' => array('showitem' => 'logo, sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, name, description, templatefile, fields, --div--;LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:appearance, icon, color, hide_records, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'),
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
								'default' => 0,
								'showIconTable' => true,
						)
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
								'foreign_table' => 'tx_dataviewer_domain_model_datatype',
								'foreign_table_where' => 'AND tx_dataviewer_domain_model_datatype.pid=###CURRENT_PID### AND tx_dataviewer_domain_model_datatype.sys_language_uid IN (-1,0)',
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
				'logo' => array(
						'exclude' => 1,
						'label' => '',
						'config' => array(
								'type' => 'user',
								'userFunc' => 'MageDeveloper\Dataviewer\UserFunc\Logo->displayLogoText',
						),
				),
				'name' => array(
						'exclude' => 1,
						'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.name',
						'config' => array(
								'type' => 'input',
								'size' => 30,
								'eval' => 'trim,required'
						),
				),
				'description' => array(
						'exclude' => 1,
						'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.description',
						'config' => array(
								'type' => 'text',
								'cols' => 40,
								'rows' => 5,
								'eval' => 'trim',
						),
				),
				'icon' => array(
						'exclude' => 1,
						'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.icon',
						'config' => array(
								'type' => 'user',
								'userFunc' => 'MageDeveloper\\Dataviewer\\UserFunc\\Icon->displayIconSelection',
								'size' => 30,
								'eval' => ''
						),
				),
				'templatefile' => array(
						'exclude' => 1,
						'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.templatefile',
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
				'color' => array(
					'exclude' => 1,
					'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.color',
					'config' => array(
						'type' => 'input',
						'size' => 30,
						'eval' => 'trim',
						'wizards' => array(
							'colorChoice' => array(
								'type' => 'colorbox',
								'module' => array(
									'name' => 'wizard_colorpicker',
								),
								'JSopenParams' => 'height=600,width=500,status=0,menubar=0,scrollbars=1',
								'exampleImg' => 'EXT:dataviewer/Resources/Public/Images/color_wheel.png',
							)
						),
					),
				),
				'hide_records' => array(
					'exclude' => 1,
					'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.hide_records',
					'config' => array(
						'type' => 'check',
						'default' => 0,
					),
				),
				'fields' => array(
						'exclude' => 1,
						'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang_db.xlf:tx_dataviewer_domain_model_datatype.fields',
						'config' => array(
								'type' => 'select',
								'renderType' => 'selectMultipleSideBySide',
								'foreign_table' => 'tx_dataviewer_domain_model_field',
								'foreign_table_where' => 'AND tx_dataviewer_domain_model_field.pid=###CURRENT_PID###',
								'MM' => 'tx_dataviewer_datatype_field_mm',
								'size' => 10,
								'autoSizeMax' => 30,
								'iconsInOptionTags' => 1,
								'maxitems' => 9999,
								'multiple' => 1,
								'wizards' => array(
										'_PADDING' => 4,
										'_VERTICAL' => 1,
										'suggest' => array(
												'type' => 'suggest'
										),
										'edit' => array(
												'type' => 'popup',
												'title' => 'LLL:EXT:lang/locallang_core.xlf:cm.edit',
												'module' => array(
														'name' => 'wizard_edit',
												),
												'icon' => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_edit.gif',
												'popup_onlyOpenIfSelected' => 1,
												'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
										),
										'add' => Array(
												'type' => 'script',
												'title' => 'LLL:EXT:lang/locallang_core.xlf:cm.new',
												'icon' => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_add.gif',
												'params' => array(
														'table' => 'tx_dataviewer_domain_model_field',
														'pid' => '###CURRENT_PID###',
														'setValue' => 'prepend'
												),
												'module' => array(
														'name' => 'wizard_add'
												)
										),
								),
						),
				),
		),
);
