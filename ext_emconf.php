<?php
$EM_CONF[$_EXTKEY] = [
	'title' => 'Dataviewer Core',
	'description' => 'Build easy and intuitive TCA-records (e.g. News, Jobs, Events and more) on the fly without developing a new extension. Only templating is needed; includes many plugins for all kinds of usage!',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '3.0.0',
	'dependencies' => 'cms,extbase,fluid',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Bastian Zagar',
	'author_email' => 'kontakt@aixdesign.net',
	'author_company' => 'aixdesign.net',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => [
		'depends' => [
			'typo3' => '12.4.0-12.4.99',
			'extbase' => '',
			'fluid' => '',
		],
		'conflicts' => [
		],
		'suggests' => [
		],
	],
	'_md5_values_when_last_written' => 'a:0:{}',
	'suggests' => [
	],
    'autoload' => [
        'classmap' => [
            'Classes',
            'ext_autoload.php',
        ]
    ]
];
