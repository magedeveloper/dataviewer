<?php
$extPath  = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('dataviewer');

return [
	'MageDeveloper\\Dataviewer\\Hooks\\ExtTablesInclusion' 		=> $extPath . 'Classes/Hooks/ExtTablesInclusion.php',
	'MageDeveloper\\Dataviewer\\RealUrl\\RealUrlConfiguration'	=> $extPath . 'Classes/RealUrl/RealUrlConfiguration.php',
	'MageDeveloper\\Dataviewer\\TypoScript\\UserFunc' 			=> $extPath . 'Classes/TypoScript/UserFunc.php',
];
