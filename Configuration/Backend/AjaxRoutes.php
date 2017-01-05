<?php
return [
	'dataviewer_list' => [
		'path' => '/dataviewer/list',
		'target' => \MageDeveloper\Dataviewer\Hooks\ToolbarItem::class . '::menuAction'
	],
	'dataviewer_remove' => [
		'path' => '/dataviewer/remove',
		'target' => \MageDeveloper\Dataviewer\Hooks\ToolbarItem::class . '::removeRecordAction'
	],
	'dataviewer_hideshow' => [
		'path' => '/dataviewer/hideshow',
		'target' => \MageDeveloper\Dataviewer\Hooks\ToolbarItem::class . '::hideShowRecordAction'
	]
];
